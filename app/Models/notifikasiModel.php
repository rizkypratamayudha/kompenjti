<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class notifikasiModel extends Model
{
    use HasFactory;

    protected $table = 'notifikasi';
    protected $primaryKey ='notifikasi_id';
    protected $fillable = ['notifikasi_id','user_id','pekerjaan_id','pesan','status','user_id_kap','created_at','updated_at'];

    public function user(){
        return $this->belongsTo(UserModel::class,'user_id','user_id');
    }

    public function kaprodi(){
        return $this->belongsTo(UserModel::class,'user_id_kap','user_id');
    }

    public function pekerjaan()
    {
        return $this->belongsTo(PekerjaanModel::class, 'pekerjaan_id', 'pekerjaan_id');
    }
}
