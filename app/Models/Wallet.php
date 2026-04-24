<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Wallet extends Model
{
    protected $fillable = ['telegram_user_id','balance','currency'];
    protected $casts = ['balance' => 'decimal:2'];
}
