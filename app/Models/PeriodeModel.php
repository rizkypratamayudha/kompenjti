<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PeriodeModel extends Model
{
    use HasFactory;

    protected $table = 'periode';

    protected $primaryKey = 'periode_id';
    protected $fillable = ['periode_id', 'periode_nama', 'created_at', 'updated_at'];


    public static function getPeriodeNama($periodeId)
    {
        $periode = self::find($periodeId);
        return $periode ? $periode->periode_nama : null;
    }
}