<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Ijin - Presensi RSSA</title>
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
            <a href="{{ url('/admin/shift') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-clock w-6"></i> Kelola Shift
            </a>
            <!-- MENU INI SEKARANG AKTIF -->
            <a href="{{ url('/admin/ijin') }}" class="flex items-center px-4 py-3 bg-teal-700 text-white rounded-xl shadow-inner font-semibold transition">
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
                <h2 class="text-2xl font-bold text-gray-800">Verifikasi Pengajuan Ijin</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fas fa-bell text-gray-400 text-xl cursor-pointer hover:text-teal-600 transition"></i>
                    @if($total_ijin_pending > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 w-3 h-3 rounded-full border-2 border-white"></span>
                    @endif
                </div>
                <a href="{{ url('/admin/logout') }}" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-100 transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </header>

        <!-- Konten Halaman -->
        <div class="p-8">

            <!-- Alert Notifikasi Jika Sukses Update Status -->
            @if(session('success'))
            <div class="bg-green-100 border border-green-200 text-green-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3 font-bold shadow-sm">
                <i class="fas fa-check-circle text-xl"></i>
                {{ session('success') }}
            </div>
            @endif

            <!-- Tabel Data Ijin -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 border-b border-gray-100 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-800"><i class="fas fa-envelope-open-text text-orange-500 mr-2"></i> Kotak Masuk Pengajuan</h3>
                </div>

                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-bold">Peserta Didik</th>
                                <th class="px-6 py-4 font-bold">Detail Pengajuan</th>
                                <th class="px-6 py-4 font-bold">Status</th>
                                <th class="px-6 py-4 font-bold text-center">Tindakan Admin</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">

                            @forelse($data_ijin as $row)
                            <tr class="hover:bg-teal-50/30 transition">
                                <td class="px-6 py-5">
                                    <p class="font-bold text-gray-800 text-base">{{ $row->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500 font-medium">NIM: {{ $row->nim }}</p>
                                    <p class="text-xs text-teal-600 font-bold mt-1">{{ $row->institusi }}</p>
                                </td>
                                <td class="px-6 py-5">
                                    <div class="flex items-center gap-2 mb-1">
                                        <span class="bg-gray-100 text-gray-700 px-2 py-1 rounded text-xs font-bold border border-gray-200">
                                            <i class="far fa-calendar-alt mr-1"></i> Ijin Tgl: {{ $row->tanggal_ijin }}
                                        </span>
                                        <span class="bg-blue-50 text-blue-600 px-2 py-1 rounded text-xs font-bold border border-blue-100">
                                            {{ $row->tipe_ijin }}
                                        </span>
                                    </div>
                                    <p class="text-gray-600 italic mt-2 text-sm bg-gray-50 p-2 rounded-lg border border-gray-100">"{{ $row->alasan }}"</p>
                                </td>
                                <td class="px-6 py-5">
                                    @if($row->status_persetujuan == 'Pending')
                                    <span class="bg-orange-100 text-orange-600 px-3 py-1.5 rounded-full text-xs font-bold flex w-max items-center gap-1 border border-orange-200">
                                        <i class="fas fa-clock"></i> Menunggu
                                    </span>
                                    @elseif($row->status_persetujuan == 'Disetujui')
                                    <span class="bg-green-100 text-green-700 px-3 py-1.5 rounded-full text-xs font-bold flex w-max items-center gap-1 border border-green-200">
                                        <i class="fas fa-check-circle"></i> Disetujui
                                    </span>
                                    @else
                                    <span class="bg-red-100 text-red-600 px-3 py-1.5 rounded-full text-xs font-bold flex w-max items-center gap-1 border border-red-200">
                                        <i class="fas fa-times-circle"></i> Ditolak
                                    </span>
                                    @endif
                                </td>
                                <td class="px-6 py-5 text-center">
                                    <!-- Jika status masih pending, tampilkan tombol aksi -->
                                    @if($row->status_persetujuan == 'Pending')
                                    <div class="flex items-center justify-center gap-2">
                                        <a href="{{ url('/admin/ijin/proses/'.$row->id.'/Disetujui') }}" class="bg-green-500 hover:bg-green-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-green-500/30 transition-transform active:scale-95" title="Setujui Ijin">
                                            <i class="fas fa-check"></i>
                                        </a>
                                        <a href="{{ url('/admin/ijin/proses/'.$row->id.'/Ditolak') }}" class="bg-red-500 hover:bg-red-600 text-white w-10 h-10 rounded-xl flex items-center justify-center shadow-lg shadow-red-500/30 transition-transform active:scale-95" title="Tolak Ijin">
                                            <i class="fas fa-times"></i>
                                        </a>
                                    </div>
                                    @else
                                    <span class="text-xs font-bold text-gray-400">Selesai Diproses</span>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="px-6 py-12 text-center text-gray-400">
                                    <div class="w-20 h-20 bg-gray-50 rounded-full flex items-center justify-center mx-auto mb-4 border border-gray-100">
                                        <i class="fas fa-envelope-open-text text-3xl text-gray-300"></i>
                                    </div>
                                    <p class="font-bold text-gray-500">Tidak ada pengajuan ijin</p>
                                    <p class="text-sm mt-1">Belum ada peserta didik yang mengajukan ijin/sakit.</p>
                                </td>
                            </tr>
                            @endforelse

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

</body>

</html>