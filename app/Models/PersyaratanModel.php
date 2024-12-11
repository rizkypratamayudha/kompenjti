<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class PersyaratanModel extends Model
{
    use HasFactory;

    protected $table = 'persyaratan';
    protected $primaryKey = 'persyaratan_id';
    protected $fillable = ['persyaratan_id','detail_pekerjaan_id','persyaratan_nama'];

    public function detail_pekerjaan():BelongsTo{
        return $this->belongsTo(detail_pekerjaanModel::class,'detail_pekerjaan_id','detail_pekerjaan_id');
    }
}
