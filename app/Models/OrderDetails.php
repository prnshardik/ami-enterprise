<?php

    namespace App\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class OrderDetails extends Authenticatable{

        use HasFactory, Notifiable;

        protected $table = 'orders_details';
        
        protected $fillable = ['order_id', 'product_id', 'quantity', 'rate', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
