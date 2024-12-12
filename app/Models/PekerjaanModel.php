<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Facades\Auth;

class PekerjaanModel extends Model
{
    use HasFactory;

    protected $table = 'pekerjaan';
    protected $primaryKey = 'pekerjaan_id';
    protected $fillable = ['user_id', 'jenis_pekerjaan', 'pekerjaan_nama', 'jumlah_jam_kompen', 'status', 'akumulasi_deadline', 'created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id', 'user_id');
    }

    public function detail_pekerjaan()
    {
        return $this->belongsTo(detail_pekerjaanModel::class, 'pekerjaan_id', 'pekerjaan_id');
    }
    public function progres(): HasMany
    {
        return $this->hasMany(ProgresModel::class, 'pekerjaan_id', 'pekerjaan_id');
    }

    public function persyaratan()
    {
        return $this->hasManyThrough(PersyaratanModel::class, detail_pekerjaanModel::class, 'pekerjaan_id', 'detail_pekerjaan_id', 'pekerjaan_id', 'detail_pekerjaan_id');
    }

    public function kompetensiDosen()
    {
        return $this->hasManyThrough(kompetensi_dosenModel::class, detail_pekerjaanModel::class);
    }

    public function approve()
    {
        return $this->belongsTo(ApprovePekerjaanModel::class, 'pekerjaan_id', 'pekerjaan_id');
    }

    public function getCanRequestSuratAttribute()
{
    // Periksa apakah semua progres pekerjaan ini sudah dikumpulkan dengan status bukan 'pending'
    $progresSelesai = $this->progres->every(function ($progres) {
        return $progres->pengumpulan->isNotEmpty() &&
            $progres->pengumpulan->first()->status !== 'pending';
    });

    // Periksa apakah akumulasi_deadline sudah melewati tanggal saat ini
    $deadlineTerlewati = $this->akumulasi_deadline < now();

    // Request Cetak Surat diperbolehkan jika salah satu kondisi terpenuhi
    return $progresSelesai || $deadlineTerlewati;
}


    public function t_approve_pekerjaan()
    {
        return $this->hasMany(ApprovePekerjaanModel::class, 'pekerjaan_id');
    }
    public function t_pending_pekerjaan()
    {
        return $this->hasMany(PendingPekerjaanModel::class, 'pekerjaan_id');
    }
}
