<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class kompetensiModel extends Model
{
    use HasFactory;

    protected $table = 'kompetensi';

    protected $primaryKey = 'kompetensi_id';

    protected $fillable = [
        'user_id',
        'kompetensi_admin_id',
        'pengalaman',
        'bukti',
        'created_at',
        'upload_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id','user_id');
    }

    public function kompetensiAdmin(){
        return $this->belongsTo(kompetensi_adminModel::class,'kompetensi_admin_id','kompetensi_admin_id');
    }
}
