<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Order extends Model{

        use HasFactory;

        protected $table = 'orders';
        
        protected $fillable = ['name', 'order_date', 'file', 'remark', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
