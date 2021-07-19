<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PurchaseOrder extends Model
{
    use HasFactory;
    protected $table = 'purchase_orders';
        
    protected $fillable = ['name', 'order_date', 'file', 'remark', 'created_by', 'created_at', 'updated_by', 'updated_at'];
}
