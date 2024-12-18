<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class jamKompenModel extends Model
{
    use HasFactory;

    protected $table = 'jam_kompen';

    protected $primaryKey = 'jam_kompen_id';

    protected $fillable = ['user_id','periode_id','akumulasi_jam', 'created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function periode(): BelongsTo
    {
        return $this->belongsTo(PeriodeModel::class, 'periode_id');
    }

    public function detail_jamKompen(): HasMany
    {
        return $this->hasMany(detail_jamKompenModel::class, 'jam_kompen_id','jam_kompen_id');
    }
}
