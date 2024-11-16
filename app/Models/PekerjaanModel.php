<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PekerjaanModel extends Model
{
    use HasFactory;

    protected $table = 'pekerjaan';
    protected $primaryKey = 'pekerjaan_id';
    protected $fillable = ['user_id','jenis_pekerjaan','pekerjaan_nama','jumlah_jam_kompen','status', 'akumulasi_deadline', 'created_at','updated_at'];

    public function user(): BelongsTo{
        return $this->belongsTo(UserModel::class,'user_id','user_id');
    }

    public function detail_pekerjaan():BelongsTo{
        return $this->belongsTo(detail_pekerjaanModel::class,'pekerjaan_id','pekerjaan_id');
    }
    public function progres():BelongsTo{
        return $this->belongsTo(ProgresModel::class,'pekerjaan_id','pekerjaan_id');
    }

}
