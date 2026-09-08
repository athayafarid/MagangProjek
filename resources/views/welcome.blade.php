<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>PT Besmindo Materi Sawatama - Inventory Management System</title>
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    @vite(['resources/css/app.css', 'resources/js/app.js'])
</head>
<body class="bg-slate-900 text-slate-100 font-sans antialiased min-h-screen flex flex-col justify-between p-6 sm:p-12">

    <!-- Header Navigation -->
    <header class="max-w-6xl mx-auto w-full flex items-center justify-between">
        <div class="flex items-center gap-3">
            <div class="w-10 h-10 rounded-xl bg-blue-600 flex items-center justify-center text-white shadow-lg shadow-blue-500/30">
                <img src="{{ asset('images/logo.png') }}" alt="Besmindo" class="max-h-7 max-w-7 object-contain filter drop-shadow">
            </div>
            <div>
                <span class="font-bold text-white text-base tracking-wide block">BESMINDO</span>
                <span class="text-[10px] text-blue-400 font-semibold tracking-wider uppercase block">Inventory System</span>
            </div>
        </div>

        <div>
            @auth
                <a href="{{ route('dashboard') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-600/30 transition-all">
                    Masuk Ke Dashboard <i class="mdi mdi-arrow-right"></i>
                </a>
            @else
                <a href="{{ route('login') }}" class="px-5 py-2.5 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-600/30 transition-all">
                    Login Pengguna <i class="mdi mdi-login"></i>
                </a>
            @endauth
        </div>
    </header>

    <!-- Main Hero Section -->
    <main class="max-w-4xl mx-auto w-full my-auto text-center space-y-8 py-12">
        <div class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-full bg-blue-500/10 border border-blue-500/20 text-blue-400 text-xs font-semibold">
            <i class="mdi mdi-shield-check-outline text-sm"></i> Enterprise Warehouse Management System
        </div>

        <h1 class="text-3xl sm:text-5xl font-extrabold text-white tracking-tight leading-tight max-w-3xl mx-auto">
            Sistem Informasi Inventory <br><span class="text-blue-500">PT Besmindo Materi Sawatama</span>
        </h1>

        <p class="text-slate-400 text-sm sm:text-base max-w-2xl mx-auto leading-relaxed">
            Platform manajemen stok barang warehouse terpadu dengan pemindaian barcode/QR Code instan, pemantauan mutasi barang masuk & keluar realtime, serta audit stock opname presisi tinggi.
        </p>

        <div class="flex flex-col sm:flex-row items-center justify-center gap-4 pt-4">
            @auth
                <a href="{{ route('dashboard') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xl shadow-blue-600/30 transition-all hover:scale-105">
                    Buka Dashboard Inventory
                </a>
                <a href="{{ route('barcode.scan') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-slate-800 hover:bg-slate-700 border border-slate-700 text-slate-200 font-bold text-sm transition-all">
                    <i class="mdi mdi-qrcode-scan text-emerald-400"></i> Pemindai Barcode
                </a>
            @else
                <a href="{{ route('login') }}" class="w-full sm:w-auto px-8 py-3.5 rounded-2xl bg-blue-600 hover:bg-blue-700 text-white font-bold text-sm shadow-xl shadow-blue-600/30 transition-all hover:scale-105">
                    Masuk Ke Sistem
                </a>
            @endauth
        </div>
    </main>

    <!-- Footer -->
    <footer class="max-w-6xl mx-auto w-full text-center text-xs text-slate-500 border-t border-slate-800/80 pt-6">
        &copy; {{ date('Y') }} PT Besmindo Materi Sawatama. All rights reserved.
    </footer>

</body>
</html>
