<?php

    namespace App\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class Order extends Authenticatable{

        use HasFactory, Notifiable;

        protected $table = 'orders';
        
        protected $fillable = ['name', 'order_date', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
