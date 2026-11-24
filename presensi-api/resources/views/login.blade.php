<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Presensi RSSA</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css" rel="stylesheet">
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f0fdfa;
        }
    </style>
</head>

<body class="flex items-center justify-center min-h-screen">

    <div class="w-full max-w-md bg-white p-8 rounded-3xl shadow-xl border border-teal-100 relative overflow-hidden">
        <!-- Aksen Warna Atas -->
        <div class="absolute top-0 left-0 w-full h-2 bg-gradient-to-r from-teal-500 to-teal-700"></div>

        <div class="text-center mb-8 pt-4">
            <!-- LOGO RSSA LOKAL -->
            <div class="w-24 h-24 bg-white rounded-full flex items-center justify-center mx-auto mb-4 border-2 border-teal-50 shadow-md p-3">
                <img src="{{ asset('img/logo-rssa.png') }}" alt="Logo RSSA" class="w-full h-full object-contain">
            </div>
            <h1 class="text-2xl font-black text-gray-800">Admin Dasbor</h1>
            <p class="text-sm text-gray-500 mt-1">Presensi Peserta Didik RSSA</p>
        </div>

        <!-- Alert Error jika login gagal -->
        @if(session('error'))
        <div class="bg-red-50 text-red-600 p-4 rounded-xl mb-6 text-sm font-bold flex items-center gap-2 border border-red-100">
            <i class="fas fa-exclamation-circle"></i>
            {{ session('error') }}
        </div>
        @endif

        <form action="{{ url('/admin/login') }}" method="POST" class="space-y-5">
            <!-- CSRF Token Wajib di Laravel -->
            @csrf

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Username Admin</label>
                <div class="relative">
                    <i class="fas fa-user absolute left-4 top-4 text-gray-400"></i>
                    <input type="text" name="username" required placeholder="Contoh: admin_dokter"
                        class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-2xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <div>
                <label class="block text-sm font-bold text-gray-700 mb-2">Password</label>
                <div class="relative">
                    <i class="fas fa-lock absolute left-4 top-4 text-gray-400"></i>
                    <input type="password" name="password" required placeholder="Masukkan password..."
                        class="w-full bg-gray-50 border border-gray-200 text-gray-800 rounded-2xl pl-12 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-teal-500">
                </div>
            </div>

            <button type="submit" class="w-full bg-teal-600 hover:bg-teal-700 text-white font-bold rounded-2xl py-3.5 transition-colors shadow-lg shadow-teal-500/30">
                Masuk ke Dasbor
            </button>
        </form>

        <p class="text-center text-xs text-gray-400 mt-8">
            <i class="fas fa-shield-alt mr-1"></i> Akses Terbatas Khusus Staf Diklit
        </p>
    </div>

</body>

</html>