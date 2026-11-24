<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Presensi - Diklit RSSA</title>
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

        /* CSS KHUSUS UNTUK MAGIC PRINT */
        @media print {
            @page {
                size: landscape;
                margin: 15mm;
            }

            body {
                background-color: white !important;
            }

            /* Menyembunyikan elemen yang tidak perlu dicetak (termasuk kolom cari dan pemisah halaman) */
            .no-print,
            .datatable-top,
            .datatable-bottom {
                display: none !important;
            }

            .print-only {
                display: block !important;
            }

            main {
                overflow: visible !important;
                height: auto !important;
            }

            /* Merapikan tabel saat dicetak */
            .print-table {
                width: 100%;
                border-collapse: collapse;
            }

            .print-table th,
            .print-table td {
                border: 1px solid #000 !important;
                padding: 8px !important;
                color: #000 !important;
            }

            .print-table th {
                background-color: #f3f4f6 !important;
                -webkit-print-color-adjust: exact;
            }

            /* Menghilangkan bayangan dan border berlebih */
            .shadow-sm,
            .rounded-3xl,
            .rounded-xl {
                box-shadow: none !important;
                border-radius: 0 !important;
                border: none !important;
            }
        }

        .print-only {
            display: none;
        }
    </style>
</head>

<body class="flex h-screen overflow-hidden">

    <!-- Sidebar (Otomatis Menghilang Saat Print) -->
    <aside class="w-64 bg-teal-800 text-white flex flex-col shadow-xl z-20 hidden md:flex no-print">
        <div class="p-6 flex items-center justify-center border-b border-teal-700">
            <!-- LOGO RSSA LOKAL -->
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
            <a href="{{ url('/admin/laporan') }}" class="flex items-center px-4 py-3 bg-teal-700 text-white rounded-xl shadow-inner font-semibold transition">
                <i class="fas fa-print w-6"></i> Laporan Presensi
            </a>
            <!-- KEMBALIKAN MENU PENGATURAN -->
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

        <!-- Header (Otomatis Menghilang Saat Print) -->
        <header class="bg-white shadow-sm px-8 py-4 flex items-center justify-between z-10 no-print">
            <div class="flex items-center text-gray-500">
                <i class="fas fa-bars text-xl mr-4 cursor-pointer md:hidden"></i>
                <h2 class="text-2xl font-bold text-gray-800">Cetak Laporan Kehadiran</h2>
            </div>
            <div class="flex items-center gap-4">
                <a href="{{ url('/admin/logout') }}" class="bg-red-50 text-red-600 px-4 py-2 rounded-lg font-bold hover:bg-red-100 transition flex items-center gap-2">
                    <i class="fas fa-sign-out-alt"></i> Keluar
                </a>
            </div>
        </header>

        <!-- KOP SURAT (HANYA MUNCUL SAAT DI PRINT) -->
        <div class="print-only text-center border-b-4 border-black pb-4 mb-6">
            <h1 class="text-2xl font-black uppercase">RUMAH SAKIT UMUM DAERAH DR. SAIFUL ANWAR</h1>
            <h2 class="text-lg font-bold">BIDANG PENDIDIKAN DAN PENELITIAN (DIKLIT)</h2>
            <p class="text-sm">Jl. Jaksa Agung Suprapto No.2, Klojen, Kota Malang, Jawa Timur 65111</p>
            <p class="text-sm">Laporan Kehadiran Kelompok: <b>{{ strtoupper($admin_role) }}</b></p>
            <p class="text-sm">
                Periode:
                @if(request('tgl_mulai') && request('tgl_akhir'))
                {{ request('tgl_mulai') }} s/d {{ request('tgl_akhir') }}
                @else
                Semua Riwayat (Tanpa Filter)
                @endif
            </p>
        </div>

        <!-- Konten Halaman -->
        <div class="p-8 pb-20">

            <!-- PANEL FILTER (Menghilang saat print) -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 p-6 mb-8 no-print">
                <h3 class="font-bold text-gray-800 mb-4 border-b border-gray-100 pb-3 flex items-center gap-2">
                    <i class="fas fa-filter text-teal-600"></i> Saring Data Laporan
                </h3>

                <form action="{{ url('/admin/laporan') }}" method="GET">
                    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-4">

                        <!-- Filter Bulan/Tahun via Rentang Tanggal -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Dari Tanggal (Mulai)</label>
                            <input type="date" name="tgl_mulai" value="{{ request('tgl_mulai') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        </div>

                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Sampai Tanggal (Akhir)</label>
                            <input type="date" name="tgl_akhir" value="{{ request('tgl_akhir') }}" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                        </div>

                        <!-- Filter Status -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Status Pendidikan</label>
                            <select name="status" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Semua Status --</option>
                                @foreach($list_status as $s)
                                <option value="{{ $s }}" {{ request('status') == $s ? 'selected' : '' }}>{{ $s }}</option>
                                @endforeach
                            </select>
                        </div>

                        <!-- Filter Program Studi -->
                        <div>
                            <label class="block text-xs font-bold text-gray-700 mb-1">Program Studi / Bagian</label>
                            <select name="prodi" class="w-full bg-gray-50 border border-gray-200 rounded-xl px-4 py-2 text-sm focus:ring-2 focus:ring-teal-500">
                                <option value="">-- Semua Prodi --</option>
                                @foreach($list_prodi as $p)
                                <option value="{{ $p }}" {{ request('prodi') == $p ? 'selected' : '' }}>{{ $p }}</option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-5 flex items-center justify-between">
                        <p class="text-xs text-gray-500"><i class="fas fa-info-circle text-teal-500"></i> <b>Tips:</b> Untuk laporan bulanan, set tanggal awal ke tgl 1, dan tanggal akhir ke tgl 30/31 bulan tersebut.</p>

                        <div class="flex gap-3">
                            <a href="{{ url('/admin/laporan') }}" class="bg-gray-100 text-gray-600 px-6 py-2.5 rounded-xl font-bold hover:bg-gray-200 transition text-sm">
                                <i class="fas fa-undo mr-1"></i> Reset
                            </a>
                            <button type="submit" class="bg-teal-600 hover:bg-teal-700 text-white px-6 py-2.5 rounded-xl font-bold shadow-lg shadow-teal-500/30 transition text-sm">
                                <i class="fas fa-search mr-1"></i> Terapkan Filter
                            </button>
                        </div>
                    </div>
                </form>
            </div>

            <!-- AREA TABEL LAPORAN -->
            <div class="bg-white rounded-3xl shadow-sm border border-gray-100 overflow-hidden">
                <!-- Baris aksi (Menghilang saat print) -->
                <div class="px-6 py-5 flex justify-between items-center bg-gray-50/50 no-print">
                    <div>
                        <h3 class="font-bold text-lg text-gray-800"><i class="fas fa-file-alt text-teal-600 mr-2"></i> Hasil Laporan</h3>
                        <p class="text-xs text-gray-500 mt-1">Ditemukan <b>{{ count($data_laporan) }}</b> data presensi sesuai filter.</p>
                    </div>
                    <!-- PANGGIL FUNGSI CETAK PINTAR -->
                    <button onclick="cetakLaporanPintar()" class="bg-gray-800 hover:bg-black text-white px-5 py-2.5 rounded-xl font-bold shadow-lg transition flex items-center gap-2">
                        <i class="fas fa-print"></i> CETAK KE KERTAS / PDF
                    </button>
                </div>

                <div class="overflow-x-auto">
                    <!-- Penambahan Class tabel-presensi -->
                    <table class="w-full text-left border-collapse print-table tabel-presensi">
                        <thead>
                            <tr class="bg-gray-100 text-gray-600 text-xs uppercase tracking-wider rounded-xl">
                                <th class="px-4 py-3 font-bold rounded-l-xl">Tanggal</th>
                                <th class="px-4 py-3 font-bold">Identitas Peserta</th>
                                <th class="px-4 py-3 font-bold">Status & Prodi</th>
                                <th class="px-4 py-3 font-bold">Jadwal Shift</th>
                                <th class="px-4 py-3 font-bold">Jam Datang</th>
                                <th class="px-4 py-3 font-bold rounded-r-xl">Jam Pulang</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm divide-y divide-gray-50">

                            <!-- Ganti forelse menjadi foreach -->
                            @foreach($data_laporan as $row)
                            <tr class="hover:bg-teal-50/30 transition">
                                <td class="px-4 py-3 font-bold text-gray-700 whitespace-nowrap">{{ $row->tanggal }}</td>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-gray-900">{{ $row->nama_lengkap }}</p>
                                    <p class="text-xs text-gray-500">NIM: {{ $row->nim }}</p>
                                </td>
                                <td class="px-4 py-3">
                                    <p class="font-bold text-teal-700 text-xs">{{ $row->status }}</p>
                                    <p class="text-xs text-gray-600">{{ $row->program_studi }}</p>
                                </td>
                                <td class="px-4 py-3 font-medium text-gray-700">{{ $row->shift }}</td>
                                <td class="px-4 py-3">
                                    <span class="font-bold text-blue-700 block">{{ $row->jam_datang }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $row->status_datang }}</span>
                                </td>
                                <td class="px-4 py-3">
                                    @if($row->jam_pulang)
                                    <span class="font-bold text-orange-700 block">{{ $row->jam_pulang }}</span>
                                    <span class="text-[10px] text-gray-500">{{ $row->status_pulang }}</span>
                                    @else
                                    <span class="text-red-500 font-bold text-xs italic">Belum Pulang</span>
                                    @endif
                                </td>
                            </tr>
                            @endforeach

                        </tbody>
                    </table>
                </div>
            </div>

        </div>
    </main>

    <!-- Script Inisialisasi DataTables & Cetak Pintar -->
    <script src="https://cdn.jsdelivr.net/npm/simple-datatables@9.0.3" type="text/javascript"></script>
    <script>
        let myDataTable; // Simpan dalam variabel agar bisa dipanggil saat mau cetak

        document.addEventListener("DOMContentLoaded", function() {
            const table = document.querySelector(".tabel-presensi");
            if (table) {
                myDataTable = new simpleDatatables.DataTable(table, {
                    labels: {
                        placeholder: "Cari data laporan...",
                        perPage: "Data per halaman",
                        noRows: "Tidak ada data yang sesuai filter",
                        info: "Menampilkan {start} s/d {end} dari total {rows} data",
                    },
                    perPageSelect: [10, 25, 50, 100],
                    perPage: 10
                });
            }
        });

        // Trik khusus agar saat di-print, tabel otomatis menampilkan semua data tanpa terpotong halaman 1
        function cetakLaporanPintar() {
            if (myDataTable) {
                myDataTable.update({
                    perPage: 10000
                }); // Paksa tampilkan semua baris (batas 10000 data)

                // Beri waktu 0.5 detik untuk browser memuat baris, lalu jalankan menu Print
                setTimeout(() => {
                    window.print();
                    myDataTable.update({
                        perPage: 10
                    }); // Setelah print selesai, kembalikan ke 10 baris per halaman
                }, 500);
            } else {
                window.print();
            }
        }
    </script>
</body>

</html>