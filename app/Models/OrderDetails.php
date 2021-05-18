<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class OrderDetails extends Model{
        
        use HasFactory;

        protected $table = 'orders_details';
        
        protected $fillable = ['order_id', 'product_id', 'quantity', 'rate', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
