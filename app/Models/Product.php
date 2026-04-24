<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Product extends Model
{
    protected $fillable = ['category_id','name','slug','short_description','full_description','price','price_before_discount','type','status','stock','sku','sort_order','is_featured','sizes','colors','material','weight','expected_delivery_time','notes_before_purchase','min_qty','max_qty','requires_address','requires_phone','requires_note'];
    protected $casts = ['price'=>'decimal:2','price_before_discount'=>'decimal:2','is_featured'=>'boolean','sizes'=>'array','colors'=>'array','requires_address'=>'boolean','requires_phone'=>'boolean','requires_note'=>'boolean'];
}
