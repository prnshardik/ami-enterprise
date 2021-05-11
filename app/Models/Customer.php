<?php

    namespace App\Models;

    use Illuminate\Contracts\Auth\MustVerifyEmail;
    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Foundation\Auth\User as Authenticatable;
    use Illuminate\Notifications\Notifiable;

    class Customer extends Authenticatable{

        use HasFactory, Notifiable;

        protected $table = 'customers';
        
        protected $fillable = ['party_name', 'billing_name', 'contact_person', 'mobile_number', 'billing_address', 'delivery_address',
                                'electrician', 'electrician_number', 'architect', 'architect_number', 'office_contact_person', 
                                'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
