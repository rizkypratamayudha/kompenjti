<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class ApprovePekerjaanModel extends Model
{
    use HasFactory;

    protected $table = 't_approve_pekerjaan';
    protected $primarykey = 't_approve_pekerjaan_id';

    protected $fillable = ['t_approve_pekerjaan_id','user_id','pekerjaan_id'];

    public function user():BelongsTo{
        return $this->belongsTo(UserModel::class,'user_id','user_id');
    }

    public function pekerjaan():BelongsTo{
        return $this->belongsTo(PekerjaanModel::class,'pekerjaan_id','pekerjaan_id');
    }
}
