<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\kompetensiModel;

class KompetensiController extends Controller
{
    // Mengambil data kompetensi berdasarkan user_id
    public function index($user_id)
    {
        $kompetensi = KompetensiModel::where('user_id', $user_id)->get();
        return response()->json($kompetensi);
    }

    // Menyimpan data kompetensi baru
    public function store(Request $request)
    {
        $validatedData = $request->validate([
            'user_id' => 'required|integer',
            'semester_id' => 'required|integer',
            'kompetensi_nama' => 'required|string',
            'pengalaman' => 'required|string',
            'bukti' => 'nullable|string',
        ]);

        $kompetensi = KompetensiModel::create($validatedData);
        return response()->json(['message' => 'Kompetensi berhasil disimpan', 'kompetensi' => $kompetensi]);
    }
}
