<?php

namespace App\Http\Controllers;

use App\Models\GatewayCurrency;
use App\Models\Invest;
use App\Models\Plan;
use App\Models\User;
use App\Models\WithdrawMethod;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ChatbotController extends Controller
{
    public function message(Request $request): JsonResponse
    {
        $validated = $request->validate([
            'message' => 'required|string|max:500',
        ]);

        $message = trim((string) $validated['message']);
        $reply   = $this->generateReply($message, auth()->user());

        return response()->json([
            'ok'      => true,
            'reply'   => $reply,
            'message' => $message,
        ]);
    }

    private function generateReply(string $message, ?User $user): string
    {
        $text  = Str::lower($message);
        $plans = Plan::where('status', 1)
            ->orderByRaw('CASE WHEN fixed_amount > 0 THEN fixed_amount ELSE minimum END ASC')
            ->get([
                'id',
                'name',
                'minimum',
                'maximum',
                'fixed_amount',
                'interest',
                'interest_type',
                'time_name',
                'repeat_time',
                'lifetime',
                'capital_back',
            ]);

        $matchedPlan = $this->matchPlanFromMessage($text, $plans);

        if ($matchedPlan) {
            return $this->planDetailsReply($matchedPlan);
        }

        if ($this->containsAny($text, ['hi', 'hello', 'hey', 'namaste', 'hlo'])) {
            return "Hello! I am Core Asset Investing AI assistant.\nI can help with register, login, plans, deposit, withdrawal, 2FA, password change, and company details.";
        }

        if ($this->containsAny($text, ['register', 'signup', 'sign up', 'create account', 'new account'])) {
            return $this->registerReply();
        }

        if ($this->containsAny($text, ['login', 'log in', 'sign in', 'signin'])) {
            return $this->loginReply();
        }

        if ($this->containsAny($text, ['2fa', 'two factor', 'two-factor', 'f2 verification', 'otp verification', 'google authenticator'])) {
            return $this->twoFactorReply();
        }

        if ($this->containsAny($text, ['change password', 'password change', 'reset password', 'forgot password', 'forgot pass'])) {
            return $this->passwordReply();
        }

        if ($this->containsAny($text, ['my plan', 'my investment', 'active plan', 'my return', 'mera plan', 'meri investment'])) {
            return $this->userPlanReply($user);
        }

        if ($this->containsAny($text, ['plan list', 'show plan', 'investment plan', 'plans', 'which plan', 'plan'])) {
            return $this->planListReply($plans);
        }

        if ($this->containsAny($text, ['deposit', 'recharge', 'add money', 'fund', 'payment method'])) {
            return $this->depositReply();
        }

        if ($this->containsAny($text, ['withdraw', 'cashout', 'payout', 'withdr'])) {
            return $this->withdrawReply($user);
        }

        if ($this->containsAny($text, ['about', 'company', 'address', 'california', 'usa', 'us', 'bitcoin trade', 'bitcoin trading', 'start date', 'since', '2023', 'march 2023'])) {
            return $this->companyReply();
        }

        if ($this->containsAny($text, ['referral', 'refer', 'invite'])) {
            return "Referral guide:\n1. Share your referral link/code\n2. New user registers through your referral\n3. Referral benefits are credited as per platform settings.";
        }

        return "I can help with register, login, plan details, deposit, withdrawal, 2FA, password change, referral, and company details.\nTry: 'show plans', 'how to register', or 'company details'.";
    }

    private function matchPlanFromMessage(string $text, $plans): ?Plan
    {
        foreach ($plans as $plan) {
            $planName = Str::lower((string) $plan->name);
            if ($planName !== '' && Str::contains($text, $planName)) {
                return $plan;
            }
        }

        if (preg_match('/plan\s*#?\s*(\d+)/i', $text, $matches)) {
            return $plans->firstWhere('id', (int) $matches[1]);
        }

        return null;
    }

    private function planDetailsReply(Plan $plan): string
    {
        $currencyText = gs()->cur_text;
        $currencySym  = gs()->cur_sym;

        if ((float) $plan->fixed_amount > 0) {
            $investmentLine = "Investment: {$currencySym}" . showAmount($plan->fixed_amount) . " ({$currencyText})";
        } else {
            $investmentLine = "Investment Range: {$currencySym}" . showAmount($plan->minimum) . " - {$currencySym}" . showAmount($plan->maximum) . " ({$currencyText})";
        }

        $interestValue = $plan->interest_type == 1
            ? showAmount($plan->interest) . "% every {$plan->time_name}"
            : $currencySym . showAmount($plan->interest) . " {$currencyText} every {$plan->time_name}";

        $durationLine = $plan->lifetime == 1
            ? "Duration: Lifetime"
            : "Duration: {$plan->repeat_time} {$plan->time_name} cycles";

        $capitalBack = $plan->capital_back ? "Yes" : "No";

        return "Plan: {$plan->name}\n{$investmentLine}\nReturn: {$interestValue}\n{$durationLine}\nCapital Back: {$capitalBack}";
    }

    private function planListReply($plans): string
    {
        if ($plans->isEmpty()) {
            return "No active investment plans are available right now.";
        }

        $currencySym = gs()->cur_sym;
        $lines       = ["Active Plans:"];

        foreach ($plans->take(8) as $plan) {
            $price = (float) $plan->fixed_amount > 0
                ? $currencySym . showAmount($plan->fixed_amount)
                : $currencySym . showAmount($plan->minimum) . "-" . $currencySym . showAmount($plan->maximum);

            $return = $plan->interest_type == 1
                ? showAmount($plan->interest) . "%/" . $plan->time_name
                : $currencySym . showAmount($plan->interest) . "/" . $plan->time_name;

            $lines[] = "{$plan->id}. {$plan->name} | {$price} | {$return}";
        }

        $lines[] = "Type plan name for full details. Example: Starter Plan";
        return implode("\n", $lines);
    }

    private function userPlanReply(?User $user): string
    {
        if (!$user) {
            return "Please login first to view your personal plan summary.";
        }

        $invests = Invest::where('user_id', $user->id)
            ->where('status', 1)
            ->with('plan:id,name,time_name')
            ->orderBy('id', 'desc')
            ->get();

        if ($invests->isEmpty()) {
            return "You currently have no active investments.\nGo to Investment section to start with an active plan.";
        }

        $currencySym       = gs()->cur_sym;
        $currencyText      = gs()->cur_text;
        $totalInvested     = $invests->sum('amount');
        $interestPerCycle  = $invests->sum('interest');
        $groupedByPlan     = $invests->groupBy(function ($invest) {
            return optional($invest->plan)->name ?: 'Plan';
        });

        $lines   = [];
        $lines[] = "Your Active Investment Summary:";
        $lines[] = "Total Invested: {$currencySym}" . showAmount($totalInvested) . " {$currencyText}";
        $lines[] = "Estimated Interest/Cycle: {$currencySym}" . showAmount($interestPerCycle) . " {$currencyText}";

        foreach ($groupedByPlan->take(6) as $planName => $rows) {
            $planAmount   = $rows->sum('amount');
            $planInterest = $rows->sum('interest');
            $timeName     = (string) ($rows->first()->time_name ?? optional($rows->first()->plan)->time_name ?? 'cycle');
            $lines[]      = "{$planName}: {$currencySym}" . showAmount($planAmount) . " invested, {$currencySym}" . showAmount($planInterest) . " per {$timeName}";
        }

        return implode("\n", $lines);
    }

    private function depositReply(): string
    {
        $currencySym = gs()->cur_sym;
        $currency    = gs()->cur_text;

        $methods = GatewayCurrency::whereHas('method', function ($query) {
            $query->where('status', 1);
        })->with('method')->orderBy('id')->get();

        if ($methods->isEmpty()) {
            return "No active deposit method is available right now. Please contact support.";
        }

        $lines   = ["Active Deposit Methods:"];
        foreach ($methods->take(5) as $method) {
            $mode = ((int) $method->method_code >= 1000) ? 'Manual' : 'Automatic';
            $lines[] = "{$method->name} ({$mode}) | Limit {$currencySym}" . showAmount($method->min_amount) . "-{$currencySym}" . showAmount($method->max_amount) . " {$currency}";
        }

        $lines[] = "Deposit steps: Login -> Deposit -> Select method -> Enter amount -> Submit.";
        return implode("\n", $lines);
    }

    private function withdrawReply(?User $user): string
    {
        $method = WithdrawMethod::where('status', 1)->orderBy('id')->first();
        if (!$method) {
            return "Withdrawal is temporarily unavailable.";
        }

        $currencySym = gs()->cur_sym;
        $currency    = gs()->cur_text;
        $savedStatus = (!$user || !trim((string) $user->bep20_wallet_address))
            ? "No saved BEP20 wallet found."
            : "Saved BEP20 wallet is active.";

        return "Withdrawal info:\nMethod: {$method->name}\nLimit: {$currencySym}" . showAmount($method->min_limit) . "-{$currencySym}" . showAmount($method->max_limit) . " {$currency}\n{$savedStatus}\nWithdrawal steps: Login -> Withdraw -> Confirm saved wallet -> Submit request.";
    }

    private function registerReply(): string
    {
        return "Registration guide:\n1. Open " . route('user.register') . "\n2. Fill name, email, mobile, password, and referral code (if any)\n3. Submit signup form\n4. Verify email/mobile if prompted\nAfter registration, login from " . route('user.login');
    }

    private function loginReply(): string
    {
        return "Login guide:\n1. Open " . route('user.login') . "\n2. Enter username/email and password\n3. Complete 2FA if enabled\n4. Access your dashboard.";
    }

    private function twoFactorReply(): string
    {
        return "2FA verification guide:\n1. Login and open User Menu -> Two Factor\n2. Scan QR in Google Authenticator\n3. Enter 6-digit OTP and enable 2FA\nDuring login, OTP verification is required for extra account security.";
    }

    private function passwordReply(): string
    {
        return "Password change guide:\n1. Login -> Profile Setting -> Change Password\n2. Enter current password\n3. Enter new password and confirm\nIf password is forgotten, use 'Forgot Password' on login page to reset.";
    }

    private function companyReply(): string
    {
        return "Core Asset Investing company info:\n1. Company start date: March 2023\n2. Location: California, United States (USA)\n3. Business model: Bitcoin-focused trading strategies with risk management\n4. User returns are credited according to the active investment plan terms shown on dashboard.";
    }

    private function containsAny(string $haystack, array $keywords): bool
    {
        foreach ($keywords as $keyword) {
            if (Str::contains($haystack, Str::lower($keyword))) {
                return true;
            }
        }

        return false;
    }
}
