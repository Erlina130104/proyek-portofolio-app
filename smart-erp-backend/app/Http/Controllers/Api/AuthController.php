<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash; // buat cek password terenkripsi
use Illuminate\Support\Facades\Validator; // buat validasi input

class AuthController extends Controller
{
    // -----------------------------
    // LOGIN USER
    // -----------------------------
    public function login(Request $request)
    {
        // Validasi input (email & password harus diisi)
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'password' => 'required'
        ]);

        // Kalau data salah / kosong
        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Cari user berdasarkan email
        $user = User::where('email', $request->email)->first();

        // Cek apakah user ada dan password cocok
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'success' => false,
                'message' => 'Email atau password salah'
            ], 401);
        }

        // Generate token login untuk user
        $token = $user->createToken('auth_token')->plainTextToken;

        // Kirim response berhasil
        return response()->json([
            'success' => true,
            'message' => 'Login berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,       // token untuk akses API
                'token_type' => 'Bearer' // standar format token
            ]
        ]);
    }

    // -----------------------------
    // REGISTER USER
    // -----------------------------
    public function register(Request $request)
    {
        // Validasi input pendaftaran
        $validator = Validator::make($request->all(), [
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email', // email harus unik
            'password' => 'required|min:6|confirmed' // harus ada password_confirmation
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        // Simpan user baru ke database
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password), // password dienkripsi
            'email_verified_at' => now() // otomatis verifikasi email
        ]);

        // Generate token otomatis setelah daftar
        $token = $user->createToken('auth_token')->plainTextToken;

        return response()->json([
            'success' => true,
            'message' => 'Registrasi berhasil',
            'data' => [
                'user' => $user,
                'token' => $token,
                'token_type' => 'Bearer'
            ]
        ], 201);
    }

    // -----------------------------
    // LOGOUT USER
    // -----------------------------
    public function logout(Request $request)
    {
        // Hapus token login
        $request->user()->currentAccessToken()->delete();

        return response()->json([
            'success' => true,
            'message' => 'Logout berhasil'
        ]);
    }

    // -----------------------------
    // AMBIL DATA USER YANG LAGI LOGIN
    // -----------------------------
    public function me(Request $request)
    {
        return response()->json([
            'success' => true,
            'data' => $request->user() // return data user yang login saat ini
        ]);
    }
}
