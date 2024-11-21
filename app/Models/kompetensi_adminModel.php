<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class kompetensi_adminModel extends Model
{
    use HasFactory;
    protected $table = 'kompetensi_admin';
    protected $primaryKey = 'kompetensi_admin_id';
    protected $fillable = ['kompetensi_admin_id','kompetensi_nama'];

    
}
