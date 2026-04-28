<?php

namespace App\Http\Controllers\Gateway\Coinpayments;

use App\Models\Deposit;
use App\Http\Controllers\Controller;
use App\Http\Controllers\Gateway\Coinpayments\CoinPaymentHosted;
use App\Http\Controllers\Gateway\PaymentController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ProcessController extends Controller
{
    /*
     * CoinPaymentHosted Gateway
     */

    public static function process($deposit)
    {
        $coinPayAcc = self::coinPaymentsConfig($deposit);

        if (
            self::isInvalidCredential($coinPayAcc->public_key ?? '') ||
            self::isInvalidCredential($coinPayAcc->private_key ?? '') ||
            self::isInvalidCredential($coinPayAcc->merchant_id ?? '')
        ) {
            Log::warning('CoinPayments deposit blocked: API credentials are missing/invalid', [
                'trx' => $deposit->trx,
            ]);

            return json_encode([
                'error'   => true,
                'message' => 'CoinPayments credentials are missing or invalid. Admin must set Public Key, Private Key, Merchant ID, and IPN Secret in Automatic Gateways.',
            ]);
        }

        if ($deposit->btc_amo == 0 || $deposit->btc_wallet == "") {
            try {
                $cps = new CoinPaymentHosted();
            } catch (\Exception $e) {
                $send['error'] = true;
                $send['message'] = $e->getMessage();
                return json_encode($send);
            }

            $cps->Setup($coinPayAcc->private_key, $coinPayAcc->public_key);
            $callbackUrl = route('ipn.'.$deposit->gateway->alias);

            $req = array(
                'amount' => $deposit->final_amo,
                'currency1' => 'USD',
                'currency2' => $deposit->method_currency,
                'custom' => $deposit->trx,
                'buyer_email' => auth()->user()->email,
                'ipn_url' => $callbackUrl,
            );

            $result = $cps->CreateTransaction($req);
            if ($result['error'] == 'ok') {
                $bcoin = sprintf('%.08f', $result['result']['amount']);
                $sendadd = $result['result']['address'];
                $deposit['btc_amo'] = $bcoin;
                $deposit['btc_wallet'] = $sendadd;
                $deposit->update();
            } else {
                $apiError = (string) ($result['error'] ?? 'Unable to process payment');
                if (stripos($apiError, 'invalid api') !== false || stripos($apiError, 'invalid key') !== false) {
                    $apiError = 'CoinPayments credentials are invalid. Admin must verify Public Key and Private Key for this gateway.';
                }
                $send['error'] = true;
                $send['message'] = $apiError;
            }
        }

        $send['amount'] = $deposit->btc_amo;
        $send['sendto'] = $deposit->btc_wallet;
        $send['img'] = cryptoQR($deposit->btc_wallet);
        $send['currency'] = "$deposit->method_currency";
        $send['view'] = 'user.payment.crypto';
        return json_encode($send);
    }

    public function ipn(Request $request)
    {
        $track = (string) $request->input('custom', '');

        if ($track === '') {
            Log::warning('CoinPayments IPN rejected: missing transaction reference', [
                'ip' => $request->ip(),
            ]);
            return;
        }

        $deposit = Deposit::where('trx', $track)->orderBy('id', 'DESC')->first();

        if (!$deposit) {
            Log::warning('CoinPayments IPN rejected: deposit not found', [
                'trx' => $track,
                'ip'  => $request->ip(),
            ]);
            return;
        }

        if ((int) $deposit->status !== 0) {
            return;
        }

        $gatewayCurrency = $deposit->gatewayCurrency();

        if (!$gatewayCurrency) {
            Log::warning('CoinPayments IPN rejected: gateway currency missing', [
                'trx' => $track,
                'ip'  => $request->ip(),
            ]);
            return;
        }

        $coinPayAcc = self::coinPaymentsConfig($deposit);
        $ipnSecret  = trim((string) ($coinPayAcc->ipn_secret ?? ''));

        if ($ipnSecret === '') {
            Log::warning('CoinPayments IPN rejected: IPN secret is not configured', [
                'trx' => $track,
                'ip'  => $request->ip(),
            ]);
            return;
        }

        $ipnMode = strtolower((string) $request->input('ipn_mode', ''));

        if ($ipnMode !== 'hmac') {
            Log::warning('CoinPayments IPN rejected: invalid IPN mode', [
                'trx'      => $track,
                'ipn_mode' => $ipnMode,
                'ip'       => $request->ip(),
            ]);
            return;
        }

        $receivedHmac = trim((string) ($request->header('HMAC') ?: $request->server('HTTP_HMAC', '')));

        if ($receivedHmac === '') {
            Log::warning('CoinPayments IPN rejected: missing HMAC header', [
                'trx' => $track,
                'ip'  => $request->ip(),
            ]);
            return;
        }

        $calculatedHmac = hash_hmac('sha512', $request->getContent(), $ipnSecret);

        if (!hash_equals(strtolower($calculatedHmac), strtolower($receivedHmac))) {
            Log::warning('CoinPayments IPN rejected: HMAC verification failed', [
                'trx' => $track,
                'ip'  => $request->ip(),
            ]);
            return;
        }

        $merchant = (string) $request->input('merchant', '');

        if ((string) ($coinPayAcc->merchant_id ?? '') !== $merchant) {
            Log::warning('CoinPayments IPN rejected: merchant mismatch', [
                'trx' => $track,
                'ip'  => $request->ip(),
            ]);
            return;
        }

        $status = (int) $request->input('status', 0);

        if ($status < 100 && $status !== 2) {
            return;
        }

        $amount2   = (float) $request->input('amount2', 0);
        $currency2 = (string) $request->input('currency2', '');

        if ($deposit->method_currency !== $currency2 || (float) $deposit->btc_amo > $amount2) {
            Log::warning('CoinPayments IPN rejected: currency or amount mismatch', [
                'trx'             => $track,
                'expected_amount' => (float) $deposit->btc_amo,
                'received_amount' => $amount2,
                'expected_cur'    => $deposit->method_currency,
                'received_cur'    => $currency2,
                'ip'              => $request->ip(),
            ]);
            return;
        }

        PaymentController::userDataUpdate($deposit);
    }

    private static function coinPaymentsConfig(Deposit $deposit): object
    {
        $currencyParams = json_decode($deposit->gatewayCurrency()->gateway_parameter ?? '{}', true);
        $gatewayParams  = json_decode($deposit->gateway->gateway_parameters ?? '{}', true);

        if (!is_array($currencyParams)) {
            $currencyParams = [];
        }
        if (!is_array($gatewayParams)) {
            $gatewayParams = [];
        }

        return (object) [
            'public_key'  => self::getCredentialConfigValue('public_key', $currencyParams, $gatewayParams),
            'private_key' => self::getCredentialConfigValue('private_key', $currencyParams, $gatewayParams),
            'merchant_id' => self::getCredentialConfigValue('merchant_id', $currencyParams, $gatewayParams),
            'ipn_secret'  => self::getConfigValue('ipn_secret', $currencyParams, $gatewayParams),
        ];
    }

    private static function getConfigValue(string $key, array $currencyParams, array $gatewayParams): string
    {
        $gatewayValue = $gatewayParams[$key]['value'] ?? '';
        $gatewayValue = trim((string) $gatewayValue);
        if ($gatewayValue !== '') {
            return $gatewayValue;
        }

        return trim((string) ($currencyParams[$key] ?? ''));
    }

    private static function getCredentialConfigValue(string $key, array $currencyParams, array $gatewayParams): string
    {
        $gatewayValue = trim((string) ($gatewayParams[$key]['value'] ?? ''));
        $currencyValue = trim((string) ($currencyParams[$key] ?? ''));

        if (!self::isInvalidCredential($gatewayValue)) {
            return $gatewayValue;
        }

        if (!self::isInvalidCredential($currencyValue)) {
            return $currencyValue;
        }

        return $gatewayValue !== '' ? $gatewayValue : $currencyValue;
    }

    private static function isInvalidCredential(string $value): bool
    {
        $value = trim($value);
        if ($value === '') {
            return true;
        }

        if (preg_match('/^-+$/', $value)) {
            return true;
        }

        return strlen($value) < 8;
    }
}
