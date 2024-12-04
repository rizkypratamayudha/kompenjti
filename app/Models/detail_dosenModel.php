<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class detail_dosenModel extends Model
{
    use HasFactory;

    protected $table = 'detail_dosen';
    protected $primaryKey = 'detail_dosen_id';

    protected $fillable = ['user_id','email','no_hp','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
    public function pekerjaan(): BelongsTo
    {
        return $this->belongsTo(PekerjaanModel::class, 'dosen_id', 'detail_dosen_id');
    }
}