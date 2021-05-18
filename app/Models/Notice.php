<?php

    namespace App\Models;

    use Illuminate\Database\Eloquent\Factories\HasFactory;
    use Illuminate\Database\Eloquent\Model;

    class Notice extends Model{

        use HasFactory;

        protected $table = 'notices';

        protected $fillable = ['title', 'description', 'created_by', 'created_at', 'updated_by', 'updated_at'];
    }
