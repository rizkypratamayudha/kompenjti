<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProgresModel extends Model
{
    use HasFactory;

    protected $table = 'progres';
    protected $primaryKey = 'progres_id';
    protected $fillable = ['progres_id','pekerjaan_id','pengumpulan_id','judul_progres','jam_kompen','status','hari', 'deadline'];

    public function pekerjaan():BelongsTo{
        return $this->belongsTo(PekerjaanModel::class,'pekerjaan_id','pekerjaan_id');
    }

    public function pengumpulan(){
        return $this->hasMany(PengumpulanModel::class,'pengumpulan_id','pengumpulan_id');
    }
}
