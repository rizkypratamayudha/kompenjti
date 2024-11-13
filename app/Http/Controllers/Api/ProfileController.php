<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;

class ProfileController extends Controller
{
    public function updatePhoto(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'avatar' => 'required|image|mimes:jpeg,png,jpg|max:2048', // Validasi file gambar
        ]);

        if ($validator->fails()) {
            return response()->json(['errors' => $validator->errors()], 422);
        }

        $user = $request->user();

        // Hapus foto profil lama jika ada
        if ($user->avatar) {
            Storage::delete($user->avatar);
        }

        // Simpan foto profil baru
        $avatarPath = $request->file('avatar')->store('avatars');
        $user->avatar = $avatarPath;
        $user->save();

        return response()->json([
            'status' => true,
            'message' => 'Foto profil berhasil diupdate',
            'avatar_url' => Storage::url($avatarPath),
        ], 200);
    }

    public function updatePassword(Request $request)
{
    $validator = Validator::make($request->all(), [
        'current_password' => 'required',
        'password' => 'required|min:5|confirmed',
    ]);

    if ($validator->fails()) {
        return response()->json(['errors' => $validator->errors()], 422);
    }

    $user = $request->user();

    // Ensure the user is not null before proceeding
    if (!$user) {
        return response()->json(['error' => 'User not found'], 404);
    }

    // Check if the current password matches the hashed password
    if (!Hash::check($request->current_password, $user->password)) {
        return response()->json(['error' => 'Incorrect current password'], 401);
    }

    // Update the password
    $user->password = Hash::make($request->password);
    $user->save();

    return response()->json(['message' => 'Password updated successfully'], 200);
}

    public function deleteAvatar(Request $request)
    {
        $user = $request->user();

        // Hapus avatar jika ada
        if ($user->avatar) {
            Storage::delete($user->avatar);
            $user->avatar = null;
            $user->save();

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