<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class detail_mahasiswaModel extends Model
{
    use HasFactory;

    protected $table = 'detail_mahasiswa';

    protected $primaryKey = 'detail_mahasiswa_id';

    protected $fillable = ['user_id','email','no_hp','angkatan','prodi_id','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function prodi(): BelongsTo
    {
        return $this->belongsTo(ProdiModel::class, 'prodi_id');
    }
}
