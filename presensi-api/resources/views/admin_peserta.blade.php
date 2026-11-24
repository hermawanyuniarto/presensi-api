<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Peserta Didik - Presensi RSSA</title>
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
            <h1 class="text-xl font-bold tracking-wider">Diklat RSSA</h1>
        </div>

        <nav class="flex-1 px-4 py-6 space-y-2">
            <a href="{{ url('/admin/dashboard') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
                <i class="fas fa-chart-pie w-6"></i> Dasbor
            </a>
            <!-- MENU INI SEKARANG AKTIF -->
            <a href="{{ url('/admin/peserta') }}" class="flex items-center px-4 py-3 bg-teal-700 text-white rounded-xl shadow-inner font-semibold transition">
                <i class="fas fa-user-md w-6"></i> Data Peserta Didik
            </a>
            <a href="{{ url('/admin/shift') }}" class="flex items-center px-4 py-3 text-teal-100 hover:bg-teal-700 hover:text-white rounded-xl transition">
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
                <h2 class="text-2xl font-bold text-gray-800">Manajemen Peserta Didik</h2>
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
                <i class="fas fa-check-circle text-xl"></i> {{ session('success') }}
            </div>
            @endif

            @if(session('error'))
            <div class="bg-red-100 border border-red-200 text-red-700 px-6 py-4 rounded-2xl mb-6 flex items-center gap-3 font-bold shadow-sm">
                <i class="fas fa-exclamation-triangle text-xl"></i> {{ session('error') }}
            </div>
            @endif

            <div class="grid grid-cols-1 xl:grid-cols-3 gap-8">

                <!-- KOLOM KIRI (Berisi Form Manual & Form Import) -->
                <div class="flex flex-col gap-6">

                    <!-- Formulir Tambah Peserta Manual -->
                    <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 h-max">
                        <h3 class="font-bold text-lg text-gray-800 mb-4 border-b pb-3 border-gray-100">
                            <i class="fas fa-user-plus text-teal-500 mr-2"></i> Pendaftaran Baru
                        </h3>

                        <form action="{{ url('/admin/peserta/tambah') }}" method="POST" class="space-y-4">
                            @csrf
                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">NIM (Nomor Induk)</label>
                                    <input type="text" name="nim" required placeholder="Contoh: 2150001" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Password Aplikasi</label>
                                    <input type="text" name="password" required placeholder="Buat password..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>
                            </div>

                            <div>
                                <label class="block text-xs font-bold text-gray-700 mb-1">Nama Lengkap</label>
                                <input type="text" name="nama_lengkap" required placeholder="Contoh: Budi Santoso" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                            </div>

                            <div class="grid grid-cols-2 gap-4">
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Gelar (Opsional)</label>
                                    <input type="text" name="gelar" placeholder="S.Ked / S.Kep" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>
                                <div>
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Status Pendidikan</label>
                                    <input list="pilihan_status" name="status" required placeholder="Pilih manual..." autocomplete="off" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                    <datalist id="pilihan_status">
                                        @if($admin_role == 'Kedokteran')
                                        <option value="Dokter Muda">
                                        <option value="PPDS">
                                            @elseif($admin_role == 'Keperawatan')
                                        <option value="Perawat Magang">
                                        <option value="Ners Muda">
                                            @else
                                        <option value="Tenaga Magang">
                                            @endif
                                    </datalist>
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Program Studi / Bagian</label>
                                    <input type="text" name="program_studi" required placeholder="Penyakit Dalam / Bedah..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>
                                <div class="col-span-2">
                                    <label class="block text-xs font-bold text-gray-700 mb-1">Institusi / Universitas</label>
                                    <input type="text" name="institusi" required placeholder="Universitas Brawijaya..." class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2.5 text-sm focus:ring-2 focus:ring-teal-500 outline-none">
                                </div>
                            </div>

                            <div class="mt-6 pt-4 border-t border-gray-100">
                                <p class="text-[10px] text-gray-400 mb-3 text-center">Data akan dimasukkan ke kelompok: <b>{{ $admin_role }}</b></p>
                                <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-xl py-3 transition shadow-lg shadow-teal-500/30">
                                    <i class="fas fa-save mr-1"></i> Simpan Peserta Didik
                                </button>
                            </div>
                        </form>
                    </div>

                    @if($admin_role == 'Kedokteran')
                    <!-- KOTAK BARU: IMPORT DATA MASSAL (HANYA KEDOKTERAN) -->
                    <div class="bg-gradient-to-br from-indigo-800 to-indigo-900 rounded-3xl shadow-lg border border-indigo-700 p-6 text-white h-max relative overflow-hidden">
                        <!-- Ornamen Latar Belakang -->
                        <i class="fas fa-file-excel absolute -right-6 -bottom-6 text-indigo-700/50 text-8xl transform -rotate-12"></i>

                        <h3 class="font-bold text-lg mb-2 relative z-10">
                            <i class="fas fa-cloud-upload-alt text-indigo-300 mr-2"></i> Import Data Massal
                        </h3>
                        <p class="text-xs text-indigo-200 mb-5 relative z-10">Unggah ribuan data peserta didik sekaligus menggunakan file format CSV.</p>

                        <div class="space-y-4 relative z-10">
                            <!-- Tombol Download Template -->
                            <a href="{{ url('/admin/peserta/template-csv') }}" class="flex items-center justify-center gap-2 w-full bg-indigo-700 hover:bg-indigo-600 border border-indigo-500 text-white font-bold rounded-xl py-2.5 transition text-sm">
                                <i class="fas fa-download"></i> 1. Download Template CSV
                            </a>

                            <!-- Form Upload File CSV -->
                            <form action="{{ url('/admin/peserta/import') }}" method="POST" enctype="multipart/form-data" class="space-y-3">
                                @csrf
                                <div class="bg-indigo-950/50 border border-indigo-600/50 rounded-xl p-3">
                                    <label class="block text-[10px] font-bold text-indigo-300 mb-2 uppercase tracking-wider">2. Upload File CSV</label>
                                    <input type="file" name="file_csv" accept=".csv" required class="block w-full text-xs text-indigo-200 file:mr-3 file:py-1.5 file:px-3 file:rounded-lg file:border-0 file:text-xs file:font-bold file:bg-indigo-500 file:text-white hover:file:bg-indigo-400 cursor-pointer">
                                </div>
                                <button type="submit" class="w-full bg-teal-500 hover:bg-teal-400 text-white font-bold rounded-xl py-3 transition shadow-lg text-sm">
                                    <i class="fas fa-rocket mr-1"></i> Mulai Proses Import
                                </button>
                            </form>
                        </div>
                    </div>
                    @endif

                </div>

                <!-- Tabel Data Peserta -->
                <div class="xl:col-span-2 bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                    <div class="px-6 py-5 flex justify-between items-center bg-gray-50/50">
                        <h3 class="font-bold text-lg text-gray-800"><i class="fas fa-users text-teal-600 mr-2"></i> Daftar Peserta Aktif ({{ $admin_role }})</h3>
                        <span class="bg-teal-100 text-teal-800 text-xs font-bold px-3 py-1 rounded-full">{{ count($data_peserta) }} Orang</span>
                    </div>

                    <div class="overflow-x-auto">
                        <!-- Penambahan Class tabel-presensi -->
                        <table class="w-full text-left border-collapse tabel-presensi">
                            <thead>
                                <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider rounded-xl">
                                    <th class="px-4 py-3 font-bold rounded-l-xl">Profil Peserta</th>
                                    <th class="px-4 py-3 font-bold">Studi & Institusi</th>
                                    <th class="px-4 py-3 font-bold text-center">Status</th>
                                    <th class="px-4 py-3 font-bold text-center rounded-r-xl">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="text-sm divide-y divide-gray-50">

                                <!-- Ganti forelse menjadi foreach -->
                                @foreach($data_peserta as $row)
                                <tr class="hover:bg-teal-50/30 transition">
                                    <td class="px-4 py-4">
                                        <div class="flex items-center gap-3">
                                            <div class="w-10 h-10 rounded-full bg-teal-100 flex items-center justify-center text-teal-600 font-bold border border-teal-200">
                                                {{ substr($row->nama_lengkap, 0, 1) }}
                                            </div>
                                            <div>
                                                <p class="font-bold text-gray-800">{{ $row->nama_lengkap }}{{ $row->gelar ? ', '.$row->gelar : '' }}</p>
                                                <p class="text-xs text-gray-500 font-medium">NIM: {{ $row->nim }}</p>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="px-4 py-4">
                                        <p class="font-bold text-gray-700">{{ $row->program_studi }}</p>
                                        <p class="text-xs text-gray-500">{{ $row->institusi }}</p>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <span class="bg-blue-50 text-blue-600 border border-blue-100 px-2 py-1 rounded text-[10px] font-bold uppercase tracking-wider">
                                            {{ $row->status }}
                                        </span>
                                    </td>
                                    <td class="px-4 py-4 text-center">
                                        <a href="{{ url('/admin/peserta/hapus/'.$row->nim) }}" onclick="return confirm('Hapus {{ $row->nama_lengkap }} dari sistem? (Semua data riwayat presensinya juga akan ikut terhapus secara permanen)')" class="inline-flex items-center justify-center w-8 h-8 rounded-lg bg-red-50 text-red-500 hover:bg-red-500 hover:text-white transition">
                                            <i class="fas fa-trash-alt text-xs"></i>
                                        </a>
                                    </td>
                                </tr>
                                @endforeach

                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
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
                        placeholder: "Cari nama/NIM...",
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