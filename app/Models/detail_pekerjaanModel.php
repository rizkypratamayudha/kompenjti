<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class detail_pekerjaanModel extends Model
{
    use HasFactory;

    protected $table = 'detail_pekerjaan';
    protected $primaryKey = 'detail_pekerjaan_id';
    protected $fillable = ['detail_pekerjaan_id','pekerjaan_id','jumlah_anggota','persyaratan','deskripsi_tugas'];

    public function pekerjaan():BelongsTo{
        return $this->belongsTo(PekerjaanModel::class,'pekerjaan_id','pekerjaan_id');
    }
}
