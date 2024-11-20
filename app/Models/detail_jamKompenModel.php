<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class detail_jamKompenModel extends Model
{
    use HasFactory;

    protected $table = 'detail_jam_kompen';

    protected $primaryKey = 'detail_jam_kompen_id';

    protected $fillable = ['jam_kompen_id', 'matkul_id','jumlah_jam', 'created_at','updated_at'];

    public function jamKompen(): BelongsTo
    {
        return $this->belongsTo(jamKompenModel::class, 'jam_kompen_id');
    }
    public function matkul(): BelongsTo
    {
        return $this->belongsTo(MatkulModel::class, 'matkul_id');
    }
}
