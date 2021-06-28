<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class PaymentAssign extends Model{
        
        protected $table = 'payment_assign';
        
        protected $fillable = ['user_id', 'party_name', 'date', 'note', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
