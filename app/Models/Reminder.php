<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Reminder extends Model{
        use HasFactory;

        protected $table = 'reminders';

        protected $fillable = ['title', 'date_time' ,'note', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
