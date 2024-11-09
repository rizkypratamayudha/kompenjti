<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class jamKompenModel extends Model
{
    use HasFactory;

    protected $table = 'jam_kompen';

    protected $primaryKey = 'jam_kompen_id';

    protected $fillable = ['user_id','semester_id','akumulasi_jam', 'created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }

    public function semester(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'semester_id');
    }
}
