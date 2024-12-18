<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProdiModel extends Model
{
    use HasFactory;

    protected $table = 'prodi';
    protected $primaryKey = 'prodi_id';

    protected $fillable = ['prodi_nama'];

    public static function getProdiNama($prodiId)
    {
        $prodi = self::find($prodiId);
        return $prodi ? $prodi->prodi_nama : null;
    }
}
