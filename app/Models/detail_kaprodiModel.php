<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class detail_kaprodiModel extends Model
{
    use HasFactory;

    protected $table = 'detail_kaprodi';
    protected $primaryKey = 'detail_kaprodi_id';
    protected $fillable = ['user_id','email','no_hp','created_at','updated_at'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(UserModel::class, 'user_id');
    }
}
