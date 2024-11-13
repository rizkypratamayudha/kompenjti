<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\SemesterModel;
use Illuminate\Http\Request;

class SemesterController extends Controller
{
    // Mengambil data semester berdasarkan user_id
    public function getSemesterByUserId($user_id)
    {
        $semester = SemesterModel::where('user_id', $user_id)->first();

        if ($semester) {
            return response()->json($semester);
        } else {
            return response()->json(['message' => 'Semester not found'], 404);
        }
    }
}
