<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Shift - Presensi RSSA</title>
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
            <!-- LOGO RSSA LOKAL -->
            <div class="bg-white w-11 h-11 rounded-full mr-3 flex items-center justify-center p-1.5 shadow-md">
                <img src="{{ asset('img/logo-rssa.png') }}" alt="Logo RSSA" class="w-full h-full object-contain">
            </div>
            <h1 class="text-xl font-bold tracking-wider">Diklat RSSA</h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-chart-pie w-6"></i> Dasbor
            </a>
            <a href="{{ url('/admin/peserta') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-user-md w-6"></i> Data Peserta Didik
            </a>
            <!-- MENU INI SEKARANG AKTIF -->
            <a href="{{ url('/admin/shift') }}" class="flex items-center px-4 py-3 bg-teal-700 text-white rounded-xl shadow-inner font-semibold transition">
                <i class="fas fa-clock w-6"></i> Kelola Shift
            </a>
            <a href="{{ url('/admin/ijin') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-envelope-open-text w-6"></i> Pengajuan Ijin
            </a>
            <!-- TAMBAHAN MENU LAPORAN -->
            <a href="{{ url('/admin/laporan') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-print w-6"></i> Laporan Presensi
            </a>
            <a href="{{ url('/admin/pengaturan') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
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

        <!-- Header -->
        <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between z-10">
            <div class="flex items-center text-gray-500">
                <i class="fas fa-bars text-xl mr-4 cursor-pointer md:hidden"></i>
                <h2 class="text-2xl font-bold text-gray-800">Master Data Shift (<span class="text-teal-600">{{ $admin_role }}</span>)</h2>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ url('/admin/logout') }}" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-100 transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </header>

        <!-- Konten Halaman -->
        <div class="p-8">

            @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3 font-bold shadow-sm">
                <i class="fas fa-check-circle text-xl"></i>
                {{ session('success') }}
            </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">

                <!-- Formulir Tambah Shift -->
                <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 h-max">
                    <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-3 border-gray-100"><i class="fas fa-plus-circle text-teal-500 mr-2"></i> Tambah Shift Baru</h3>

                    <form action="{{ url('/admin/shift/tambah') }}" method="POST" class="space-y-4">
                        @csrf
                        <div>
                            <label class="block text-sm font-bold text-gray-700 mb-1">Nama Shift</label>
                            <input type="text" name="nama_shift" required placeholder="Contoh: Poli Pagi" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 focus:ring-2 focus:ring-teal-500 outline-none">
                        </div>

                        <div class="grid grid-cols-2 gap-4">
                            <div class="bg-blue-50 p-3 rounded-xl border border-blue-100">
                                <p class="text-xs font-bold text-blue-700 mb-2"><i class="fas fa-sign-in-alt"></i> JAM DATANG</p>
                                <label class="block text-xs font-medium text-gray-600">Mulai Absen:</label>
                                <input type="time" name="jam_datang_mulai" required class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1 mb-2 text-sm">
                                <label class="block text-xs font-medium text-gray-600">Batas Telat:</label>
                                <input type="time" name="jam_datang_selesai" required class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1 text-sm">
                            </div>

                            <div class="bg-orange-50 p-3 rounded-xl border border-orange-100">
                                <p class="text-xs font-bold text-orange-700 mb-2"><i class="fas fa-sign-out-alt"></i> JAM PULANG</p>
                                <label class="block text-xs font-medium text-gray-600">Mulai Pulang:</label>
                                <input type="time" name="jam_pulang_mulai" required class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1 mb-2 text-sm">
                                <label class="block text-xs font-medium text-gray-600">Batas Akhir:</label>
                                <input type="time" name="jam_pulang_selesai" required class="w-full bg-white border border-gray-200 rounded-lg px-2 py-1 text-sm">
                            </div>
                        </div>

                        <div class="flex items-center gap-3 bg-gray-50 p-3 rounded-xl border border-gray-200">
                            <input type="checkbox" name="is_cross_day" id="cross" class="w-5 h-5 text-teal-600 rounded">
                            <label for="cross" class="text-sm font-medium text-gray-700 cursor-pointer">Shift Malam (Lompat Hari)</label>
                        </div>

                        <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl py-3 transition shadow-lg shadow-teal-500/30">
                            Simpan Shift
                        </button>
                    </form>
                </div>

                <!-- Tabel Data Shift Aktif -->
                <div class="lg:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-lg text-gray-800"><i class="fas fa-list-ul text-teal-600 mr-2"></i> Daftar Shift Aktif</h3>
                    </div>

                    <div class="overflow-x-auto p-4">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider rounded-xl">
                                    <th class="px-4 py-3 font-bold rounded-l-xl">Nama Shift</th>
                                    <th class="px-4 py-3 font-bold">Jadwal Datang</th>
                                    <th class="px-4 py-3 font-bold">Jadwal Pulang</th>
                                    <th class="px-4 py-3 font-bold text-center rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50">

                                @forelse($data_shift as $row)
                                <tr class="hover:bg-teal-50/30 transition">
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-gray-800">{{ $row->nama_shift }}</p>
                                        @if($row->is_cross_day)
                                        <span class="text-[10px] bg-indigo-100 text-indigo-700 px-2 py-0.5 rounded-full font-bold">Lompat Hari</span>
                                        @endif
                                    </td>
                                    <td class="px-4 py-4 text-blue-600 font-semibold">
                                        {{ substr($row->jam_datang_mulai,0,5) }} - {{ substr($row->jam_datang_selesai,0,5) }}
                                    </td>
                                    <td class="px-4 py-4 text-orange-600 font-semibold">
                                        {{ substr($row->jam_pulang_mulai,0,5) }} - {{ substr($row->jam_pulang_selesai,0,5) }}
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <a href="{{ url('/admin/shift/hapus/'.$row->id) }}" onclick="return confirm('Hapus shift ini? (Akan hilang dari HP peserta didik)')" class="bg-red-50 text-red-500 hover:bg-red-500 hover:text-white px-3 py-2 rounded-lg text-xs font-bold transition">
                                            <i class="fas fa-trash-alt"></i> Hapus
                                        </a>
                                    </td>
                                </tr>
                                @empty
                                <tr>
                                    <td colspan="4" class="px-6 py-8 text-center text-gray-400">Belum ada shift terdaftar.</td>
                                </tr>
                                @endforelse

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </main>

</body>

</html>