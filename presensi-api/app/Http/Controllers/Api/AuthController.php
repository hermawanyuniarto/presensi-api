<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        // 1. Validasi input dari Android (NIM dan Password wajib diisi)
        $request->validate([
            'nim' => 'required',
            'password' => 'required'
        ]);

        // 2. Mencari data peserta didik berdasarkan NIM di database
        $user = DB::table('users')->where('nim', $request->nim)->first();

        // 3. Mengecek apakah NIM ada dan Password cocok
        // (Catatan: Untuk demo ini kita cocokan langsung, nanti di produksi pakai Hash)
        if (!$user || $user->password !== $request->password) {
            return response()->json([
                'success' => false,
                'message' => 'NIM atau Password salah!'
            ], 401);
        }

        // 4. Jika cocok, kirimkan pesan sukses beserta data profil peserta didik
        return response()->json([
            'success' => true,
            'message' => 'Login Berhasil!',
            'data' => [
                'nim' => $user->nim,
                'nama_lengkap' => $user->nama_lengkap,
                'gelar' => $user->gelar,
                'status' => $user->status,
                'program_studi' => $user->program_studi,
                'kelompok_peserta' => $user->kelompok_peserta,
                'institusi' => $user->institusi
            ]
        ], 200);
    }
}
