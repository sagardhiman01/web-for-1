<?php

namespace App\Http\Controllers\Gateway;

use App\Http\Controllers\Controller;
use App\Http\Controllers\User\UserController;
use App\Lib\FormProcessor;
use App\Lib\PlatformEngine;
use App\Models\AdminNotification;
use App\Models\Deposit;
use App\Models\GatewayCurrency;
use App\Models\GeneralSetting;
use App\Models\Plan;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function deposit()
    {
        $gatewayCurrency = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->with('method')->orderby('method_code')->get();
        $pageTitle = 'Deposit Methods';
        return view($this->activeTemplate . 'user.payment.deposit', compact('gatewayCurrency', 'pageTitle'));
    }

    public function depositInsert(Request $request)
    {
        $request->validate([
            'amount'      => 'required|numeric|gt:0',
            'method_code' => 'required',
            'currency'    => 'required',
            'wallet'      => 'required|in:deposit_wallet,nft_wallet',
        ]);


        $gate = GatewayCurrency::whereHas('method', function ($gate) {
            $gate->where('status', 1);
        })->where('method_code', $request->method_code)->where('currency', $request->currency)->first();
        if (!$gate) {
            $notify[] = ['error', 'Invalid gateway'];
            return back()->withNotify($notify);
        }

        if ($gate->min_amount > $request->amount || $gate->max_amount < $request->amount) {
            $notify[] = ['error', 'Please follow deposit limit'];
            return back()->withNotify($notify);
        }

        $data = self::insertDeposit($gate, $request->amount, null, $request->wallet);
        session()->put('Track', $data->trx);
        return to_route('user.deposit.confirm');
    }

    public static function insertDeposit($gateway, $amount, $investPlan = null, $wallet = 'deposit_wallet')
    {
        $user      = auth()->user();
        $charge    = $gateway->fixed_charge + ($amount * $gateway->percent_charge / 100);
        $payable   = $amount + $charge;
        $final_amo = $payable * $gateway->rate;

        $data = new Deposit();
        if ($investPlan) {
            $data->plan_id = $investPlan->id;
        }
        $data->user_id         = $user->id;
        $data->method_code     = $gateway->method_code;
        $data->method_currency = strtoupper($gateway->currency);
        $data->amount          = $amount;
        $data->charge          = $charge;
        $data->rate            = $gateway->rate;
        $data->final_amo       = $final_amo;
        $data->btc_amo         = 0;
        $data->btc_wallet      = "";
        $data->trx             = getTrx();
        $data->wallet_type      = $wallet;
        $data->save();

        return $data;
    }

    public static function userDataUpdate($deposit, $isManual = null)
    {
        if ($deposit->status == 0 || $deposit->status == 2) {
            $deposit->status = 1;
            $deposit->save();

            $user = User::where('id', $deposit->user_id)->lockForUpdate()->first();
            $wallet = $deposit->wallet_type ?? 'deposit_wallet';
            $user->$wallet += $deposit->amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $deposit->user_id;
            $transaction->amount       = $deposit->amount;
            $transaction->post_balance = $user->$wallet;
            $transaction->charge       = $deposit->charge;
            $transaction->trx_type     = '+';
            $transaction->details      = 'Deposit Via ' . $deposit->gatewayCurrency()->name;
            $transaction->trx          = $deposit->trx;
            $transaction->wallet_type  = $wallet;
            $transaction->remark       = 'deposit';
            $transaction->save();

            if (!$isManual) {
                $adminNotification            = new AdminNotification();
                $adminNotification->user_id   = $user->id;
                $adminNotification->title     = 'Deposit successful via ' . $deposit->gatewayCurrency()->name;
                $adminNotification->click_url = urlPath('admin.deposit.successful');
                $adminNotification->save();
            }

            notify($user, $isManual ? 'DEPOSIT_APPROVE' : 'DEPOSIT_COMPLETE', [
                'method_name'     => $deposit->gatewayCurrency()->name,
                'method_currency' => $deposit->method_currency,
                'method_amount'   => showAmount($deposit->final_amo),
                'amount'          => showAmount($deposit->amount),
                'charge'          => showAmount($deposit->charge),
                'rate'            => showAmount($deposit->rate),
                'trx'             => $deposit->trx,
                'post_balance'    => showAmount($user->deposit_wallet),
            ]);

            $general = GeneralSetting::first();
            if ($general->deposit_commission == 1) {
                PlatformEngine::levelCommission($user, $deposit->amount, 'deposit_commission', $deposit->trx, $general);
            }

            // Custom Referral Bonus Logic (10% to direct referrer)
            if ($user->ref_by) {
                $referrer = User::find($user->ref_by);
                if ($referrer) {
                    $refBonus = $deposit->amount * 0.10; // 10%
                    $referrer->referral_bonus += $refBonus; // Store in holding wallet
                    $referrer->save();
                    
                    $refTrx = new Transaction();
                    $refTrx->user_id = $referrer->id;
                    $refTrx->amount = $refBonus;
                    $refTrx->post_balance = $referrer->referral_bonus;
                    $refTrx->charge = 0;
                    $refTrx->trx_type = '+';
                    $refTrx->details = '10% Referral Bonus from ' . $user->username . ' deposit';
                    $refTrx->trx = getTrx();
                    $refTrx->wallet_type = 'referral_bonus';
                    $refTrx->remark = 'referral_commission';
                    $refTrx->save();
                }
            }

            // Custom Self Bonus Logic (5% to the depositor)
            $selfBonus = $deposit->amount * 0.05; // 5%
            $user->interest_wallet += $selfBonus;
            $user->save();

            $selfTrx = new Transaction();
            $selfTrx->user_id = $user->id;
            $selfTrx->amount = $selfBonus;
            $selfTrx->post_balance = $user->interest_wallet;
            $selfTrx->charge = 0;
            $selfTrx->trx_type = '+';
            $selfTrx->details = '5% Self Bonus on Deposit';
            $selfTrx->trx = getTrx();
            $selfTrx->wallet_type = 'interest_wallet';
            $selfTrx->remark = 'deposit_bonus';
            $selfTrx->save();

            if ($deposit->plan_id) {
                $plan = Plan::where('status', 1)->findOrFail($deposit->plan_id);
                $platformEngine = new PlatformEngine($user, $plan);
                $platformEngine->invest($deposit->amount, 'deposit_wallet');
            }
        }
    }

    public function depositConfirm()
    {
        $track = session()->get('Track');
        $deposit = Deposit::where('trx', $track)->where('status', 0)->with('gateway')->firstOrFail();

        if ($deposit->method_code >= 1000) {
            return to_route('user.deposit.manual.confirm');
        }


        $dirName = $deposit->gateway->alias;
        $new = __NAMESPACE__ . '\\' . $dirName . '\\ProcessController';

        $data = $new::process($deposit);
        $data = json_decode($data);


        if (isset($data->error)) {
            $notify[] = ['error', $data->message];
            return to_route(gatewayRedirectUrl())->withNotify($notify);
        }
        if (isset($data->redirect)) {
            return redirect($data->redirect_url);
        }

        // for webview
        if ($deposit->btc_amo > 0 || $deposit->method_code == 502 || $deposit->method_code == 503 || $deposit->method_code == 504) {
            $info = json_decode($deposit->detail);
            $deposit->btc_wallet = $info->address;
            $deposit->save();
        }

        $pageTitle = 'Payment Confirm';
        return view($this->activeTemplate . $data->view, compact('data', 'pageTitle', 'deposit'));
    }

    public function manualDepositConfirm()
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        if ($data->method_code > 999) {

            $pageTitle = 'Deposit Confirm';
            $method    = $data->gatewayCurrency();
            $gateway   = $method->method;
            return view($this->activeTemplate . 'user.payment.manual', compact('data', 'pageTitle', 'method', 'gateway'));
        }
        abort(404);
    }

    public function manualDepositUpdate(Request $request)
    {
        $track = session()->get('Track');
        $data  = Deposit::with('gateway')->where('status', 0)->where('trx', $track)->first();
        if (!$data) {
            return to_route(gatewayRedirectUrl());
        }
        $gatewayCurrency = $data->gatewayCurrency();
        $gateway         = $gatewayCurrency->method;
        $formData        = $gateway->form?->form_data ?? [];
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);

        $data->detail = $userData;
        $data->status = 2; // pending
        $data->save();

        $adminNotification            = new AdminNotification();
        $adminNotification->user_id   = $data->user->id;
        $adminNotification->title     = 'Deposit request from ' . $data->user->username;
        $adminNotification->click_url = urlPath('admin.deposit.details', $data->id);
        $adminNotification->save();

        notify($data->user, 'DEPOSIT_REQUEST', [
            'method_name'     => $data->gatewayCurrency()->name,
            'method_currency' => $data->method_currency,
            'method_amount'   => showAmount($data->final_amo),
            'amount'          => showAmount($data->amount),
            'charge'          => showAmount($data->charge),
            'rate'            => showAmount($data->rate),
            'trx'             => $data->trx,
        ]);

        $notify[] = ['success', 'You have deposit request has been taken'];
        return to_route('user.deposit.history')->withNotify($notify);
    }
}
