<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Admin extends Model
{
    protected $fillable = ['name', 'email', 'password', 'is_active', 'last_login_at'];
    protected $hidden = ['password'];
    protected $casts = ['is_active' => 'boolean', 'last_login_at' => 'datetime'];
}
