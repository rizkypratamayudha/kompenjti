<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PendingRegister extends Model
{
    protected $table = 't_pending_register';
    protected $primaryKey = 'user_id';

    protected $fillable = ['user_id','level_id','username','nama','password','email','no_hp','angkatan','prodi_id','created_at','updated_at'];

    protected $hidden = ['password'];

    protected $casts = ['password'=>'hashed'];

    public function level():BelongsTo{
        return $this->belongsTo(levelmodel::class,'level_id', 'level_id');
    }
}
