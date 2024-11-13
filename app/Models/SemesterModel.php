<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class SemesterModel extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected $primaryKey = 'semester_id';

    protected $fillable = ['user_id', 'semester', 'created_at', 'updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
