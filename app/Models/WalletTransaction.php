<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class WalletTransaction extends Model
{
    protected $fillable = ['telegram_user_id','type','amount','balance_before','balance_after','reference_type','reference_id','note','created_by'];
    protected $casts = ['amount' => 'decimal:2','balance_before' => 'decimal:2','balance_after' => 'decimal:2'];
}
