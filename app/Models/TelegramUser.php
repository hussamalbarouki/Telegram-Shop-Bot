<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class TelegramUser extends Model
{
    protected $fillable = [
        'telegram_id','username','first_name','last_name','language_code','phone_number','is_bot','raw_json','first_seen_at','last_seen_at','is_blocked'
    ];
    protected $casts = ['raw_json' => 'array','is_bot' => 'boolean','is_blocked' => 'boolean','first_seen_at' => 'datetime','last_seen_at' => 'datetime'];
}
