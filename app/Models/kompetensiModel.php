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
        'semester_id',
        'kompetensi_nama',
        'pengalaman',
        'bukti',
        'created_at',
        'upload_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(SemesterModel::class, 'semester_id');
    }
}
