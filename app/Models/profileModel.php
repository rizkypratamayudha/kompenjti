<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ProfileModel extends Model
{
    use HasFactory;

    protected $table = 'profile';
    protected $primaryKey = 'profil_id';

    protected $fillable = [
        'avatar',
        'user_id',
        'kompetensi_id',
    ];

    // Relasi ke model UserModel
    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    // Relasi ke model KompetensiModel (asumsikan ada model ini)
    public function kompetensi(): BelongsTo
    {
        return $this->belongsTo(KompetensiModel::class, 'kompetensi_id');
    }

    // Accessor untuk avatar URL
    public function getAvatarUrlAttribute()
    {
        return $this->avatar ? url('storage/' . $this->avatar) : url('storage/default.png');
    }
    
}