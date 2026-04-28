<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInvestmentCode extends Model
{
    protected $fillable = [
        'user_id',
        'investment_code_id',
        'redeemed_at',
    ];

    protected $casts = [
        'redeemed_at' => 'datetime',
    ];

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function investmentCode()
    {
        return $this->belongsTo(InvestmentCode::class);
    }
}

