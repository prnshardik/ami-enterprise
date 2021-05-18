<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class task extends Model{
    	
        use HasFactory;

        protected $table = 'task';
        
        protected $fillable = ['title', 'user_id', 'description', 'target_date', 'attechment', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
        