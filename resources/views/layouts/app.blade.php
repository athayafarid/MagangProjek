<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Dashboard') - PT Besmindo Materi Sawatama Inventory</title>
    <!-- Google Fonts - Plus Jakarta Sans -->
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <!-- Material Design Icons -->
    <link href="https://cdn.jsdelivr.net/npm/@mdi/font@7.4.47/css/materialdesignicons.min.css" rel="stylesheet">
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <!-- JSBarcode -->
    <script src="https://cdn.jsdelivr.net/npm/jsbarcode@3.11.5/dist/JsBarcode.all.min.js"></script>
    <!-- QRCode Generator -->
    <script src="https://cdn.jsdelivr.net/npm/qrcode-generator@1.4.4/qrcode.min.js"></script>
    <!-- HTML5-QRCode Scanner -->
    <script src="https://unpkg.com/html5-qrcode@2.3.8/html5-qrcode.min.js"></script>
    
    @vite(['resources/css/app.css', 'resources/js/app.js'])
    @stack('styles')
</head>
<body class="bg-slate-50 text-slate-800 antialiased font-sans min-h-screen flex flex-col">

<div class="flex min-h-screen relative overflow-x-hidden">

    <!-- Mobile Sidebar Backdrop Overlay -->
    <div id="sidebarBackdrop" onclick="toggleSidebar()" class="fixed inset-0 bg-slate-900/60 backdrop-blur-xs z-40 lg:hidden hidden transition-opacity duration-300"></div>

    <!-- ===== SIDEBAR NAVIGATION ===== -->
    <aside id="sidebar" class="fixed top-0 bottom-0 left-0 w-64 bg-slate-950 text-slate-300 flex flex-col z-50 transform -translate-x-full lg:translate-x-0 transition-transform duration-300 ease-in-out border-r border-slate-800/80 shadow-2xl">
        
        <!-- Logo Branding Header (Fixed Height, Shrink 0) -->
        <div class="h-16 shrink-0 px-4 bg-gradient-to-r from-slate-950 via-slate-900 to-emerald-950 flex items-center justify-between border-b border-emerald-500/20">
            <a href="{{ route('dashboard') }}" class="flex items-center gap-3 group">
                <div class="w-9 h-9 rounded-xl bg-emerald-600 flex items-center justify-center text-white shadow-md shadow-emerald-600/30 group-hover:scale-105 transition-transform border border-emerald-400/30">
                    <img src="{{ asset('images/logo.png') }}" alt="Besmindo" class="max-h-6 max-w-6 object-contain filter drop-shadow">
                </div>
                <div class="flex flex-col leading-tight">
                    <span class="font-extrabold text-white text-sm tracking-wide group-hover:text-emerald-300 transition-colors">BESMINDO</span>
                    <span class="text-[10px] text-emerald-400 font-bold tracking-wider uppercase">Inventory System</span>
                </div>
            </a>
            <button onclick="toggleSidebar()" class="lg:hidden text-slate-400 hover:text-white p-1 rounded-md">
                <i class="mdi mdi-close text-xl"></i>
            </button>
        </div>

        <!-- Scrollable Navigation Items (Flex-1 Overflow Y Auto) -->
        <div id="sidebarNav" class="flex-1 overflow-y-auto px-3 py-4 space-y-6">

            <!-- MAIN MENU -->
            <div>
                <a href="{{ route('dashboard') }}"
                   class="flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-semibold transition-all {{ request()->routeIs('dashboard') ? 'sidebar-active bg-emerald-600 text-white shadow-lg shadow-emerald-900/40' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                    <i class="mdi mdi-view-dashboard-outline text-lg {{ request()->routeIs('dashboard') ? 'text-white' : 'text-slate-400' }}"></i>
                    <span>Dashboard</span>
                </a>
                
                <a href="{{ route('barcode.scan') }}"
                   class="mt-2 flex items-center justify-between px-3.5 py-2.5 rounded-xl text-xs font-bold transition-all border border-emerald-500/40 bg-emerald-500/10 text-emerald-400 hover:bg-emerald-500/20 shadow-xs {{ request()->routeIs('barcode.scan') ? 'sidebar-active' : '' }}">
                    <div class="flex items-center gap-3">
                        <i class="mdi mdi-qrcode-scan text-lg text-emerald-400"></i>
                        <span>Scan Barcode / QR</span>
                    </div>
                    <span class="px-1.5 py-0.5 text-[9px] uppercase font-extrabold bg-emerald-500 text-slate-950 rounded-md">Live</span>
                </a>
            </div>

            <!-- MASTER DATA SECTION -->
            <div>
                <div class="px-3.5 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Master Data
                </div>
                <div class="space-y-1">
                    <a href="{{ route('items.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('items.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md shadow-emerald-900/30' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-package-variant-closed text-lg {{ request()->routeIs('items.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Barang Inventory</span>
                    </a>

                    @if(Auth::user()->isAdmin())
                    <a href="{{ route('categories.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('categories.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-tag-multiple-outline text-lg {{ request()->routeIs('categories.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Kategori</span>
                    </a>
                    <a href="{{ route('units.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('units.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-ruler text-lg {{ request()->routeIs('units.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Satuan</span>
                    </a>
                    <a href="{{ route('locations.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('locations.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-map-marker-outline text-lg {{ request()->routeIs('locations.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Lokasi / Rak</span>
                    </a>
                    <a href="{{ route('suppliers.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('suppliers.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-truck-outline text-lg {{ request()->routeIs('suppliers.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Supplier</span>
                    </a>
                    <a href="{{ route('users.index') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('users.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-account-group-outline text-lg {{ request()->routeIs('users.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>User Management</span>
                    </a>
                    @endif
                </div>
            </div>

            <!-- INVENTORY OPERATIONS -->
            <div>
                <div class="px-3.5 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Manajemen Inventory
                </div>
                <div class="space-y-1">
                    <a href="{{ route('inventory.stock') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('inventory.stock') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-layers-outline text-lg {{ request()->routeIs('inventory.stock') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Stok Realtime</span>
                    </a>
                    <a href="{{ route('inventory.incoming') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('inventory.incoming') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-arrow-bottom-left text-lg text-emerald-400"></i>
                        <span>Barang Masuk</span>
                    </a>
                    <a href="{{ route('inventory.outgoing') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('inventory.outgoing') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-arrow-top-right text-lg text-rose-400"></i>
                        <span>Barang Keluar</span>
                    </a>
                    <a href="{{ route('inventory.adjustments') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('inventory.adjustments') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-tune-variant text-lg {{ request()->routeIs('inventory.adjustments') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Penyesuaian Stok</span>
                    </a>
                    <a href="{{ route('inventory.opnames') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('inventory.opnames') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-clipboard-check-outline text-lg {{ request()->routeIs('inventory.opnames') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Stock Opname</span>
                    </a>
                </div>
            </div>

            <!-- BARCODE TOOLS -->
            @if(Auth::user()->isAdmin())
            <div>
                <div class="px-3.5 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Barcode & Label
                </div>
                <div class="space-y-1">
                    <a href="{{ route('barcode.generate') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('barcode.generate') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-printer-outline text-lg {{ request()->routeIs('barcode.generate') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Cetak Barcode / QR</span>
                    </a>
                </div>
            </div>
            @endif

            <!-- REPORTS -->
            <div>
                <div class="px-3.5 mb-2 text-[11px] font-bold text-slate-400 uppercase tracking-wider flex items-center gap-1.5">
                    <span class="w-1.5 h-1.5 rounded-full bg-emerald-500"></span> Laporan & Audit
                </div>
                <div class="space-y-1">
                    <a href="{{ route('reports.stock') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('reports.*') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-file-document-outline text-lg {{ request()->routeIs('reports.*') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Laporan Inventory</span>
                    </a>
                    <a href="{{ route('inventory.history') }}"
                       class="flex items-center gap-3 px-3.5 py-2 rounded-xl text-xs font-medium transition-all {{ request()->routeIs('inventory.history') ? 'sidebar-active bg-emerald-600 text-white font-bold shadow-md' : 'hover:bg-slate-900 hover:text-emerald-300 text-slate-300' }}">
                        <i class="mdi mdi-history text-lg {{ request()->routeIs('inventory.history') ? 'text-white' : 'text-slate-400' }}"></i>
                        <span>Riwayat Transaksi</span>
                    </a>
                </div>
            </div>

        </div>

        <!-- Sidebar Footer / User Profile Card (Fixed Height, Shrink 0) -->
        <div class="p-3 shrink-0 border-t border-slate-800/80 bg-slate-950">
            <div class="flex items-center gap-3 p-2 rounded-xl bg-slate-900/90 border border-slate-800">
                <div class="w-9 h-9 rounded-xl bg-emerald-600/20 text-emerald-400 border border-emerald-500/30 flex items-center justify-center font-extrabold text-sm shrink-0">
                    {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-xs font-bold text-slate-200 truncate">{{ Auth::user()->name }}</p>
                    <span class="inline-block px-1.5 py-0.2 rounded text-[10px] font-bold uppercase bg-emerald-500/20 text-emerald-400 border border-emerald-500/30 mt-0.5">
                        {{ Auth::user()->role }}
                    </span>
                </div>
                <form action="{{ route('logout') }}" method="POST" class="m-0">
                    @csrf
                    <button type="submit" title="Logout" class="p-2 text-slate-400 hover:text-rose-400 hover:bg-slate-800 rounded-lg transition-colors">
                        <i class="mdi mdi-logout text-lg"></i>
                    </button>
                </form>
            </div>
        </div>
    </aside>

    <!-- ===== MAIN CONTENT WRAPPER ===== -->
    <div class="flex-1 lg:pl-64 flex flex-col min-w-0 min-h-screen">

        <!-- Top Header Navigation Bar -->
        <header class="sticky top-0 z-30 h-16 bg-white/90 backdrop-blur-md border-b border-slate-200/80 px-4 lg:px-8 flex items-center justify-between no-print shadow-2xs">
            <div class="flex items-center gap-3">
                <button onclick="toggleSidebar()" class="lg:hidden p-2 rounded-xl text-slate-600 hover:bg-slate-100 focus:outline-none">
                    <i class="mdi mdi-menu text-2xl"></i>
                </button>
                <div>
                    <h1 class="text-lg font-extrabold text-slate-900 tracking-tight">@yield('title', 'Dashboard')</h1>
                    <p class="text-xs text-emerald-700 font-semibold hidden sm:block">PT Besmindo Materi Sawatama</p>
                </div>
            </div>

            <div class="flex items-center gap-3">
                <a href="{{ route('barcode.scan') }}" class="inline-flex items-center gap-2 px-3.5 py-1.5 rounded-xl text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200/80 transition-all no-print shadow-xs">
                    <i class="mdi mdi-barcode-scan text-base text-emerald-600"></i>
                    <span class="hidden sm:inline">Scan Cepat</span>
                </a>
                
                <div class="hidden md:flex items-center gap-2 px-3.5 py-1.5 rounded-xl bg-slate-100 text-xs font-semibold text-slate-600 border border-slate-200/80">
                    <i class="mdi mdi-calendar-outline text-slate-500 text-sm"></i>
                    <span>{{ now()->translatedFormat('l, d F Y') }}</span>
                </div>
            </div>
        </header>

        <!-- Main Content Area -->
        <main class="flex-1 p-4 lg:p-8 space-y-6">
            
            <!-- Alert Session Messages -->
            @if (session('success'))
                <div class="p-4 rounded-2xl bg-emerald-50 border border-emerald-200 text-emerald-800 flex items-start gap-3 shadow-xs no-print transition-all" id="flash-success">
                    <i class="mdi mdi-check-circle-outline text-xl text-emerald-600 shrink-0"></i>
                    <div class="flex-1 text-sm font-semibold pt-0.5">{{ session('success') }}</div>
                    <button onclick="document.getElementById('flash-success').remove()" class="text-emerald-500 hover:text-emerald-700">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            @endif

            @if (session('info'))
                <div class="p-4 rounded-2xl bg-sky-50 border border-sky-200 text-sky-800 flex items-start gap-3 shadow-xs no-print transition-all" id="flash-info">
                    <i class="mdi mdi-information-outline text-xl text-sky-600 shrink-0"></i>
                    <div class="flex-1 text-sm font-semibold pt-0.5">{{ session('info') }}</div>
                    <button onclick="document.getElementById('flash-info').remove()" class="text-sky-500 hover:text-sky-700">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            @endif

            @if ($errors->any())
                <div class="p-4 rounded-2xl bg-rose-50 border border-rose-200 text-rose-800 flex items-start gap-3 shadow-xs no-print transition-all" id="flash-error">
                    <i class="mdi mdi-alert-outline text-xl text-rose-600 shrink-0"></i>
                    <div class="flex-1 text-sm pt-0.5">
                        <strong class="font-bold">Terjadi kesalahan input:</strong>
                        <ul class="mt-1 list-disc list-inside space-y-0.5 font-medium">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                    <button onclick="document.getElementById('flash-error').remove()" class="text-rose-500 hover:text-rose-700">
                        <i class="mdi mdi-close"></i>
                    </button>
                </div>
            @endif

            @yield('content')
        </main>

        <!-- Footer -->
        <footer class="mt-auto border-t border-slate-200/80 bg-white py-4 px-4 lg:px-8 text-xs text-slate-500 flex flex-col sm:flex-row justify-between items-center gap-2 no-print">
            <span>© {{ date('Y') }} <strong class="text-slate-700">PT Besmindo Materi Sawatama</strong>. All rights reserved.</span>
            <span class="font-bold text-emerald-700 bg-emerald-50 px-2.5 py-1 rounded-md border border-emerald-200/60">Inventory Management System</span>
        </footer>

    </div>

</div>

<script>
    function toggleSidebar() {
        const sidebar = document.getElementById('sidebar');
        const backdrop = document.getElementById('sidebarBackdrop');
        if (sidebar.classList.contains('-translate-x-full')) {
            sidebar.classList.remove('-translate-x-full');
            backdrop.classList.remove('hidden');
        } else {
            sidebar.classList.add('-translate-x-full');
            backdrop.classList.add('hidden');
        }
    }

    // Persist and restore sidebar scroll position across page navigation
    document.addEventListener("DOMContentLoaded", function() {
        const sidebarNav = document.getElementById('sidebarNav');
        if (sidebarNav) {
            const savedPos = sessionStorage.getItem('sidebarScrollPos');
            if (savedPos !== null) {
                sidebarNav.scrollTop = parseInt(savedPos, 10);
            } else {
                const activeItem = sidebarNav.querySelector('.sidebar-active');
                if (activeItem) {
                    activeItem.scrollIntoView({ block: 'nearest' });
                }
            }

            sidebarNav.addEventListener('scroll', function() {
                sessionStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
            });

            sidebarNav.querySelectorAll('a').forEach(link => {
                link.addEventListener('click', function() {
                    sessionStorage.setItem('sidebarScrollPos', sidebarNav.scrollTop);
                });
            });
        }
    });

    // Auto-dismiss alerts after 5s
    ['flash-success', 'flash-info'].forEach(id => {
        const el = document.getElementById(id);
        if (el) {
            setTimeout(() => {
                el.style.opacity = '0';
                el.style.transition = 'opacity 0.3s ease';
                setTimeout(() => el.remove(), 300);
            }, 5000);
        }
    });
</script>
@stack('scripts')
</body>
</html>
