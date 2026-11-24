<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Session;

// 1. URL Utama diarahkan otomatis ke halaman login admin
Route::get('/', function () {
    return redirect('/admin/login');
});

// 2. Halaman Tampilan Login
Route::get('/admin/login', function () {
    if (Session::has('admin_logged_in')) {
        return redirect('/admin/dashboard');
    }
    return view('login');
});

// 3. Proses Validasi Login Admin (Menangkap data dari form)
Route::post('/admin/login', function (Request $request) {
    // Mencari admin di database
    $admin = DB::table('admins')
        ->where('username', $request->username)
        ->where('password', $request->password)
        ->first();

    if ($admin) {
        // Jika cocok, simpan data di Sesi Browser (Session)
        Session::put('admin_logged_in', true);
        Session::put('admin_name', $admin->nama_lengkap);
        Session::put('admin_role', $admin->role_admin);

        return redirect('/admin/dashboard');
    }

    // Jika gagal, kembalikan ke halaman login bawa pesan error
    return back()->with('error', 'Username atau Password salah!');
});

// 4. Proses Logout
Route::get('/admin/logout', function () {
    Session::flush(); // Hapus semua sesi
    return redirect('/admin/login');
});

// 5. Halaman Dasbor Admin (DIPROTEKSI DAN DIISOLASI)
Route::get('/admin/dashboard', function () {
    if (!Session::has('admin_logged_in')) {
        return redirect('/admin/login');
    }

    date_default_timezone_set('Asia/Jakarta');
    $hari_ini = date('Y-m-d');
    $admin_role = Session::get('admin_role'); // Ambil jabatan admin (Kedokteran/Keperawatan/dll)

    // Tarik data presensi HANYA untuk kelompok admin yang login
    $presensi = DB::table('presensi')
        ->join('users', 'presensi.nim', '=', 'users.nim')
        ->select('presensi.*', 'users.nama_lengkap', 'users.institusi', 'users.program_studi')
        ->where('users.kelompok_peserta', $admin_role) // <-- KUNCI ISOLASI DATA
        ->orderBy('presensi.tanggal', 'desc')
        ->orderBy('presensi.jam_datang', 'desc')
        ->get();

    // Hitung total hadir HANYA untuk kelompok admin yang login
    $total_hadir = DB::table('presensi')
        ->join('users', 'presensi.nim', '=', 'users.nim')
        ->where('presensi.tanggal', $hari_ini)
        ->where('users.kelompok_peserta', $admin_role) // <-- KUNCI ISOLASI DATA
        ->count();

    // Hitung total ijin HANYA untuk kelompok admin yang login
    $total_ijin = DB::table('perijinan')
        ->join('users', 'perijinan.nim', '=', 'users.nim')
        ->where('perijinan.tanggal_ijin', $hari_ini)
        ->where('users.kelompok_peserta', $admin_role) // <-- KUNCI ISOLASI DATA
        ->count();

    return view('admin', [
        'data_presensi' => $presensi,
        'total_hadir' => $total_hadir,
        'total_ijin' => $total_ijin,
        'admin_name' => Session::get('admin_name'),
        'admin_role' => $admin_role
    ]);
});

// 6. Halaman Kelola Pengajuan Ijin (DIISOLASI)
Route::get('/admin/ijin', function () {
    if (!Session::has('admin_logged_in')) {
        return redirect('/admin/login');
    }

    $admin_role = Session::get('admin_role');

    // Ambil data ijin HANYA untuk kelompok admin yang login
    $data_ijin = DB::table('perijinan')
        ->join('users', 'perijinan.nim', '=', 'users.nim')
        ->select('perijinan.*', 'users.nama_lengkap', 'users.institusi')
        ->where('users.kelompok_peserta', $admin_role) // <-- KUNCI ISOLASI DATA
        ->orderBy('perijinan.created_at', 'desc')
        ->get();

    // Hitung notifikasi lonceng merah HANYA untuk admin tersebut
    $total_ijin_pending = DB::table('perijinan')
        ->join('users', 'perijinan.nim', '=', 'users.nim')
        ->where('perijinan.status_persetujuan', 'Pending')
        ->where('users.kelompok_peserta', $admin_role) // <-- KUNCI ISOLASI DATA
        ->count();

    return view('admin_ijin', [
        'data_ijin' => $data_ijin,
        'total_ijin_pending' => $total_ijin_pending,
        'admin_name' => Session::get('admin_name'),
        'admin_role' => $admin_role
    ]);
});

// 7. Proses Setujui / Tolak Ijin (Diakses via Tombol)
Route::get('/admin/ijin/proses/{id}/{status}', function ($id, $status) {
    if (!Session::has('admin_logged_in')) {
        return redirect('/admin/login');
    }

    // Update status di database menjadi "Disetujui" atau "Ditolak"
    DB::table('perijinan')->where('id', $id)->update([
        'status_persetujuan' => $status
    ]);

    return back()->with('success', 'Status ijin berhasil diperbarui menjadi: ' . $status . '!');
});

// 8. Halaman Kelola Shift Dinamis Admin
Route::get('/admin/shift', function () {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    $admin_role = Session::get('admin_role');
    // Hanya tampilkan shift sesuai wewenang admin (Dokter urus Dokter, Perawat urus Perawat)
    $data_shift = DB::table('master_shifts')->where('kelompok_peserta', $admin_role)->get();

    return view('admin_shift', [
        'data_shift' => $data_shift,
        'admin_name' => Session::get('admin_name'),
        'admin_role' => $admin_role
    ]);
});

// 9. Proses Tambah Shift
Route::post('/admin/shift/tambah', function (Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    DB::table('master_shifts')->insert([
        'kelompok_peserta' => Session::get('admin_role'),
        'nama_shift' => $request->nama_shift,
        'jam_datang_mulai' => $request->jam_datang_mulai,
        'jam_datang_selesai' => $request->jam_datang_selesai,
        'jam_pulang_mulai' => $request->jam_pulang_mulai,
        'jam_pulang_selesai' => $request->jam_pulang_selesai,
        'is_cross_day' => $request->has('is_cross_day') ? 1 : 0
    ]);

    return back()->with('success', 'Jadwal Shift Baru berhasil ditambahkan ke Database!');
});

// 10. Proses Hapus Shift
Route::get('/admin/shift/hapus/{id}', function ($id) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    DB::table('master_shifts')->where('id', $id)->delete();
    return back()->with('success', 'Jadwal Shift berhasil dihapus!');
});

// 11. Halaman Kelola Data Peserta Didik
Route::get('/admin/peserta', function () {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    $admin_role = Session::get('admin_role');

    // Tarik data peserta yang kelompoknya sesuai dengan Admin yang login
    $data_peserta = DB::table('users')
        ->where('kelompok_peserta', $admin_role)
        ->orderBy('created_at', 'desc')
        ->get();

    return view('admin_peserta', [
        'data_peserta' => $data_peserta,
        'admin_name' => Session::get('admin_name'),
        'admin_role' => $admin_role
    ]);
});

// 12. Proses Tambah Peserta Didik
Route::post('/admin/peserta/tambah', function (Illuminate\Http\Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    // Cek apakah NIM sudah terdaftar untuk menghindari error duplikat
    $cek_nim = DB::table('users')->where('nim', $request->nim)->first();
    if ($cek_nim) return back()->with('error', 'Gagal! NIM ' . $request->nim . ' sudah terdaftar di database.');

    DB::table('users')->insert([
        'nim' => $request->nim,
        'password' => $request->password,
        'nama_lengkap' => $request->nama_lengkap,
        'gelar' => $request->gelar,
        'status' => $request->status,
        'program_studi' => $request->program_studi,
        'institusi' => $request->institusi,
        'kelompok_peserta' => Session::get('admin_role') // Otomatis masuk ke kelompok admin yang sedang login
    ]);
    return back()->with('success', 'Data peserta didik baru berhasil ditambahkan!');
});

// FITUR BARU 1: Download Template CSV untuk Import
Route::get('/admin/peserta/template-csv', function () {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    // PENJAGA: Tolak selain Kedokteran
    if (Session::get('admin_role') !== 'Kedokteran') {
        return back()->with('error', 'Akses Ditolak: Fitur Import Massal hanya diberikan untuk Admin Kedokteran.');
    }

    $filename = "Template_Import_Peserta_Diklit.csv";
    $headers = [
        "Content-type"        => "text/csv",
        "Content-Disposition" => "attachment; filename=$filename",
        "Pragma"              => "no-cache",
        "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
        "Expires"             => "0"
    ];

    // Buka file memori sementara untuk menulis CSV
    $callback = function () {
        $file = fopen('php://output', 'w');
        // Tulis Judul Kolom (Header)
        fputcsv($file, ['NIM', 'Password', 'Nama Lengkap', 'Gelar', 'Status Pendidikan', 'Program Studi', 'Institusi']);
        // Tulis 1 baris contoh agar User paham isinya
        fputcsv($file, ['1950001', '123456', 'dr. Budi Setiawan', 'S.Ked', 'Dokter Muda', 'Penyakit Dalam', 'Universitas Brawijaya']);
        fclose($file);
    };

    return response()->stream($callback, 200, $headers);
});

// FITUR BARU 2: Proses Membaca dan Memasukkan (Import) CSV ke Database
Route::post('/admin/peserta/import', function (Illuminate\Http\Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    // PENJAGA: Tolak selain Kedokteran
    if (Session::get('admin_role') !== 'Kedokteran') {
        return back()->with('error', 'Akses Ditolak: Fitur Import Massal hanya diberikan untuk Admin Kedokteran.');
    }

    // 1. Validasi Ekstra Ketat: Pastikan file benar-benar ada dan tidak rusak
    if (!$request->hasFile('file_csv') || !$request->file('file_csv')->isValid()) {
        return back()->with('error', 'Gagal: File CSV rusak, kebesaran, atau belum Anda pilih!');
    }

    $file = $request->file('file_csv');
    $admin_role = Session::get('admin_role');

    // 2. Gunakan getPathname() -> Jauh lebih stabil di Windows daripada getRealPath()
    $path = $file->getPathname();

    // 3. Pastikan sistem Windows memberikan izin untuk membaca file
    if (!file_exists($path) || !is_readable($path)) {
        return back()->with('error', 'Gagal: Sistem tidak memiliki izin (Permission) untuk membaca file ini.');
    }

    // Buka file CSV yang diunggah
    $handle = fopen($path, "r");
    if ($handle !== FALSE) {
        $header = fgetcsv($handle, 1000, ","); // Ambil baris pertama (Judul Kolom)
        $data_peserta_baru = [];
        $jumlah_sukses = 0;

        // Baca data per baris
        while (($row = fgetcsv($handle, 1000, ",")) !== FALSE) {
            // Abaikan jika baris kosong sepenuhnya
            if (empty(array_filter($row))) continue;

            // CEK KHUSUS INDONESIA: Jika Excel menyimpan CSV dengan Titik Koma (;)
            // Maka seluruh data akan tergabung di $row[0]. Kita harus memecahnya.
            if (count($row) == 1 && strpos($row[0], ';') !== false) {
                $row = explode(';', $row[0]);
            }

            // Abaikan jika tidak ada NIM (karena NIM wajib)
            if (empty($row[0])) continue;

            // Siapkan data dalam format Array (Menggunakan batasan aman agar tidak Undefined)
            $data_peserta_baru[] = [
                'nim' => $row[0],
                'password' => $row[1] ?? '123456', // Default 123456 jika lupa mengisi
                'nama_lengkap' => $row[2] ?? 'Tanpa Nama',
                'gelar' => $row[3] ?? null,
                'status' => $row[4] ?? '-',
                'program_studi' => $row[5] ?? '-',
                'institusi' => $row[6] ?? '-',
                'kelompok_peserta' => $admin_role
            ];
            $jumlah_sukses++;
        }
        fclose($handle);

        // TEKNIK OPTIMASI BATCH INSERT & IGNORE DUPLICATE
        $chunks = array_chunk($data_peserta_baru, 500);
        foreach ($chunks as $chunk) {
            DB::table('users')->insertOrIgnore($chunk);
        }

        return back()->with('success', 'Hore! Sebanyak ' . $jumlah_sukses . ' data peserta dari file CSV berhasil dimasukkan ke sistem.');
    }

    return back()->with('error', 'Gagal membuka file. Pastikan format file adalah .csv');
});

// 13. Proses Hapus Peserta Didik
Route::get('/admin/peserta/hapus/{nim}', function ($nim) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    DB::table('users')->where('nim', $nim)->delete();
    return back()->with('success', 'Peserta didik berhasil dihapus dari sistem!');
});

// 14. Halaman Cetak Laporan Presensi
Route::get('/admin/laporan', function (Illuminate\Http\Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    $admin_role = Session::get('admin_role');

    // Siapkan Query Dasar (Menghubungkan tabel presensi dan users)
    $query = DB::table('presensi')
        ->join('users', 'presensi.nim', '=', 'users.nim')
        ->select('presensi.*', 'users.nama_lengkap', 'users.status', 'users.program_studi', 'users.institusi')
        ->where('users.kelompok_peserta', $admin_role);

    // Filter 1: Rentang Tanggal (Bisa untuk filter Bulanan / Tahunan)
    if ($request->has('tgl_mulai') && $request->tgl_mulai != '') {
        $query->whereDate('presensi.tanggal', '>=', $request->tgl_mulai);
    }
    if ($request->has('tgl_akhir') && $request->tgl_akhir != '') {
        $query->whereDate('presensi.tanggal', '<=', $request->tgl_akhir);
    }

    // Filter 2: Status Pendidikan (Dokter Muda, Kebidanan, dll)
    if ($request->has('status') && $request->status != '') {
        $query->where('users.status', $request->status);
    }

    // Filter 3: Program Studi
    if ($request->has('prodi') && $request->prodi != '') {
        $query->where('users.program_studi', 'LIKE', '%' . $request->prodi . '%');
    }

    // Eksekusi Query
    $data_laporan = $query->orderBy('presensi.tanggal', 'desc')->get();

    // Ambil daftar unik dari database untuk mengisi Dropdown Filter secara otomatis
    $list_status = DB::table('users')->where('kelompok_peserta', $admin_role)->select('status')->distinct()->pluck('status');
    $list_prodi = DB::table('users')->where('kelompok_peserta', $admin_role)->select('program_studi')->distinct()->pluck('program_studi');

    return view('admin_laporan', [
        'data_laporan' => $data_laporan,
        'list_status' => $list_status,
        'list_prodi' => $list_prodi,
        'admin_name' => Session::get('admin_name'),
        'admin_role' => $admin_role,
        'request' => $request // Membawa kembali inputan ke form
    ]);
});

// 15. Halaman Pengaturan Sistem & Akun
Route::get('/admin/pengaturan', function () {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    $admin_name = Session::get('admin_name');
    $admin_role = Session::get('admin_role');

    $admin_data = DB::table('admins')
        ->where('nama_lengkap', $admin_name)
        ->where('role_admin', $admin_role)
        ->first();

    // Tarik data koordinat dari database
    $config = DB::table('konfigurasi')->pluck('nilai', 'nama_pengaturan')->toArray();
    // Default jika database kosong
    if (empty($config)) {
        $config = ['latitude' => '-7.969608', 'longitude' => '112.630045', 'radius' => '500'];
    }

    return view('admin_pengaturan', [
        'admin_name' => $admin_name,
        'admin_role' => $admin_role,
        'admin' => $admin_data,
        'config' => $config
    ]);
});

// 16. Proses Simpan Pengaturan (Ganti Password & Nama)
Route::post('/admin/pengaturan/simpan', function (Illuminate\Http\Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    DB::table('admins')->where('id', $request->admin_id)->update([
        'nama_lengkap' => $request->nama_lengkap,
        'password' => $request->password
    ]);

    Session::put('admin_name', $request->nama_lengkap);
    return back()->with('success', 'Profil dan Password berhasil diperbarui!');
});

// 17. JALUR BARU: Proses Simpan Konfigurasi GPS
Route::post('/admin/pengaturan/sistem', function (Illuminate\Http\Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    DB::table('konfigurasi')->updateOrInsert(['nama_pengaturan' => 'latitude'], ['nilai' => $request->latitude]);
    DB::table('konfigurasi')->updateOrInsert(['nama_pengaturan' => 'longitude'], ['nilai' => $request->longitude]);
    DB::table('konfigurasi')->updateOrInsert(['nama_pengaturan' => 'radius'], ['nilai' => $request->radius]);

    return back()->with('success', 'Konfigurasi Sistem GPS berhasil diperbarui! HP Peserta akan otomatis mengikuti aturan baru.');
});

// 16. Proses Simpan Pengaturan (Ganti Password & Nama)
Route::post('/admin/pengaturan/simpan', function (Illuminate\Http\Request $request) {
    if (!Session::has('admin_logged_in')) return redirect('/admin/login');

    DB::table('admins')->where('id', $request->admin_id)->update([
        'nama_lengkap' => $request->nama_lengkap,
        'password' => $request->password
    ]);

    // Perbarui nama di memori sesi agar langsung berubah di layar
    Session::put('admin_name', $request->nama_lengkap);

    return back()->with('success', 'Profil dan Password berhasil diperbarui!');
});
