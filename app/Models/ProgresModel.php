<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProgresModel extends Model
{
    use HasFactory;

    protected $table = 'progres';
    protected $primaryKey = 'progres_id';
    protected $fillable = ['progres_id','pekerjaan_id','pengumpulan_id','judul_progres','jam_kompen','status'];
}
