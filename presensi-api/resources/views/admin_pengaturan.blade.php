<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pengaturan Sistem - Presensi RSSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden">

    <!-- Sidebar -->
    <aside class="w-64 bg-teal-800 text-white flex flex-col shadow-xl z-20 hidden md:flex">
        <div class="p-6 flex items-center justify-center border-b border-teal-700">
            <div class="bg-white w-11 h-11 rounded-full mr-3 flex items-center justify-center p-1.5 shadow-md">
                <img src="{{ asset('img/logo-rssa.png') }}" alt="Logo RSSA" class="w-full h-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-wider">Diklit RSSA</h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-chart-pie w-6"></i> Dasbor
            </a>
            <a href="{{ url('/admin/peserta') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-user-md w-6"></i> Data Peserta Didik
            </a>
            <a href="{{ url('/admin/shift') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-clock w-6"></i> Kelola Shift
            </a>
            <a href="{{ url('/admin/ijin') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-envelope-open-text w-6"></i> Pengajuan Ijin
            </a>
            <a href="{{ url('/admin/laporan') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-print w-6"></i> Laporan Presensi
            </a>
            <a href="{{ url('/admin/pengaturan') }}" class="flex items-center px-4 py-3 bg-teal-700 text-white rounded-xl shadow-inner font-semibold transition">
                <i class="fas fa-cog w-6"></i> Pengaturan
            </a>
        </nav>

        <div class="p-4 border-t border-teal-700">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-full bg-teal-600 flex items-center justify-center border-2 border-teal-400">
                    <i class="fas fa-user-shield text-sm"></i>
                </div>
                <div>
                    <p class="text-sm font-bold truncate w-40">{{ $admin_name }}</p>
                    <p class="text-xs text-teal-300">{{ $admin_role }}</p>
                </div>
            </div>
        </div>
    </aside>

    <!-- Area Utama -->
    <main class="flex-1 flex flex-col h-screen overflow-y-auto">
        <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between z-10">
            <div class="flex items-center text-gray-500">
                <i class="fas fa-bars text-xl mr-4 cursor-pointer md:hidden"></i>
                <h2 class="text-2xl font-bold text-gray-800">Pengaturan Sistem</h2>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ url('/admin/logout') }}" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-100 transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </header>

        <div class="p-8">
            @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3 font-bold shadow-sm">
                <i class="fas fa-check-circle text-xl"></i> {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-2 gap-8">

                <!-- KARTU PENGATURAN PROFIL -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-user-shield text-teal-600"></i> Keamanan Akun Admin
                    </h3>

                    <form action="{{ url('/admin/pengaturan/simpan') }}" method="POST" class="space-y-4">
                        @csrf
                        <input type="hidden" name="admin_id" value="{{ $admin->id }}">

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Username (Tidak bisa diubah)</label>
                            <input type="text" value="{{ $admin->username }}" disabled class="w-full bg-gray-100 text-gray-500 border border-gray-200 rounded-xl px-4 py-2.5 outline-none cursor-not-allowed">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Tampilan Admin</label>
                            <input type="text" name="nama_lengkap" value="{{ $admin->nama_lengkap }}" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Ubah Kata Sandi (Password)</label>
                            <input type="text" name="password" value="{{ $admin->password }}" required class="w-full bg-white border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div class="pt-4 mt-2 border-t border-gray-100">
                            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl py-3 transition shadow-lg shadow-teal-500/30">
                                <i class="fas fa-save mr-1"></i> Simpan Pembaruan Akun
                            </button>
                        </div>
                    </form>
                </div>

                <!-- KARTU INFORMASI SISTEM -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 h-max">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center gap-2">
                        <i class="fas fa-server text-blue-600"></i> Konfigurasi Aplikasi Mobile
                    </h3>

                    <form action="{{ url('/admin/pengaturan/sistem') }}" method="POST" class="space-y-4">
                        @csrf
                        <div class="flex items-start gap-3 bg-blue-50 p-4 rounded-xl border border-blue-100">
                            <i class="fas fa-map-marker-alt text-blue-500 text-xl mt-1"></i>
                            <div class="w-full">
                                <p class="text-sm font-bold text-gray-800 mb-2">Koordinat Pusat GPS (RSSA)</p>
                                <div class="grid grid-cols-2 gap-3">
                                    <div>
                                        <label class="text-xs text-gray-600 font-bold mb-1 block">Latitude</label>
                                        <input type="text" name="latitude" value="{{ $config['latitude'] ?? '-7.969608' }}" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                    <div>
                                        <label class="text-xs text-gray-600 font-bold mb-1 block">Longitude</label>
                                        <input type="text" name="longitude" value="{{ $config['longitude'] ?? '112.630045' }}" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="flex items-start gap-3 bg-orange-50 p-4 rounded-xl border border-orange-100">
                            <i class="fas fa-broadcast-tower text-orange-500 text-xl mt-1"></i>
                            <div class="w-full">
                                <p class="text-sm font-bold text-gray-800 mb-2">Radius Toleransi (Meter)</p>
                                <input type="number" name="radius" value="{{ $config['radius'] ?? '500' }}" required class="w-full bg-white border border-gray-200 rounded-lg px-3 py-2 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                <p class="text-[10px] text-gray-500 mt-1">Jarak maksimal peserta diizinkan absen.</p>
                            </div>
                        </div>

                        <div class="pt-4 border-t border-gray-100">
                            <button type="submit" class="w-full bg-blue-600 hover:bg-blue-700 text-white font-bold rounded-xl py-3 transition shadow-lg shadow-blue-500/30">
                                <i class="fas fa-sync-alt mr-1"></i> Perbarui Parameter GPS
                            </button>
                        </div>
                    </form>
                </div>

            </div>
        </div>
    </main>
</body>

</html>