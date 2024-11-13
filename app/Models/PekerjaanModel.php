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
    protected $fillable = ['user_id','jenis_pekerjaan','pekerjaan_nama','jumlah_jam_kompen','status','created_at','updated_at'];

    public function user(): BelongsTo{
        return $this->belongsTo(UserModel::class,'user_id');
    }

    
}
