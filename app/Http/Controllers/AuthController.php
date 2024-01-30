<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\Password;
use Illuminate\Foundation\Auth\SendsPasswordResetEmails;
use Illuminate\Support\Facades\Mail;
use App\Mail\HelloWorldEmail;
use App\Models\User;

class AuthController extends Controller
{
    // Menggunakan trait HasApiTokens dari Sanctum untuk otentikasi API nya
    use HasApiTokens;

    // Fungsi untuk mendaftarkan pengguna baru
    public function register(Request $request)
    {
        // Validasi input pengguna, seperti yang ada dibawah ini reqiured berarti harus,, string dan minimal untuk password 8 karakter dsb.
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|string|email|max:255|unique:users',
            'password' => 'required|string|min:8',
        ]);

        // Membuat pengguna baru berdasarkan input
        $user = User::create([
            'name' => $request->input('name'),
            'email' => $request->input('email'),
            'password' => Hash::make($request->input('password')),
        ]);

        // Mengembalikan respons JSON dengan informasi pengguna yang baru dibuat
        return response()->json([
            'success' => true,
            'data' => [
                'user' => $user,
            ],
        ], 201);
    }

    // Fungsi untuk proses login pengguna
    public function login(Request $request)
    {
        // Mengambil kredensial email dan password dari request
        $credentials = $request->only('email', 'password');

        // Melakukan proses login menggunakan metode Auth::attempt
        // Auth::attempt($credentials) digunakan untuk mencoba melakukan login menggunakan kredensial yang diberikan.
        // Jika kredensial sesuai dengan data yang ada di database, maka user dianggap berhasil login.
        if (Auth::attempt($credentials)) {
            // Jika login berhasil, membuat token baru untuk otentikasi
            $user = Auth::user();
            $token = $user->createToken('authToken')->plainTextToken;
            $user['token'] = $token;

            // Mengembalikan respons JSON dengan informasi pengguna dan token
            return response()->json([
                'success' => true,
                'data' => $user
            ], 200);
        }

        // Jika login gagal, mengembalikan respons JSON dengan pesan error
        return response()->json([
            'success' => false,
            'data' => [
                "message" => 'Wrong email or password'
            ]
        ], 200);
    }

    // Fungsi untuk proses logout pengguna
    public function logout(Request $request)
    {
        // Mendapatkan pengguna dari request dan menghapus semua token yang dimilikinya
        $user = $request->user();
        $user->tokens()->delete();

        // Mengembalikan respons JSON dengan pesan logout
        return response()->json(['message' => 'Logged out'], 200);
    }

    // Fungsi untuk melakukan refresh token pengguna
    public function refresh(Request $request)
    {
        // Mendapatkan pengguna dari request, menghapus semua token, dan membuat token baru
        $user = $request->user();
        $user->tokens()->delete();
        $token = $user->createToken('authToken')->plainTextToken;

        // Mengembalikan respons JSON dengan token baru
        return response()->json(['token' => $token], 200);
    }
}
