<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class InvestmentCode extends Model
{
    protected $fillable = [
        'code',
        'status',
        'created_by',
        'max_users',
        'valid_hours',
        'expires_at',
    ];

    protected $casts = [
        'expires_at' => 'datetime',
    ];

    public function redemptions()
    {
        return $this->hasMany(UserInvestmentCode::class);
    }
}
