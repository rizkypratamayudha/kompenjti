<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PengumpulanModel extends Model
{
    use HasFactory;

    protected $table = 'pengumpulan';

    protected $primaryKey = 'pengumpulan_id';
    protected $fillable = ['pengumpulan_id','user_id','progres_id','bukti_pengumpulan','status','created_at','updated_at'];

    public function user(){
        return $this->belongsTo(UserModel::class,'user_id','user_id');
    }

    public function progres(){
        return $this->belongsTo(PengumpulanModel::class,'progres_id','progres_id');
    }
}
