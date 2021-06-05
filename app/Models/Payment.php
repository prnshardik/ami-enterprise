<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Payment extends Model{
        
        public $timestamps = false;

        protected $table = 'payments';
        
        protected $fillable = ['party_name', 'bill_no', 'bill_date', 'due_days', 'bill_amount', 'balance_amount', 'mobile_no'];
    }
