<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dasbor - Presensi RSSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">

    <!-- Tambahan Library Tabel Pagination (Simple DataTables) -->
    <link href="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3/dist/style.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f3f4f6;
        }

        /* Kostumisasi Tampilan DataTables agar senada dengan Tailwind */
        .datatable-wrapper {
            font-family: inherit;
        }

        .datatable-selector {
            border-radius: 0.5rem;
            padding: 0.25rem 2rem 0.25rem 0.5rem;
            border-color: #e5e7eb;
            outline: none;
            background-color: #f9fafb;
            cursor: pointer;
        }

        .datatable-input {
            border-radius: 0.5rem;
            padding: 0.5rem 1rem;
            border-color: #e5e7eb;
            outline: none;
            background-color: #f9fafb;
            width: 100%;
            max-width: 250px;
        }

        .datatable-input:focus,
        .datatable-selector:focus {
            border-color: #0d9488;
            box-shadow: 0 0 0 2px rgba(13, 148, 136, 0.2);
        }

        .datatable-pagination .datatable-active a {
            background-color: #0d9488 !important;
            color: white !important;
            border-radius: 0.5rem;
        }

        .datatable-pagination a {
            border-radius: 0.5rem;
            color: #4b5563;
            transition: all 0.2s;
            font-weight: bold;
        }

        .datatable-pagination a:hover {
            background-color: #ccfbf1;
            color: #0f766e;
        }

        .datatable-top,
        .datatable-bottom {
            padding: 1rem 1.5rem;
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 1rem;
        }

        .datatable-container {
            border-top: 1px solid #f3f4f6;
            border-bottom: 1px solid #f3f4f6;
        }

        .datatable-info {
            color: #6b7280;
            font-size: 0.875rem;
            font-weight: 500;
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
            <h1 class="text-xl font-bold tracking-wider">Diklit RSSA</h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-4 py-3 bg-teal-700 text-white rounded-xl shadow-inner font-semibold transition">
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
            <!-- TAMBAHAN MENU LAPORAN PRESENSI -->
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
                    <!-- NAMA DAN ROLE DINAMIS DARI DATABASE -->
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
                <h2 class="text-2xl font-bold text-gray-800">Monitor Kehadiran Harian</h2>
            </div>
            <div class="flex items-center gap-4">
                <div class="relative">
                    <i class="fas fa-bell text-gray-400 text-xl cursor-pointer hover:text-teal-600 transition"></i>
                    @if($total_ijin > 0)
                    <span class="absolute -top-1 -right-1 bg-red-500 w-3 h-3 rounded-full border-2 border-white"></span>
                    @endif
                </div>
                <!-- TOMBOL LOGOUT AKTIF -->
                <a href="{{ url('/admin/logout') }}" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-100 transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </header>

        <!-- Konten Halaman -->
        <div class="p-8">
            <!-- Kartu Statistik -->
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-8">
                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-teal-500 hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-1">Total Hadir Hari Ini</p>
                        <p class="text-3xl font-black text-gray-800">{{ $total_hadir }} <span class="text-sm font-medium text-gray-400">Orang</span></p>
                    </div>
                    <div class="w-14 h-14 bg-teal-50 rounded-full flex items-center justify-center text-teal-500 text-2xl">
                        <i class="fas fa-user-check"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-orange-500 hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-1">Pengajuan Ijin / Sakit</p>
                        <p class="text-3xl font-black text-gray-800">{{ $total_ijin }} <span class="text-sm font-medium text-gray-400">Berkas</span></p>
                    </div>
                    <div class="w-14 h-14 bg-orange-50 rounded-full flex items-center justify-center text-orange-500 text-2xl">
                        <i class="fas fa-envelope-open-text"></i>
                    </div>
                </div>

                <div class="bg-white rounded-2xl p-6 shadow-sm border border-gray-100 flex items-center justify-between border-l-4 border-l-blue-500 hover:shadow-md transition">
                    <div>
                        <p class="text-sm font-bold text-gray-500 mb-1">Status Server</p>
                        <p class="text-3xl font-black text-gray-800">Aman</p>
                    </div>
                    <div class="w-14 h-14 bg-blue-50 rounded-full flex items-center justify-center text-blue-500 text-2xl">
                        <i class="fas fa-server"></i>
                    </div>
                </div>
            </div>

            <!-- Tabel Riwayat Absensi -->
            <div class="bg-white rounded-2xl shadow-sm border border-gray-100 overflow-hidden">
                <div class="px-6 py-5 flex justify-between items-center bg-gray-50/50">
                    <h3 class="font-bold text-lg text-gray-800"><i class="fas fa-list text-teal-600 mr-2"></i> Log Kehadiran Real-time</h3>
                    <button onclick="window.location.reload()" class="text-sm bg-white border border-gray-200 hover:bg-gray-50 px-4 py-2 rounded-lg font-semibold text-gray-600 shadow-sm transition">
                        <i class="fas fa-sync-alt mr-1"></i> Refresh Data
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <!-- Penambahan Class tabel-presensi -->
                    <table class="w-full text-left border-collapse tabel-presensi">
                        <thead>
                            <tr class="bg-gray-50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                <th class="px-6 py-4 font-bold">Tanggal</th>
                                <th class="px-6 py-4 font-bold">Peserta Didik</th>
                                <th class="px-6 py-4 font-bold">Institusi</th>
                                <th class="px-6 py-4 font-bold">Jam Datang</th>
                                <th class="px-6 py-4 font-bold">Jam Pulang</th>
                                <th class="px-6 py-4 font-bold">Status</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-gray-100 text-sm">

                            <!-- Looping Data dari Database (Ganti forelse menjadi foreach) -->
                            @foreach($data_presensi as $row)
                            <tr class="hover:bg-teal-50/30 transition">
                                <td class="px-6 py-4 font-semibold text-gray-600">{{ $row->tanggal }}</td>
                                <td class="px-6 py-4">
                                    <p class="font-bold text-gray-800">{{ $row->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500">NIM: {{ $row->nim }}</p>
                                </td>
                                <td class="px-6 py-4 text-gray-600">{{ $row->institusi }}</td>
                                <td class="px-6 py-4">
                                    <span class="bg-green-100 text-green-700 px-3 py-1 rounded-full text-xs font-bold border border-green-200">
                                        <i class="fas fa-sign-in-alt mr-1"></i> {{ $row->jam_datang }}
                                    </span>

                                    <!-- TAMBAHAN TOMBOL LIHAT FOTO DATANG -->
                                    @if(isset($row->foto_datang) && $row->foto_datang != null)
                                    <a href="{{ asset('img/presensi/'.$row->foto_datang) }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-teal-600 hover:text-teal-800 transition bg-teal-50 px-2 py-1 rounded border border-teal-100">
                                        <i class="fas fa-camera"></i> Foto Datang
                                    </a>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($row->jam_pulang)
                                    <span class="bg-orange-100 text-orange-700 px-3 py-1 rounded-full text-xs font-bold border border-orange-200">
                                        <i class="fas fa-sign-out-alt mr-1"></i> {{ $row->jam_pulang }}
                                    </span>

                                    <!-- TAMBAHAN TOMBOL LIHAT FOTO PULANG -->
                                    @if(isset($row->foto_pulang) && $row->foto_pulang != null)
                                    <br>
                                    <a href="{{ asset('img/presensi/'.$row->foto_pulang) }}" target="_blank" class="mt-2 inline-flex items-center gap-1 text-[10px] font-bold text-orange-600 hover:text-orange-800 transition bg-orange-50 px-2 py-1 rounded border border-orange-100">
                                        <i class="fas fa-camera"></i> Foto Pulang
                                    </a>
                                    @endif

                                    @else
                                    <span class="text-gray-400 italic text-xs">Belum Pulang</span>
                                    @endif
                                </td>
                                <td class="px-6 py-4">
                                    @if($row->status_pulang == 'Selesai')
                                    <span class="text-teal-600 font-bold"><i class="fas fa-check-circle"></i> Selesai</span>
                                    @else
                                    <span class="text-blue-600 font-bold"><i class="fas fa-spinner fa-spin"></i> Sedang Dinas</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

            <p class="text-center text-gray-400 text-sm mt-8">© 2026 RSUD Dr. Saiful Anwar Malang. All rights reserved.</p>
        </div>
    </main>

    <!-- Script Inisialisasi DataTables -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3" type="text/javascript"></script>
    <script>
        document.addEventListener("DOMContentLoaded", function() {
            const table = document.querySelector(".tabel-presensi");
            if (table) {
                new simpleDatatables.DataTable(table, {
                    labels: {
                        placeholder: "Cari data cepat...",
                        perPage: "Data per halaman",
                        noRows: "Tidak ada data yang ditemukan",
                        info: "Menampilkan {start} s/d {end} dari total {rows} data",
                    },
                    perPageSelect: [10, 25, 50, 100],
                    perPage: 10
                });
            }
        });
    </script>
</body>

</html>