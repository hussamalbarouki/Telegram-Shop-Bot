<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Order extends Model
{
    protected $fillable = ['telegram_user_id','status','subtotal','discount_total','total','currency','address','phone_number','customer_note','admin_note','tracking_number','customer_message','paid_at'];
    protected $casts = ['subtotal'=>'decimal:2','discount_total'=>'decimal:2','total'=>'decimal:2','paid_at'=>'datetime'];
}
