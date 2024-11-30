<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Tymon\JWTAuth\Contracts\JWTSubject;

class UserModel extends Authenticatable implements JWTSubject
{
    use HasFactory;

    public function getJWTIdentifier()
    {
            return $this->getKey();
    }

    public function getJWTCustomClaims()
    {
        return [];
    }
    protected $table = 'm_user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'user_id',
        'level_id',
        'username',
        'nama',
        'password',
        'avatar'
    ];
    protected function avatar(){
        return Attribute::make(
            get: fn ($avatar) => url('images/avatars/'.$avatar)
        );
    }
    protected $hidden = ['password'];

    protected $casts = ['password' => 'hashed'];

    public function level(): BelongsTo
    {
        return $this->belongsTo(LevelModel::class, 'level_id');
    }
    public function jamKompen(): BelongsTo
    {
        return $this->belongsTo(jamKompenModel::class, 'jam_kompen_id');
    }

    public function pekerjaan():BelongsTo{
        return $this->belongsTo(PekerjaanModel::class,'user_id');
    }
    public function kompetensi():HasMany{
        return $this->hasMany(kompetensiModel::class,'user_id');
    }

    public function periode()
{
    return $this->hasOneThrough(
        PeriodeModel::class,
        detail_mahasiswaModel::class,
        'user_id',
        'periode_id',
        'user_id',
        'periode_id'
    );
}


    public function getRoleName()
    {
        return $this->level->level_nama;
    }

    public function getUsername()
    {
        return $this->username;
    }

    public function hasRole($role)
    {
        return $this->level->kode_level == $role;
    }

    public function getRole()
    {
        return $this->level->kode_level;
    }

     // Relasi ke tabel detail_mahasiswa
public function detailMahasiswa()
{
    return $this->hasOne(detail_mahasiswaModel::class, 'user_id', 'user_id');
}

// Relasi ke tabel detail_dosen
public function detailDosen()
{
    return $this->hasOne(detail_dosenModel::class, 'user_id', 'user_id');
}

// Relasi ke tabel detail_kaprodi
public function detailKaprodi()
{
    return $this->hasOne(detail_kaprodiModel::class, 'user_id', 'user_id');
}
// Relasi ke tabel profile untuk mengambil avatar
public function profile()
{
    return $this->hasOne(ProfileModel::class, 'user_id', 'user_id');
}


}
