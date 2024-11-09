<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class SemesterModel extends Model
{
    use HasFactory;

    protected $table = 'semester';

    protected $primaryKey = 'semester_id';

    protected $fillable = ['semester', 'created_at', 'updated_at'];
}
