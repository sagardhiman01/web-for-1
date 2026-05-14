<?php

namespace App\Models;

use App\Traits\Searchable;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Laravel\Sanctum\HasApiTokens;

class User extends Authenticatable
{
    use HasApiTokens, Searchable;

    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token','ver_code',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'address' => 'object',
        'kyc_data' => 'object',
        'ver_code_send_at' => 'datetime'
    ];


    public function loginLogs()
    {
        return $this->hasMany(UserLogin::class);
    }

    public function transactions()
    {
        return $this->hasMany(Transaction::class)->orderBy('id','desc');
    }

    public function deposits()
    {
        return $this->hasMany(Deposit::class)->where('status','!=',0);
    }

    public function withdrawals()
    {
        return $this->hasMany(Withdrawal::class)->where('status','!=',0);
    }

    public function referrer()
    {
        return $this->belongsTo(User::class,'ref_by');
    }

    public function referrals()
    {
        return $this->hasMany(User::class,'ref_by');
    }

    public function allReferrals(){
        return $this->referrals()->with('referrer');
    }

    public function invests()
    {
        return $this->hasMany(Invest::class)->orderBy('id','desc');
    }

    public function fullname(): Attribute
    {
        return new Attribute(
            get: fn () => $this->firstname . ' ' . $this->lastname,
        );
    }

    public function balance(): Attribute
    {
        return new Attribute(
            get: fn () => $this->deposit_wallet + $this->interest_wallet,
        );
    }

    public static function generateReferralCode(): string
    {
        do {
            $code = str_pad((string) random_int(0, 999999), 6, '0', STR_PAD_LEFT);
        } while (self::where('referral_code', $code)->exists());

        return $code;
    }

    public function totalTeamCount()
    {
        $count = 0;
        $referrals = $this->referrals;
        foreach ($referrals as $user) {
            $count += 1 + $user->totalTeamCount();
        }
        return $count;
    }

    public function checkLevelRewards()
    {
        $directCount = $this->referrals()->count();
        $totalTeam = $this->totalTeamCount();
        $indirectCount = $totalTeam - $directCount;
        
        $levels = [
            1 => ['direct' => 10, 'indirect' => 5, 'reward' => 50],
            2 => ['direct' => 20, 'indirect' => 20, 'reward' => 100],
            3 => ['direct' => 40, 'indirect' => 80, 'reward' => 200],
            4 => ['direct' => 80, 'indirect' => 150, 'reward' => 400],
            5 => ['direct' => 200, 'indirect' => 500, 'reward' => 1000],
        ];

        $achieved = json_decode($this->achieved_rewards ?? '[]', true);
        $newRank = $this->rank;

        foreach ($levels as $lvl => $data) {
            if ($directCount >= $data['direct'] && $indirectCount >= $data['indirect']) {
                if (!in_array($lvl, $achieved)) {
                    // Reward user
                    $this->deposit_wallet += $data['reward'];
                    $achieved[] = $lvl;
                    $newRank = $lvl;
                    
                    // Log transaction
                    $transaction = new Transaction();
                    $transaction->user_id = $this->id;
                    $transaction->amount = $data['reward'];
                    $transaction->post_balance = $this->deposit_wallet;
                    $transaction->charge = 0;
                    $transaction->trx_type = '+';
                    $transaction->details = "Level $lvl Referral Reward Achieved";
                    $transaction->trx = getTrx();
                    $transaction->wallet_type = 'deposit_wallet';
                    $transaction->remark = 'level_reward';
                    $transaction->save();
                }
            }
        }

        $this->achieved_rewards = json_encode($achieved);
        $this->rank = $newRank;
        $this->save();
    }

    // SCOPES
    public function scopeActive()
    {
        return $this->where('status', 1)->where('ev', '1')->where('sv', 1);
    }

    public function scopeBanned()
    {
        return $this->where('status', 0);
    }

    public function scopeEmailUnverified()
    {
        return $this->where('ev', 0);
    }

    public function scopeMobileUnverified()
    {
        return $this->where('sv', 0);
    }

    public function scopeKycUnverified()
    {
        return $this->where('kv', 0);
    }

    public function scopeKycPending()
    {
        return $this->where('kv', 2);
    }

    public function scopeEmailVerified()
    {
        return $this->where('ev', 1);
    }

    public function scopeMobileVerified()
    {
        return $this->where('sv', 1);
    }

    public function scopeWithBalance()
    {
        return $this->where(function($userWallet){
            $userWallet->where('deposit_wallet', '>' , 0)->orWhere('interest_wallet', '>', 0);
        });
    }

    public function deviceTokens(){
        return $this->hasMany(DeviceToken::class);
    }

}
