<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\PengumpulanModel;
use App\Models\ProgresModel;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;

class MahasiswaController extends Controller
{
    public function hapus(Request $request, $id)
    {
        $pengumpulan = PengumpulanModel::find($id);

        if (!$pengumpulan) {
            return response()->json([
                'status' => false,
                'message' => 'Data tidak ditemukan',
            ], 404);
        }

        // Delete file if exists
        if ($pengumpulan->bukti_pengumpulan && Storage::disk('public')->exists($pengumpulan->bukti_pengumpulan)) {
            Storage::disk('public')->delete($pengumpulan->bukti_pengumpulan);
        }

        $pengumpulan->delete();

        return response()->json([
            'status' => true,
            'message' => 'Data berhasil dihapus',
        ], 200);
    }

    public function store_link(Request $request)
    {
        // Validasi input yang diterima
        $validated = $request->validate([
            'progres_id' => 'required|exists:progres,progres_id', // pastikan progres_id ada di tabel progres
            'link' => 'required|url', // pastikan link yang dimasukkan valid
        ]);

        // Cari data progres berdasarkan progres_id
        $progres = ProgresModel::findOrFail($request->progres_id);

        // Cek jika pengumpulan sudah ada untuk progres ini
        if ($progres->pengumpulan_id) {
            return response()->json([
                'status' => false,
                'message' => 'Pengumpulan sudah ada untuk progres ini.',
            ], 400); // Menggunakan status code 400 untuk kesalahan permintaan
        }

        // Cek jika deadline sudah terlewati
        if ($progres->deadline && Carbon::now()->greaterThan(Carbon::parse($progres->deadline))) {
            return response()->json([
                'status' => false,
                'message' => 'Aksi tidak diperbolehkan, deadline sudah terlewati'
            ], 403); // Menggunakan status code 403 untuk forbidden
        }

        // Buat data pengumpulan baru
        $pengumpulan = new PengumpulanModel();
        $pengumpulan->progres_id = $request->progres_id;
        $pengumpulan->user_id = Auth::id(); // Mengambil ID pengguna yang sedang login
        $pengumpulan->bukti_pengumpulan = $request->link; // Menyimpan URL link sebagai bukti pengumpulan
        $pengumpulan->status = 'pending'; // Status awal pengumpulan adalah pending
        $pengumpulan->save(); // Simpan data pengumpulan ke database

        // Mengembalikan response JSON dengan status sukses
        return response()->json([
            'status' => true,
            'message' => 'Pengumpulan berhasil disimpan.',
            'data' => $pengumpulan // Menyertakan data pengumpulan yang baru dibuat
        ], 201); // Menggunakan status code 201 untuk success dengan resource yang baru dibuat
    }

    public function store_gambar(Request $request)
    {
        // Validate the incoming request
        $validated = $request->validate([
            'progres_id' => 'required|exists:progres,progres_id', // Ensure the progres_id exists in the progres table
            'image' => 'required|image|mimes:jpeg,png,jpg,gif|max:2048' // Ensure the file is a valid image and within size limit
        ]);

        // Find the progress data by progres_id
        $progres = ProgresModel::findOrFail($request->progres_id);

        // Check if a submission already exists for this progress
        if ($progres->pengumpulan_id) {
            return response()->json([
                'status' => false,
                'message' => 'Pengumpulan sudah ada untuk progres ini.'
            ], 400); // Return a 400 status code for bad request
        }

        // Check if the deadline has passed
        if ($progres->deadline && Carbon::now()->greaterThan(Carbon::parse($progres->deadline))) {
            return response()->json([
                'status' => false,
                'message' => 'Aksi tidak diperbolehkan, deadline sudah terlewati.'
            ], 403); // Return a 403 status code for forbidden
        }

        // Store the image file
        $imageName = $request->file('image')->getClientOriginalName();
        $imagePath = $request->file('image')->store('pengumpulan_gambar', 'public'); // Store the image in the public disk

        // Create a new submission record
        $pengumpulan = new PengumpulanModel();
        $pengumpulan->progres_id = $request->progres_id;
        $pengumpulan->user_id = Auth::id(); // Get the ID of the currently authenticated user
        $pengumpulan->bukti_pengumpulan = $imagePath; // Store the path of the uploaded image
        $pengumpulan->namaoriginal = $imageName; // Store the original image name
        $pengumpulan->status = 'pending'; // Set the initial status to pending
        $pengumpulan->save(); // Save the new submission

        // Return a success response
        return response()->json([
            'status' => true,
            'message' => 'Pengumpulan berhasil disimpan.'
        ], 201); // Return a 201 status code for resource created
    }

    public function store_file(Request $request)
{
    // Validate the incoming request
    $request->validate([
        'progres_id' => 'required|exists:progres,progres_id',
        'file' => 'required|mimes:pdf,xlsx,docx|max:2048'
    ]);

    // Find the corresponding progress entry
    $progres = ProgresModel::findOrFail($request->progres_id);

    // Check if there's already a submission for this progress
    if ($progres->pengumpulan_id) {
        return response()->json([
            'status' => false,
            'message' => 'Pengumpulan sudah ada untuk progres ini.'
        ], 400); // Bad Request
    }

    // Check if the deadline has passed
    if ($progres->deadline && Carbon::now()->greaterThan(Carbon::parse($progres->deadline))) {
        return response()->json([
            'status' => false,
            'message' => 'Aksi tidak diperbolehkan, deadline sudah terlewati.'
        ], 403); // Forbidden
    }

    // Store the uploaded file
    $file = $request->file('file');
    $fileName = $file->getClientOriginalName();
    $filePath = $file->store('pengumpulan_file', 'public'); // Store file in public storage

    // Create a new submission record
    $pengumpulan = new PengumpulanModel();
    $pengumpulan->progres_id = $request->progres_id;
    $pengumpulan->user_id = Auth::id(); // Get the currently authenticated user
    $pengumpulan->bukti_pengumpulan = $filePath;
    $pengumpulan->namaoriginal = $fileName;
    $pengumpulan->status = 'pending'; // Set the initial status to pending
    $pengumpulan->save();

    // Return a JSON response indicating success
    return response()->json([
        'status' => true,
        'message' => 'Pengumpulan berhasil disimpan.',
        'data' => $pengumpulan // Optionally return the saved submission data
    ], 201); // HTTP Status Code for Created
}
}
