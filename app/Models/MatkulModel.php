<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class MatkulModel extends Model
{
    use HasFactory;

    protected $table = 'matkul';

    protected $primaryKey = 'matkul_id';

    protected $fillable = ['matkul_kode','matkul_nama', 'created_at','updated_at'];

}
