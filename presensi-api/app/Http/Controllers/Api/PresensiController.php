<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class PresensiController extends Controller
{
    // Fungsi Menyimpan Absen Datang / Pulang
    public function store(Request $request)
    {
        // 🛡️ LAPIS KEAMANAN 2: TOLAK JIKA TANPA SANDI RAHASIA
        if ($request->header('X-API-KEY') !== 'RSSA-DIKLIT-SECURE-2026') {
            return response()->json([
                'success' => false,
                'message' => 'Akses Ditolak: Anda mencoba menggunakan API secara ilegal!'
            ], 403);
        }

        date_default_timezone_set('Asia/Jakarta');
        $tanggal = date('Y-m-d');
        $jam = date('H:i:s');

        // TAMBAHAN BARU: Tangkap dan Simpan Foto jika ada
        $nama_file_foto = null;

        // 1. Trik Tingkat Dewa: Tangkap foto dari Base64 (Teks) lalu rakit kembali jadi Gambar JPG
        if ($request->has('foto_base64')) {
            $image_base64 = base64_decode($request->foto_base64);
            $nama_file_foto = $request->nim . '_' . $request->type . '_' . time() . '.jpg';

            // Simpan file fisik langsung ke folder img/presensi
            file_put_contents(public_path('img/presensi/' . $nama_file_foto), $image_base64);
        }
        // 2. Fallback (Cadangan) jika foto dikirim via File Upload biasa
        else if ($request->hasFile('foto')) {
            $file = $request->file('foto');
            $nama_file_foto = $request->nim . '_' . $request->type . '_' . time() . '.' . $file->getClientOriginalExtension();
            $file->move(public_path('img/presensi'), $nama_file_foto);
        }

        if ($request->type == 'DATANG') {
            // Cek apakah hari ini sudah absen datang?
            $presensi = DB::table('presensi')
                ->where('nim', $request->nim)
                ->where('tanggal', $tanggal)
                ->first();

            if ($presensi) {
                return response()->json(['success' => false, 'message' => 'Anda sudah melakukan absen DATANG hari ini!']);
            }

            // Masukkan data baru ke tabel presensi (beserta Foto)
            DB::table('presensi')->insert([
                'nim' => $request->nim,
                'tanggal' => $tanggal,
                'jam_datang' => $jam,
                'shift' => $request->shift,
                'status_datang' => $request->status,
                'foto_datang' => $nama_file_foto // <-- Simpan nama fotonya
            ]);
            return response()->json(['success' => true, 'message' => 'Absen DATANG berhasil direkam. (' . $request->status . ')']);
        } else if ($request->type == 'PULANG') {
            // LOGIKA BARU: Cari data absen TERAKHIR yang belum diakhiri (jam_pulang masih kosong)
            // Tidak peduli apakah itu absen dari hari ini atau dari kemarin (Shift Malam)
            $presensi = DB::table('presensi')
                ->where('nim', $request->nim)
                ->whereNull('jam_pulang')
                ->orderBy('tanggal', 'desc')
                ->first();

            if (!$presensi) {
                return response()->json(['success' => false, 'message' => 'Anda belum absen DATANG atau sudah pulang sebelumnya. Tidak bisa absen PULANG!']);
            }

            if ($presensi->shift != $request->shift) {
                return response()->json(['success' => false, 'message' => 'Shift tidak sesuai! Absen datang terakhir Anda adalah Shift ' . $presensi->shift . '.']);
            }

            // Update data dengan jam pulang beserta Foto
            DB::table('presensi')->where('id', $presensi->id)->update([
                'jam_pulang' => $jam,
                'status_pulang' => $request->status,
                'foto_pulang' => $nama_file_foto // <-- Simpan nama fotonya
            ]);
            return response()->json(['success' => true, 'message' => 'Absen PULANG berhasil direkam. (' . $request->status . ')']);
        }
    }

    // Fungsi Menyimpan Pengajuan Ijin
    public function storeIjin(Request $request)
    {
        date_default_timezone_set('Asia/Jakarta');

        DB::table('perijinan')->insert([
            'nim' => $request->nim,
            'tanggal_ijin' => $request->tanggal,
            'tipe_ijin' => $request->tipe,
            'alasan' => $request->alasan,
            'status_persetujuan' => 'Pending'
        ]);

        return response()->json(['success' => true, 'message' => 'Pengajuan Ijin berhasil dikirim dan menunggu persetujuan Diklit.']);
    }

    // Fungsi Mengambil Riwayat Presensi
    public function riwayat(Request $request, string $nim)
    {
        date_default_timezone_set('Asia/Jakarta');

        $query = DB::table('presensi')->where('nim', $nim);

        // Jika HP mengirimkan filter tanggal (Dari tombol filter)
        if ($request->has('tgl_mulai') && $request->has('tgl_akhir')) {
            $query->whereBetween('tanggal', [$request->tgl_mulai, $request->tgl_akhir]);
        } else {
            // PERBAIKAN LOGIKA: Hanya tampilkan absen Hari Ini ATAU absen KEMARIN yang belum dipulangkan (Shift Malam)
            $hari_ini = date('Y-m-d');
            $kemarin = date('Y-m-d', strtotime('-1 days')); // Ambil tanggal kemarin

            $query->where(function ($q) use ($hari_ini, $kemarin) {
                // Syarat 1: Tanggal adalah Hari Ini
                $q->where('tanggal', $hari_ini)
                    // Syarat 2: ATAU Tanggal adalah Kemarin DAN Jam Pulang masih kosong
                    ->orWhere(function ($sub_q) use ($kemarin) {
                        $sub_q->where('tanggal', $kemarin)
                            ->whereNull('jam_pulang');
                    });
            });
        }

        $riwayat = $query->orderBy('tanggal', 'desc')->get();

        return response()->json([
            'success' => true,
            'message' => 'Data riwayat berhasil diambil.',
            'data' => $riwayat // ✅ INI KUNCI UTAMANYA: Mengirimkan data ke HP
        ]);
    }

    // FUNGSI BARU: Mengambil Jadwal Shift dari Database
    public function getShifts(string $kelompok)
    {
        $shifts = DB::table('master_shifts')->where('kelompok_peserta', $kelompok)->get();
        return response()->json(['success' => true, 'data' => $shifts]);
    }

    // FUNGSI BARU: Mengirim Parameter GPS ke HP Peserta
    public function getKonfigurasi()
    {
        $config = DB::table('konfigurasi')->pluck('nilai', 'nama_pengaturan');
        return response()->json(['success' => true, 'data' => $config]);
    }
}
