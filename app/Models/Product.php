<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class Product extends Authenticatable
{
    use HasFactory, Notifiable;

    protected $table = 'products';
    protected $fillable = [
        'name',
        'created_by',
        'created_at',
        'updated_by',
        'updated_at',
    ];

    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    
}
