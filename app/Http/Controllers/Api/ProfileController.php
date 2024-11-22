<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use App\Models\ProfileModel;


class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        // Validasi file upload
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048',
        ]);
    
        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }
    
        // Mendapatkan user yang sedang login
        $user = $request->user();
    
        // Hapus avatar lama jika ada
        if ($user->avatar) {
            Storage::disk('public')->delete($user->avatar);
        }
    
        // Simpan avatar baru di folder 'avatars' pada disk 'public'
        $avatarPath = $request->file('avatar')->store('avatars', 'public');
    
        // Perbarui avatar di database
        $user->update(['avatar' => $avatarPath]);
    
        // Buat URL publik untuk avatar
        $avatarUrl = asset('storage/' . $avatarPath);
    
        return response()->json([
            'status' => true,
            'message' => 'Foto profil berhasil diupdate',
            'avatar_url' => $avatarUrl,
        ], 200);
    }
    
    
public function updatePassword(Request $request)
{
    $input = $request->all();

    $validator = Validator::make($input, [
        'current_password' => 'required',
        'password' => 'required|min:5|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json([
            'success' => false,
            'errors' => $validator->errors()
        ], 422);
    }

    $user = $request->user();

    if (!Hash::check($request->input('current_password'), $user->password)) {
        return response()->json([
            'success' => false,
            'error' => 'Incorrect current password'
        ], 401);
    }

    $user->password = Hash::make($request->input('password'));
    $user->save();

    return response()->json([
        'success' => true,
        'message' => 'Password updated successfully'
    ], 200);
}

    // Fungsi untuk hapus foto profil
    public function deleteAvatar(Request $request)
    {
        $user = $request->user();
        $profile = $user->profile;

        if ($profile && $profile->avatar) {
            Storage::delete($profile->avatar);
            $profile->avatar = null;
            $profile->save();

            return response()->json([
                'status' => true,
                'message' => 'Foto profil berhasil dihapus',
            ], 200);
        }

        return response()->json([
            'status' => false,
            'message' => 'Tidak ada foto profil untuk dihapus',
        ], 400);
    }
}