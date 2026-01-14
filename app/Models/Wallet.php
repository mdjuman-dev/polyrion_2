<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    public $timestamps = true;
    
    protected $fillable = ['user_id', 'wallet_type', 'balance', 'currency', 'locked_balance', 'status'];

    protected $casts = [
        'balance' => 'decimal:2',
        'locked_balance' => 'decimal:2',
        'last_transaction_at' => 'datetime',
    ];

    // Wallet types
    const TYPE_MAIN = 'main';
    const TYPE_EARNING = 'earning';

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function transactions()
    {
        return $this->hasMany(WalletTransaction::class);
    }
}