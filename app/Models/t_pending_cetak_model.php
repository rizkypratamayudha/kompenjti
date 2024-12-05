<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class t_pending_cetak_model extends Model
{
    use HasFactory;

    protected $table = 't_pending_cetak';
    protected $primaryKey = 't_pending_cetak_id';

    protected $fillable = ['t_pending_cetak_id','user_id','pekerjaan_id','created_at','updated_at'];

    public function user(){
        return $this->belongsTo(UserModel::class,'user_id','user_id');
    }

    public function pekerjaan(){
        return $this->belongsTo(PekerjaanModel::class,'pekerjaan_id','pekerjaan_id');
    }
}
