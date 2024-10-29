<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PendingRegister extends Model
{
    protected $table = 't_pending_register';
    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id','level_id','username','nama','password','created_at','updated_at'];

    protected $hidden = ['password'];

    protected $casts = ['password'=>'hashed'];
}
