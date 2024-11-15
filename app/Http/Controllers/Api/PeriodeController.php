<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PeriodeModel;
use Illuminate\Http\Request;

class PeriodeController extends Controller
{
    // Fetch all periode records
    public function index()
    {
        $periode = PeriodeModel::all();
        return response()->json($periode);
    }

    // Fetch specific periode by periode_id
    public function show($periode_id)
    {
        $periode = PeriodeModel::find($periode_id);

        if ($periode) {
            return response()->json($periode);
        } else {
            return response()->json(['message' => 'Periode not found'], 404);
        }
    }
}
