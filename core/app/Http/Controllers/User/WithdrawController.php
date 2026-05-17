<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\PlatformEngine;
use App\Models\AdminNotification;
use App\Models\Transaction;
use App\Models\User;
use App\Models\Withdrawal;
use App\Models\WithdrawMethod;
use Carbon\Carbon;
use Illuminate\Http\Request;

class WithdrawController extends Controller
{
    public function withdrawMoney()
    {
        $withdrawMethod = WithdrawMethod::where('status', 1)->get();
        $pageTitle      = 'Withdraw Money';
        $savedWithdrawAddress = trim((string) (auth()->user()->bep20_wallet_address ?? ''));

        $isHoliday      = PlatformEngine::isHoliDay(now()->toDateTimeString(), gs());
        $nextWorkingDay = now()->toDateString();

        if ($isHoliday && !gs()->holiday_withdraw) {
            $nextWorkingDay = PlatformEngine::nextWorkingDay(24);
            $nextWorkingDay = Carbon::parse($nextWorkingDay)->toDateString();
        }

        return view($this->activeTemplate . 'user.withdraw.methods', compact('pageTitle', 'withdrawMethod', 'isHoliday', 'nextWorkingDay', 'savedWithdrawAddress'));
    }

    public function withdrawAddress()
    {
        $pageTitle             = 'Wallet Address';
        $savedWithdrawAddress  = trim((string) (auth()->user()->bep20_wallet_address ?? ''));
        return view($this->activeTemplate . 'user.withdraw.address', compact('pageTitle', 'savedWithdrawAddress'));
    }

    public function updateWithdrawAddress(Request $request)
    {
        $this->validate($request, [
            'wallet_address' => ['required', 'string', 'max:64', 'regex:/^0x[a-fA-F0-9]{40}$/'],
        ], [
            'wallet_address.regex' => 'Please provide a valid USDT BEP20 address.',
        ]);

        $user           = auth()->user();
        $currentAddress = trim((string) ($user->bep20_wallet_address ?? ''));
        $newAddress     = $this->normalizeWalletAddress((string) $request->wallet_address);

        $isAddressChange = $currentAddress !== '' && strcasecmp($currentAddress, $newAddress) !== 0;

        if ($isAddressChange) {
            if (!$user->ts) {
                $notify[] = ['error', 'Enable 2FA security before changing your wallet address'];
                return back()->withNotify($notify);
            }

            if (!$request->filled('authenticator_code')) {
                $notify[] = ['error', 'Google Authenticator code is required to change wallet address'];
                return back()->withNotify($notify);
            }

            if (!verifyG2fa($user, $request->authenticator_code)) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        if ($this->walletAddressUsedByAnotherUser($newAddress, (int) $user->id)) {
            $notify[] = ['error', 'This wallet address is already used by another account'];
            return back()->withNotify($notify);
        }

        $user->bep20_wallet_address = $newAddress;
        $user->save();

        $successMessage = $isAddressChange ? 'Wallet address changed successfully' : 'USDT BEP20 wallet address saved successfully';
        $notify[]       = ['success', $successMessage];
        return back()->withNotify($notify);
    }

    public function saveWithdrawAddress(Request $request)
    {
        $this->validate($request, [
            'wallet_address' => ['required', 'string', 'max:64', 'regex:/^0x[a-fA-F0-9]{40}$/'],
        ], [
            'wallet_address.regex' => 'Please provide a valid USDT BEP20 address.',
        ]);

        $user           = auth()->user();
        $currentAddress = trim((string) ($user->bep20_wallet_address ?? ''));
        $newAddress     = $this->normalizeWalletAddress((string) $request->wallet_address);

        if ($currentAddress !== '' && strcasecmp($currentAddress, $newAddress) !== 0) {
            $notify[] = ['error', 'Use Wallet Address menu to change address. 2FA verification is required'];
            return back()->withNotify($notify);
        }

        if ($this->walletAddressUsedByAnotherUser($newAddress, (int) $user->id)) {
            $notify[] = ['error', 'This wallet address is already used by another account'];
            return back()->withNotify($notify);
        }

        $user->bep20_wallet_address = $newAddress;
        $user->save();

        $notify[] = ['success', 'USDT BEP20 wallet address saved successfully'];
        return back()->withNotify($notify);
    }

    public function withdrawStore(Request $request)
    {

        $isHoliday = PlatformEngine::isHoliDay(now()->toDateTimeString(), gs());
        if ($isHoliday && !gs()->holiday_withdraw) {
            $notify[] = ['error', 'Today is holiday. You\'re unable to withdraw today'];
            return back()->withNotify($notify);
        }
        $withdrawAddress = trim((string) (auth()->user()->bep20_wallet_address ?? ''));

        if (!$this->isValidBep20Address($withdrawAddress)) {
            $notify[] = ['error', 'Please save your USDT BEP20 wallet address before withdrawal'];
            return back()->withNotify($notify);
        }

        $request->merge([
            'withdraw_address' => $withdrawAddress,
        ]);

        $this->validate($request, [
            'method_code'      => 'required',
            'amount'           => 'required|numeric|gt:0',
        ]);
        $method = WithdrawMethod::where('id', $request->method_code)->where('status', 1)->firstOrFail();
        $user   = auth()->user();
        if ($request->amount < $method->min_limit) {
            $notify[] = ['error', 'Your requested amount is smaller than minimum amount.'];
            return back()->withNotify($notify);
        }
        if ($request->amount > $method->max_limit) {
            $notify[] = ['error', 'Your requested amount is larger than maximum amount.'];
            return back()->withNotify($notify);
        }

        if ($request->amount > $user->interest_wallet) {
            $notify[] = ['error', 'You do not have sufficient balance for withdraw.'];
            return back()->withNotify($notify);
        }

        $charge      = $method->fixed_charge + ($request->amount * $method->percent_charge / 100);
        $afterCharge = $request->amount - $charge;
        $finalAmount = $afterCharge * $method->rate;

        $withdraw               = new Withdrawal();
        $withdraw->method_id    = $method->id; // wallet method ID
        $withdraw->user_id      = $user->id;
        $withdraw->amount       = $request->amount;
        $withdraw->currency     = $method->currency;
        $withdraw->rate         = $method->rate;
        $withdraw->charge       = $charge;
        $withdraw->final_amount = $finalAmount;
        $withdraw->after_charge = $afterCharge;
        $withdraw->withdraw_information = [
            [
                'name'  => 'USDT BEP20 Address',
                'type'  => 'text',
                'value' => $withdrawAddress,
            ],
        ];
        $withdraw->trx          = getTrx();
        $withdraw->save();
        session()->put('wtrx', $withdraw->trx);
        return to_route('user.withdraw.preview');
    }

    public function withdrawPreview()
    {
        $withdraw  = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id', 'desc')->firstOrFail();
        $pageTitle = 'Withdraw Preview';
        return view($this->activeTemplate . 'user.withdraw.preview', compact('pageTitle', 'withdraw'));
    }

    public function withdrawSubmit(Request $request)
    {
        $withdraw = Withdrawal::with('method', 'user')->where('trx', session()->get('wtrx'))->where('status', 0)->orderBy('id', 'desc')->firstOrFail();
        $existingWithdrawInfo = $this->normalizeWithdrawInformation($withdraw->withdraw_information);
        $withdrawAddress      = $this->extractWithdrawAddress($existingWithdrawInfo);

        if (!$this->isValidBep20Address($withdrawAddress)) {
            $notify[] = ['error', 'Please provide a valid USDT BEP20 address.'];
            return to_route('user.withdraw.money')->withNotify($notify);
        }

        $method = $withdraw->method;
        if ($method->status == 0) {
            abort(404);
        }

        $formData = $method->form->form_data ?? [];

        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);
        $userData = $formProcessor->processFormData($request, $formData);
        $userData = array_merge($existingWithdrawInfo, $userData);

        $user = auth()->user();
        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        return \Illuminate\Support\Facades\DB::transaction(function () use ($withdraw, $userData) {
            $user = User::where('id', auth()->id())->lockForUpdate()->first();
            $lockedWithdraw = Withdrawal::where('id', $withdraw->id)->where('status', 0)->lockForUpdate()->first();

            if (!$lockedWithdraw) {
                $notify[] = ['error', 'Withdrawal already processed'];
                return to_route('user.withdraw.history')->withNotify($notify);
            }

            if ($lockedWithdraw->amount > $user->interest_wallet) {
                $notify[] = ['error', 'Your request amount is larger then your current balance.'];
                return back()->withNotify($notify);
            }

            $lockedWithdraw->status               = 2; // Pending
            $lockedWithdraw->withdraw_information = $userData;
            $lockedWithdraw->save();
            
            $user->interest_wallet -= $lockedWithdraw->amount;
            $user->save();

            $transaction               = new Transaction();
            $transaction->user_id      = $lockedWithdraw->user_id;
            $transaction->amount       = $lockedWithdraw->amount;
            $transaction->post_balance = $user->interest_wallet;
            $transaction->charge       = $lockedWithdraw->charge;
            $transaction->trx_type     = '-';
            $transaction->details      = showAmount($lockedWithdraw->final_amount) . ' ' . $lockedWithdraw->currency . ' Withdraw Via ' . $lockedWithdraw->method->name;
            $transaction->trx          = $lockedWithdraw->trx;
            $transaction->wallet_type  = 'interest_wallet';
            $transaction->remark       = 'withdraw';
            $transaction->save();

            $adminNotification            = new AdminNotification();
            $adminNotification->user_id   = $user->id;
            $adminNotification->title     = 'New withdraw request from ' . $user->username;
            $adminNotification->click_url = urlPath('admin.withdraw.details', $lockedWithdraw->id);
            $adminNotification->save();

            notify($user, 'WITHDRAW_REQUEST', [
                'method_name'     => $lockedWithdraw->method->name,
                'method_currency' => $lockedWithdraw->currency,
                'method_amount'   => showAmount($lockedWithdraw->final_amount),
                'amount'          => showAmount($lockedWithdraw->amount),
                'charge'          => showAmount($lockedWithdraw->charge),
                'rate'            => showAmount($lockedWithdraw->rate),
                'trx'             => $lockedWithdraw->trx,
                'post_balance'    => showAmount($user->interest_wallet),
            ]);

            $notify[] = ['success', 'Withdraw request sent successfully'];
            return to_route('user.withdraw.history')->withNotify($notify);
        });
    }

    private function normalizeWithdrawInformation($withdrawInformation): array
    {
        if (is_array($withdrawInformation)) {
            return array_map(fn($item) => is_object($item) ? (array) $item : $item, $withdrawInformation);
        }

        if (is_object($withdrawInformation)) {
            $normalized = json_decode(json_encode($withdrawInformation), true);
            return is_array($normalized) ? $normalized : [];
        }

        return [];
    }

    private function extractWithdrawAddress(array $withdrawInformation): ?string
    {
        foreach ($withdrawInformation as $item) {
            if (!is_array($item)) {
                continue;
            }

            if (strtolower($item['name'] ?? '') === strtolower('USDT BEP20 Address')) {
                return trim((string) ($item['value'] ?? ''));
            }
        }

        return null;
    }

    private function isValidBep20Address(?string $address): bool
    {
        if (!$address) {
            return false;
        }

        return (bool) preg_match('/^0x[a-fA-F0-9]{40}$/', $address);
    }

    private function normalizeWalletAddress(string $address): string
    {
        return strtolower(trim($address));
    }

    private function walletAddressUsedByAnotherUser(string $address, int $currentUserId): bool
    {
        return User::query()
            ->whereNotNull('bep20_wallet_address')
            ->whereRaw('LOWER(bep20_wallet_address) = ?', [strtolower($address)])
            ->where('id', '!=', $currentUserId)
            ->exists();
    }

    public function withdrawLog(Request $request)
    {
        $pageTitle = "Withdraw Log";
        $withdraws = Withdrawal::where('user_id', auth()->id())->where('status', '!=', 0);
        if ($request->search) {
            $withdraws = $withdraws->where('trx', $request->search);
        }
        $withdraws = $withdraws->with('method')->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.withdraw.log', compact('pageTitle', 'withdraws'));
    }
}
