<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kompetensi_dosenModel extends Model
{
    use HasFactory;

    protected $table = 'kompetensi_dosen';
    protected $primaryKey = 'kompetensi_dosen_id';
    protected $fillable = ['detail_pekerjaan_id','kompetensi_admin_id'];

    public function kompetensiAdmin(){
        return $this->belongsTo(kompetensi_adminModel::class,'kompetensi_admin_id','kompetensi_admin_id');
    }

    public function detailPekerjaan(){
        return $this->belongsTo(detail_pekerjaanModel::class,'detail_pekerjaan_id','detail_pekerjaan_id');
    }
}
