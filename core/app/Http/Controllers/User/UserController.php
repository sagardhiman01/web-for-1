<?php

namespace App\Http\Controllers\User;

use App\Http\Controllers\Controller;
use App\Lib\FormProcessor;
use App\Lib\GoogleAuthenticator;
use App\Lib\HyipLab;
use App\Models\Deposit;
use App\Models\Form;
use App\Models\Invest;
use App\Models\InvestmentCode;
use App\Models\PromotionTool;
use App\Models\Referral;
use App\Models\SupportTicket;
use App\Models\Transaction;
use App\Models\User;
use App\Models\UserInvestmentCode;
use App\Models\Withdrawal;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class UserController extends Controller
{
    public function home()
    {
        $data['pageTitle']         = 'Dashboard';
        $user                      = auth()->user();
        $userId                    = $user->id;
        $data['user']              = $user;
        $data['totalTicket']           = SupportTicket::where('user_id', $userId)->count();
        $data['transactions']          = Transaction::where('user_id', $userId)->latest('id')->limit(8)->get();

        $depositStats = Deposit::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(amount),0) as requested_deposits')
            ->selectRaw('COALESCE(SUM(CASE WHEN status != 0 THEN amount ELSE 0 END),0) as submitted_deposits')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 1 THEN amount ELSE 0 END),0) as successful_deposits')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 0 THEN amount ELSE 0 END),0) as initiated_deposits')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 2 AND method_code >= 1000 THEN amount ELSE 0 END),0) as pending_deposits')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 3 AND method_code >= 1000 THEN amount ELSE 0 END),0) as rejected_deposits')
            ->first();

        $withdrawStats = Withdrawal::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(amount),0) as requested_withdrawals')
            ->selectRaw('COALESCE(SUM(CASE WHEN status != 0 THEN amount ELSE 0 END),0) as submitted_withdrawals')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 1 THEN amount ELSE 0 END),0) as successful_withdrawals')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 2 THEN amount ELSE 0 END),0) as pending_withdrawals')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 3 THEN amount ELSE 0 END),0) as rejected_withdrawals')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 0 THEN amount ELSE 0 END),0) as initiated_withdrawals')
            ->first();

        $investStats = Invest::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(amount),0) as invests')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 0 THEN amount ELSE 0 END),0) as completed_invests')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 1 THEN amount ELSE 0 END),0) as running_invests')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 1 AND wallet_type = "deposit_wallet" THEN amount ELSE 0 END),0) as deposit_wallet_invests')
            ->selectRaw('COALESCE(SUM(CASE WHEN status = 1 AND wallet_type = "interest_wallet" THEN amount ELSE 0 END),0) as interest_wallet_invests')
            ->first();

        $transactionStats = Transaction::where('user_id', $userId)
            ->selectRaw('COALESCE(SUM(CASE WHEN remark = "interest" THEN amount ELSE 0 END),0) as interests')
            ->selectRaw('COALESCE(SUM(CASE WHEN remark = "referral_commission" THEN amount ELSE 0 END),0) as referral_earnings')
            ->first();

        $data['requestedDeposits']   = $depositStats->requested_deposits ?? 0;
        $data['submittedDeposits']   = $depositStats->submitted_deposits ?? 0;
        $data['successfulDeposits']  = $depositStats->successful_deposits ?? 0;
        $data['initiatedDeposits']   = $depositStats->initiated_deposits ?? 0;
        $data['pendingDeposits']     = $depositStats->pending_deposits ?? 0;
        $data['rejectedDeposits']    = $depositStats->rejected_deposits ?? 0;
        $data['totalDeposit']        = $data['successfulDeposits'];

        $data['requestedWithdrawals']   = $withdrawStats->requested_withdrawals ?? 0;
        $data['submittedWithdrawals']   = $withdrawStats->submitted_withdrawals ?? 0;
        $data['successfulWithdrawals']  = $withdrawStats->successful_withdrawals ?? 0;
        $data['pendingWithdrawals']     = $withdrawStats->pending_withdrawals ?? 0;
        $data['rejectedWithdrawals']    = $withdrawStats->rejected_withdrawals ?? 0;
        $data['initiatedWithdrawals']   = $withdrawStats->initiated_withdrawals ?? 0;
        $data['totalWithdraw']          = $data['successfulWithdrawals'];

        $data['invests']                = $investStats->invests ?? 0;
        $data['totalInvest']            = $data['invests'];
        $data['completedInvests']       = $investStats->completed_invests ?? 0;
        $data['runningInvests']         = $investStats->running_invests ?? 0;
        $data['depositWalletInvests']   = $investStats->deposit_wallet_invests ?? 0;
        $data['interestWalletInvests']  = $investStats->interest_wallet_invests ?? 0;

        $data['interests']              = $transactionStats->interests ?? 0;
        $data['referral_earnings']      = $transactionStats->referral_earnings ?? 0;

        $data['lastWithdraw'] = Withdrawal::where('user_id', $userId)
            ->where('status', 1)
            ->latest('id')
            ->first('amount');

        $data['lastDeposit'] = Deposit::where('user_id', $userId)
            ->where('status', 1)
            ->latest('id')
            ->first('amount');

        $data['isHoliday']      = HyipLab::isHoliDay(now()->toDateTimeString(), gs());
        $data['nextWorkingDay'] = now()->toDateString();
        if ($data['isHoliday']) {
            $data['nextWorkingDay'] = HyipLab::nextWorkingDay(24);
            $data['nextWorkingDay'] = Carbon::parse($data['nextWorkingDay'])->toDateString();
        }

        $dbDriver = \DB::connection()->getDriverName();
        $dateFormat = $dbDriver === 'pgsql' ? "TO_CHAR(created_at, 'YYYY-MM-DD')" : "DATE_FORMAT(created_at,'%Y-%m-%d')";

        $data['chartData'] = Transaction::where('remark', 'interest')
            ->where('created_at', '>=', Carbon::now()->subDays(30))
            ->where('user_id', $userId)
            ->selectRaw("SUM(amount) as amount, $dateFormat as date")
            ->orderBy('date', 'asc')
            ->groupBy('date')
            ->get();

        $activeCode = InvestmentCode::where('status', 1)->latest()->first();
        $isActiveCodeValid = (bool) $activeCode && (!$activeCode->expires_at || now()->lte($activeCode->expires_at));

        $userCodeRedemption = null;
        if ($activeCode) {
            $userCodeRedemption = UserInvestmentCode::with('investmentCode')
                ->where('user_id', $user->id)
                ->where('investment_code_id', $activeCode->id)
                ->first();
        }

        $isUnlocked = false;
        $showActivatedText = false;
        $codeReEntryAt = null;
        $codeActivatedAt = null;
        if ($userCodeRedemption && $userCodeRedemption->redeemed_at) {
            $codeActivatedAt = $userCodeRedemption->redeemed_at->copy();
            $codeReEntryAt = $userCodeRedemption->redeemed_at->copy()->addHours(24);
            $isUnlocked = now()->lt($codeReEntryAt);
            $showActivatedText = $userCodeRedemption->redeemed_at->gte(now()->subMinutes(2));
        }

        $data['isInvestmentCodeUnlocked']          = $isUnlocked && $isActiveCodeValid;
        $data['showInvestmentCodeActivatedMessage']= $showActivatedText && $data['isInvestmentCodeUnlocked'];
        $data['redeemedInvestmentCode']            = $activeCode?->code;
        $data['codeActivatedAt']                   = $codeActivatedAt;
        $data['codeActivatedAtMs']                 = $codeActivatedAt?->valueOf();
        $data['codeReEntryAt']                     = $codeReEntryAt;
        $data['codeReEntryAtMs']                   = $codeReEntryAt?->valueOf();
        $data['activeCodeExpiresAt']               = $activeCode?->expires_at;
        $data['activeCodeExpiresAtMs']             = $activeCode?->expires_at?->valueOf();

        $interestPlanSummary = Invest::where('user_id', $user->id)
            ->where('status', 1)
            ->with('plan')
            ->get()
            ->groupBy('plan_id')
            ->map(function ($investGroup) {
                $planName = optional($investGroup->first()->plan)->name ?? 'Plan';
                return (object) [
                    'plan_name'         => $planName,
                    'total_invest'      => $investGroup->sum('amount'),
                    'interest_per_cycle'=> $investGroup->sum('interest'),
                    'time_name'         => $investGroup->first()->time_name,
                ];
            })
            ->values();

        $data['interestPlanSummary'] = $interestPlanSummary;
        $data['interestPerCycleTotal'] = $interestPlanSummary->sum('interest_per_cycle');

        $data['directCount'] = $user->referrals()->count();
        $totalTeam = $user->totalTeamCount();
        $data['indirectCount'] = $totalTeam - $data['directCount'];

        return view($this->activeTemplate . 'user.dashboard', $data);
    }

    public function redeemInvestmentCode(Request $request)
    {
        $request->validate([
            'investment_code' => 'required|alpha_num|size:8',
        ]);

        $submittedCode = strtoupper(trim($request->investment_code));

        $activeCode = InvestmentCode::where('status', 1)
            ->where('code', $submittedCode)
            ->first();

        if (!$activeCode) {
            $notify[] = ['error', 'Invalid or inactive code'];
            return back()->withNotify($notify);
        }

        if ($activeCode->expires_at && now()->gt($activeCode->expires_at)) {
            $notify[] = ['error', 'This code has expired'];
            return back()->withNotify($notify);
        }

        $userId = auth()->id();
        $userRedemption = UserInvestmentCode::where('user_id', $userId)
            ->where('investment_code_id', $activeCode->id)
            ->first();

        if (!$userRedemption) {
            $usedUserCount = UserInvestmentCode::where('investment_code_id', $activeCode->id)->count();
            if ($activeCode->max_users && $usedUserCount >= $activeCode->max_users) {
                $notify[] = ['error', 'This code usage limit has been reached'];
                return back()->withNotify($notify);
            }
        }

        if ($userRedemption && $userRedemption->redeemed_at) {
            $nextAllowedAt = $userRedemption->redeemed_at->copy()->addHours(24);
            if (now()->lt($nextAllowedAt)) {
                $notify[] = ['error', 'Code already active. Re-enter after ' . $nextAllowedAt->diffForHumans()];
                return back()->withNotify($notify);
            }
        }

        if ($userRedemption) {
            $userRedemption->redeemed_at = now();
            $userRedemption->save();
        } else {
            UserInvestmentCode::create([
                'user_id'            => $userId,
                'investment_code_id' => $activeCode->id,
                'redeemed_at'        => now(),
            ]);
        }

        // --- 8-Digit Code Commission Logic (Option 1) ---
        $user = auth()->user();
        $totalDailyInterest = \App\Models\Invest::where('user_id', $user->id)->where('status', 1)->sum('interest');

        if ($totalDailyInterest > 0 && $user->ref_by) {
            $levels = [10, 5, 2]; // 10% for level 1, 5% for level 2, 2% for level 3
            $currentUpline = $user->referrer;
            $trx = getTrx();
            
            for ($i = 0; $i < count($levels); $i++) {
                if (!$currentUpline) break;
                
                $commissionAmount = ($totalDailyInterest * $levels[$i]) / 100;
                
                if ($commissionAmount > 0) {
                    $currentUpline->interest_wallet += $commissionAmount;
                    $currentUpline->save();
                    
                    $transaction = new \App\Models\Transaction();
                    $transaction->user_id = $currentUpline->id;
                    $transaction->amount = $commissionAmount;
                    $transaction->post_balance = $currentUpline->interest_wallet;
                    $transaction->charge = 0;
                    $transaction->trx_type = '+';
                    $transaction->details = 'Level '.($i+1).' Daily Code Bonus from ' . $user->username;
                    $transaction->trx = $trx;
                    $transaction->wallet_type = 'interest_wallet';
                    $transaction->remark = 'code_matching_bonus';
                    $transaction->save();
                    
                    notify($currentUpline, 'REFERRAL_COMMISSION', [
                        'amount' => showAmount($commissionAmount),
                        'post_balance' => showAmount($currentUpline->interest_wallet),
                        'trx' => $trx,
                        'level' => ordinal($i+1),
                        'type' => 'Daily Code Matching Bonus'
                    ]);
                }
                
                $currentUpline = $currentUpline->referrer;
            }
        }
        // --- End Commission Logic ---

        $notify[] = ['success', 'Code activated for 24 hours. Plan-wise interest is now visible'];
        return back()->withNotify($notify);
    }

    public function currencyList(Request $request)
    {
        $pageTitle    = 'Currency List';
        $vsCurrency   = 'usd';
        $currencies   = $this->fetchCoinMarkets($vsCurrency);

        $selectedCoin = Str::lower((string) $request->query('coin', ''));
        $selectedData = collect($currencies)->firstWhere('id', $selectedCoin);

        if (!$selectedData && !empty($currencies)) {
            $selectedData = $currencies[0];
            $selectedCoin = $selectedData['id'];
        }

        if (!$selectedData) {
            $selectedCoin = 'bitcoin';
            $selectedData = $this->fetchSingleCoinMarket($selectedCoin, $vsCurrency);
        }

        $chartData = $this->fetchCoinCandleData($selectedCoin, $vsCurrency);

        return view($this->activeTemplate . 'user.currency_list', compact('pageTitle', 'currencies', 'selectedData', 'chartData', 'vsCurrency'));
    }

    public function currencyPrices()
    {
        $currencies = $this->fetchCoinMarkets('usd');
        if (empty($currencies)) {
            return response()->json([
                'status'  => 'error',
                'message' => 'Live prices unavailable',
            ], 503);
        }

        return response()->json([
            'status'     => 'success',
            'currencies' => $currencies,
            'updated_at' => now()->toDateTimeString(),
            'updated_at_ms' => now()->valueOf(),
        ]);
    }

    public function currencyChart($coinId)
    {
        $coinId = Str::lower((string) $coinId);

        if (!preg_match('/^[a-z0-9-]+$/', $coinId)) {
            return response()->json(['status' => 'error', 'message' => 'Invalid currency'], 422);
        }

        $coin = collect($this->fetchCoinMarkets('usd'))->firstWhere('id', $coinId);
        if (!$coin) {
            $coin = $this->fetchSingleCoinMarket($coinId, 'usd');
        }

        if (!$coin) {
            return response()->json(['status' => 'error', 'message' => 'Currency not found'], 404);
        }

        $chartData = $this->fetchCoinCandleData($coinId, 'usd');
        if (empty($chartData['candles'])) {
            return response()->json(['status' => 'error', 'message' => 'Chart data unavailable'], 503);
        }

        return response()->json([
            'status' => 'success',
            'coin'   => $coin,
            'chart'  => $chartData,
        ]);
    }

    private function fetchCoinMarkets(string $vsCurrency = 'usd'): array
    {
        $cacheKey = "coingecko_markets_{$vsCurrency}";

        return Cache::remember($cacheKey, now()->addSeconds(40), function () use ($vsCurrency) {
            $payload = $this->fetchCoinGeckoJson('https://api.coingecko.com/api/v3/coins/markets', [
                'vs_currency'             => $vsCurrency,
                'order'                   => 'market_cap_desc',
                'per_page'                => 250,
                'page'                    => 1,
                'sparkline'               => 'false',
                'price_change_percentage' => '24h',
            ]);

            if (!is_array($payload)) {
                return [];
            }

            return collect($payload)
                ->map(function ($coin) {
                    return [
                        'id'              => data_get($coin, 'id'),
                        'symbol'          => strtoupper((string) data_get($coin, 'symbol')),
                        'name'            => data_get($coin, 'name'),
                        'image'           => data_get($coin, 'image'),
                        'rank'            => data_get($coin, 'market_cap_rank'),
                        'current_price'   => (float) data_get($coin, 'current_price', 0),
                        'change_24h'      => (float) data_get($coin, 'price_change_percentage_24h', 0),
                        'market_cap'      => (float) data_get($coin, 'market_cap', 0),
                        'total_volume'    => (float) data_get($coin, 'total_volume', 0),
                        'last_updated_ms' => $this->parseToTimestampMs(data_get($coin, 'last_updated')),
                    ];
                })
                ->filter(fn($coin) => !empty($coin['id']) && !empty($coin['name']))
                ->values()
                ->all();
        });
    }

    private function fetchSingleCoinMarket(string $coinId, string $vsCurrency = 'usd'): ?array
    {
        $cacheKey = "coingecko_market_{$vsCurrency}_{$coinId}";

        return Cache::remember($cacheKey, now()->addSeconds(40), function () use ($coinId, $vsCurrency) {
            $payload = $this->fetchCoinGeckoJson('https://api.coingecko.com/api/v3/coins/markets', [
                'vs_currency' => $vsCurrency,
                'ids'         => $coinId,
                'sparkline'   => 'false',
            ]);

            if (!is_array($payload)) {
                return null;
            }

            $coin = collect($payload)->first();
            if (!$coin) {
                return null;
            }

            return [
                'id'            => data_get($coin, 'id'),
                'symbol'        => strtoupper((string) data_get($coin, 'symbol')),
                'name'          => data_get($coin, 'name'),
                'image'         => data_get($coin, 'image'),
                'rank'          => data_get($coin, 'market_cap_rank'),
                'current_price' => (float) data_get($coin, 'current_price', 0),
                'change_24h'    => (float) data_get($coin, 'price_change_percentage_24h', 0),
                'market_cap'    => (float) data_get($coin, 'market_cap', 0),
                'total_volume'  => (float) data_get($coin, 'total_volume', 0),
                'last_updated_ms' => $this->parseToTimestampMs(data_get($coin, 'last_updated')),
            ];
        });
    }

    private function fetchCoinCandleData(string $coinId, string $vsCurrency = 'usd'): array
    {
        $coinId = preg_replace('/[^a-z0-9-]/', '', Str::lower($coinId));
        $cacheKey = "coingecko_candles_{$vsCurrency}_{$coinId}";

        return Cache::remember($cacheKey, now()->addSeconds(55), function () use ($coinId, $vsCurrency) {
            $payload = $this->fetchCoinGeckoJson("https://api.coingecko.com/api/v3/coins/{$coinId}/ohlc", [
                'vs_currency' => $vsCurrency,
                'days'        => 1,
            ]);

            if (!is_array($payload) || empty($payload)) {
                return ['candles' => []];
            }

            $candles = [];
            foreach ($payload as $point) {
                if (!is_array($point) || count($point) < 5) {
                    continue;
                }

                $candles[] = [
                    'x' => (int) $point[0],
                    'y' => [
                        (float) $point[1],
                        (float) $point[2],
                        (float) $point[3],
                        (float) $point[4],
                    ],
                ];
            }

            return ['candles' => $candles];
        });
    }

    private function parseToTimestampMs($value): ?int
    {
        if (!$value) {
            return null;
        }

        try {
            return Carbon::parse($value)->valueOf();
        } catch (\Throwable $e) {
            return null;
        }
    }

    private function fetchCoinGeckoJson(string $url, array $query = []): ?array
    {
        $request = fn() => Http::acceptJson()
            ->withHeaders([
                'User-Agent' => 'CoreAssetInvesting/1.0 (+http://127.0.0.1)',
            ])
            ->timeout(20)
            ->get($url, $query);

        try {
            $response = $request();
            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            // Retry below with SSL verification disabled for local Windows environments.
        }

        try {
            $response = Http::acceptJson()
                ->withOptions(['verify' => false])
                ->withHeaders([
                    'User-Agent' => 'CoreAssetInvesting/1.0 (+http://127.0.0.1)',
                ])
                ->timeout(20)
                ->get($url, $query);

            if ($response->successful()) {
                return $response->json();
            }
        } catch (\Throwable $e) {
            return null;
        }

        return null;
    }

    public function depositHistory(Request $request)
    {
        $pageTitle = 'Deposit History';
        $deposits  = auth()->user()->deposits()->searchable(['trx'])->with(['gateway'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.deposit_history', compact('pageTitle', 'deposits'));
    }

    public function show2faForm()
    {
        $general   = gs();
        $ga        = new GoogleAuthenticator();
        $user      = auth()->user();
        $secret    = $ga->createSecret();
        $qrCodeUrl = $ga->getQRCodeGoogleUrl($user->username . '@' . $general->site_name, $secret);
        $pageTitle = '2FA Setting';
        return view($this->activeTemplate . 'user.twofactor', compact('pageTitle', 'secret', 'qrCodeUrl'));
    }

    public function create2fa(Request $request)
    {
        $user = auth()->user();
        $this->validate($request, [
            'key'  => 'required',
            'code' => 'required',
        ]);
        $response = verifyG2fa($user, $request->code, $request->key);
        if ($response) {
            $user->tsc = $request->key;
            $user->ts  = 1;
            $user->save();
            $notify[] = ['success', 'Google authenticator activated successfully'];
            return back()->withNotify($notify);
        } else {
            $notify[] = ['error', 'Wrong verification code'];
            return back()->withNotify($notify);
        }
    }

    public function disable2fa(Request $request)
    {
        $this->validate($request, [
            'code' => 'required',
        ]);

        $user     = auth()->user();
        $response = verifyG2fa($user, $request->code);
        if ($response) {
            $user->tsc = null;
            $user->ts  = 0;
            $user->save();
            $notify[] = ['success', 'Two factor authenticator deactivated successfully'];
        } else {
            $notify[] = ['error', 'Wrong verification code'];
        }
        return back()->withNotify($notify);
    }

    public function transactions(Request $request)
    {
        $pageTitle = 'Transactions';
        $remarks   = Transaction::distinct('remark')->orderBy('remark')->get('remark');

        $transactions = Transaction::where('user_id', auth()->id())->searchable(['trx'])->filter(['trx_type', 'remark', 'wallet_type'])->orderBy('id', 'desc')->paginate(getPaginate());
        return view($this->activeTemplate . 'user.transactions', compact('pageTitle', 'transactions', 'remarks'));
    }

    public function kycForm()
    {
        if (auth()->user()->kv == 2) {
            $notify[] = ['error', 'Your KYC is under review'];
            return to_route('user.home')->withNotify($notify);
        }
        if (auth()->user()->kv == 1) {
            $notify[] = ['error', 'You are already KYC verified'];
            return to_route('user.home')->withNotify($notify);
        }
        $pageTitle = 'KYC Form';
        $form      = Form::where('act', 'kyc')->first();
        return view($this->activeTemplate . 'user.kyc.form', compact('pageTitle', 'form'));
    }

    public function kycData()
    {
        $user      = auth()->user();
        $pageTitle = 'KYC Data';
        return view($this->activeTemplate . 'user.kyc.info', compact('pageTitle', 'user'));
    }

    public function kycSubmit(Request $request)
    {
        $form           = Form::where('act', 'kyc')->first();
        $formData       = $form->form_data;
        $formProcessor  = new FormProcessor();
        $validationRule = $formProcessor->valueValidation($formData);
        $request->validate($validationRule);

        $userData       = $formProcessor->processFormData($request, $formData);
        $user           = auth()->user();
        $user->kyc_data = $userData;
        $user->kv       = 2;
        $user->save();

        $notify[] = ['success', 'KYC data submitted successfully'];
        return to_route('user.home')->withNotify($notify);

    }

    public function attachmentDownload($fileHash)
    {
        $filePath  = decrypt($fileHash);
        $extension = pathinfo($filePath, PATHINFO_EXTENSION);
        $general   = gs();
        $title     = slug($general->site_name) . '- attachments.' . $extension;
        $mimetype  = mime_content_type($filePath);
        header('Content-Disposition: attachment; filename="' . $title);
        header("Content-Type: " . $mimetype);
        return readfile($filePath);
    }

    public function userData()
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $pageTitle = 'User Data';
        return view($this->activeTemplate . 'user.user_data', compact('pageTitle', 'user'));
    }

    public function userDataSubmit(Request $request)
    {
        $user = auth()->user();
        if ($user->profile_complete == 1) {
            return to_route('user.home');
        }
        $request->validate([
            'firstname' => 'required',
            'lastname'  => 'required',
        ]);
        $user->firstname = $request->firstname;
        $user->lastname  = $request->lastname;
        $user->address   = [
            'country' => @$user->address->country,
            'address' => $request->address,
            'state'   => $request->state,
            'zip'     => $request->zip,
            'city'    => $request->city,
        ];
        $user->profile_complete = 1;
        $user->save();

        $notify[] = ['success', 'Registration process completed successfully'];
        return to_route('user.home')->withNotify($notify);
    }

    public function referrals()
    {
        $pageTitle = 'Referrals';
        $user      = auth()->user();
        $maxLevel  = Referral::max('level');
        $directReferrals = $user->referrals()->latest()->paginate(getPaginate());

        $indirectReferralsIds = [];
        foreach ($user->referrals as $directRef) {
            $indirectReferralsIds = array_merge($indirectReferralsIds, $directRef->allReferrals()->pluck('id')->toArray());
        }
        $indirectReferrals = User::whereIn('id', $indirectReferralsIds)->latest()->paginate(getPaginate(), ['*'], 'indirect_page');

        return view($this->activeTemplate . 'user.referrals', compact('pageTitle', 'user', 'maxLevel', 'directReferrals', 'indirectReferrals'));
    }

    public function transferReferralBonus(Request $request)
    {
        $request->validate([
            'amount' => 'required|numeric|gt:0',
            'wallet' => 'required|in:deposit_wallet,interest_wallet',
        ]);

        $user = auth()->user();
        if ($user->referral_bonus < $request->amount) {
            $notify[] = ['error', 'Insufficient referral bonus balance'];
            return back()->withNotify($notify);
        }

        $user->referral_bonus -= $request->amount;
        $wallet = $request->wallet;
        $user->$wallet += $request->amount;
        $user->save();

        $trx = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = $request->amount;
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->trx          = $trx;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'referral_bonus_transfer';
        $transaction->details      = 'Transferred referral bonus to ' . keyToTitle($wallet);
        $transaction->post_balance = $user->$wallet;
        $transaction->save();

        $notify[] = ['success', 'Referral bonus transferred successfully'];
        return back()->withNotify($notify);
    }

    public function promotionalBanners()
    {
        $general = gs();
        $promotionCount = PromotionTool::count();
        if (!$general->promotional_tool || !$promotionCount) {
            abort(404);
        }
        $pageTitle    = 'Promotional Banners';
        $banners      = PromotionTool::orderBy('id', 'desc')->get();
        $emptyMessage = 'No banner found';
        return view($this->activeTemplate . 'user.promo_tools', compact('pageTitle', 'banners', 'emptyMessage'));
    }

    public function transferBalance()
    {
        $general = gs();
        if (!$general->b_transfer) {
            abort(404);
        }
        $pageTitle = 'Balance Transfer';
        $user      = auth()->user();
        return view($this->activeTemplate . 'user.balance_transfer', compact('pageTitle', 'user'));
    }

    public function transferBalanceSubmit(Request $request)
    {
        $general = gs();
        if (!$general->b_transfer) {
            abort(404);
        }
        $request->validate([
            'username' => 'required',
            'amount'   => 'required|numeric|gt:0',
            'wallet'   => 'required|in:deposit_wallet,interest_wallet',
        ]);

        $user = auth()->user();
        if ($user->username == $request->username) {
            $notify[] = ['error', 'You cannot transfer balance to your own account'];
            return back()->withNotify($notify);
        }

        $receiver = User::where('username', $request->username)->first();
        if (!$receiver) {
            $notify[] = ['error', 'Oops! Receiver not found'];
            return back()->withNotify($notify);
        }

        if ($user->ts) {
            $response = verifyG2fa($user, $request->authenticator_code);
            if (!$response) {
                $notify[] = ['error', 'Wrong verification code'];
                return back()->withNotify($notify);
            }
        }

        $general     = gs();
        $charge      = $general->f_charge + ($request->amount * $general->p_charge) / 100;
        $afterCharge = $request->amount + $charge;
        $wallet      = $request->wallet;

        if ($user->$wallet < $afterCharge) {
            $notify[] = ['error', 'You have no sufficient balance to this wallet'];
            return back()->withNotify($notify);
        }

        $user->$wallet -= $afterCharge;
        $user->save();

        $trx1                      = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $user->id;
        $transaction->amount       = getAmount($afterCharge);
        $transaction->charge       = $charge;
        $transaction->trx_type     = '-';
        $transaction->trx          = $trx1;
        $transaction->wallet_type  = $wallet;
        $transaction->remark       = 'balance_transfer';
        $transaction->details      = 'Balance transfer to ' . $receiver->username;
        $transaction->post_balance = getAmount($user->$wallet);
        $transaction->save();

        $receiver->deposit_wallet += $request->amount;
        $receiver->save();

        $trx2                      = getTrx();
        $transaction               = new Transaction();
        $transaction->user_id      = $receiver->id;
        $transaction->amount       = getAmount($request->amount);
        $transaction->charge       = 0;
        $transaction->trx_type     = '+';
        $transaction->trx          = $trx2;
        $transaction->wallet_type  = 'deposit_wallet';
        $transaction->remark       = 'balance_received';
        $transaction->details      = 'Balance received from ' . $user->username;
        $transaction->post_balance = getAmount($user->deposit_wallet);
        $transaction->save();

        notify($user, 'BALANCE_TRANSFER', [
            'amount'        => showAmount($request->amount),
            'charge'        => showAmount($charge),
            'wallet_type'   => keyToTitle($wallet),
            'post_balance'  => showAmount($user->$wallet),
            'user_fullname' => $receiver->fullname,
            'username'      => $receiver->username,
            'trx'           => $trx1,
        ]);

        notify($receiver, 'BALANCE_RECEIVE', [
            'wallet_type'  => 'Deposit wallet',
            'amount'       => showAmount($request->amount),
            'post_balance' => showAmount($receiver->deposit_wallet),
            'sender'       => $user->username,
            'trx'          => $trx2,
        ]);

        $notify[] = ['success', 'Balance transferred successfully'];
        return back()->withNotify($notify);
    }

    public function findUser(Request $request)
    {
        $user    = User::where('username', $request->username)->first();
        $message = null;
        if (!$user) {
            $message = 'User not found';
        }
        if (@$user->username == auth()->user()->username) {
            $message = 'You cannot send money to your own account';
        }
        return response(['message' => $message]);
    }

}
