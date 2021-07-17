<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class PaymentReminder extends Model{
        
        protected $table = 'payment_reminder';
        
        protected $fillable = ['user_id', 'party_name', 'note', 'mobile_no', 'date', 'next_date', 'next_time', 'is_last', 'amount', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
