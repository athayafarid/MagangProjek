@extends('layouts.auth')

@section('title', 'Login System')

@section('content')
<div class="bg-white rounded-2xl p-6 sm:p-8 text-slate-800 shadow-2xl border border-slate-200/80">
    <div class="text-center mb-6">
        <h2 class="text-xl font-extrabold text-slate-900 tracking-tight">Selamat Datang</h2>
        <p class="text-xs text-slate-500 mt-1">Masuk ke Sistem Informasi Inventory Warehouse</p>
    </div>

    @if (session('success'))
        <div class="mb-4 p-3 rounded-xl bg-emerald-50 border border-emerald-200 text-emerald-800 text-xs font-semibold flex items-center gap-2">
            <i class="mdi mdi-check-circle-outline text-base text-emerald-600"></i>
            <span>{{ session('success') }}</span>
        </div>
    @endif

    @if ($errors->any())
        <div class="mb-4 p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-800 text-xs font-semibold flex items-center gap-2">
            <i class="mdi mdi-alert-outline text-base text-rose-600"></i>
            <div>
                @foreach ($errors->all() as $error)
                    <p class="m-0">{{ $error }}</p>
                @endforeach
            </div>
        </div>
    @endif

    <form action="{{ route('login.post') }}" method="POST" class="space-y-4">
        @csrf
        <div>
            <label for="username" class="block text-xs font-semibold text-slate-700 mb-1.5">Username / Email</label>
            <div class="relative">
                <i class="mdi mdi-account-outline absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="text" name="username" id="username" value="{{ old('username') }}" required autofocus
                    placeholder="Masukkan username atau email"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all bg-slate-50/50">
            </div>
        </div>

        <div>
            <label for="password" class="block text-xs font-semibold text-slate-700 mb-1.5">Password</label>
            <div class="relative">
                <i class="mdi mdi-lock-outline absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-lg"></i>
                <input type="password" name="password" id="password" required
                    placeholder="••••••••"
                    class="w-full pl-10 pr-4 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-emerald-500/20 focus:border-emerald-600 transition-all bg-slate-50/50">
            </div>
        </div>

        <div class="flex items-center justify-between">
            <label class="inline-flex items-center gap-2 cursor-pointer text-xs text-slate-600 font-medium">
                <input type="checkbox" name="remember" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500">
                <span>Ingat Saya</span>
            </label>
        </div>

        <button type="submit" class="w-full py-3 px-4 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-bold text-sm shadow-md shadow-emerald-600/30 flex items-center justify-center gap-2 transition-all hover:shadow-lg">
            <span>Masuk ke Sistem</span>
            <i class="mdi mdi-arrow-right text-lg"></i>
        </button>
    </form>

    <div class="mt-6 pt-4 border-t border-slate-100 text-center">
        <p class="text-[11px] text-slate-400">
            &copy; {{ date('Y') }} PT Besmindo Materi Sawatama.<br>Hanya untuk pengguna yang terotorisasi.
        </p>
    </div>
</div>

<!-- Quick Login Helper for Testing -->
<div class="bg-slate-900/90 backdrop-blur border border-slate-800 rounded-2xl p-4 text-slate-200 shadow-xl">
    <p class="text-xs font-semibold mb-3 flex items-center gap-2 text-slate-300">
        <i class="mdi mdi-key-variant text-emerald-400 text-base"></i> Akun Uji Coba:
    </p>
    <div class="grid grid-cols-3 gap-2 text-center">
        <button type="button" onclick="fillCreds('admin', 'admin123')"
            class="p-2 rounded-xl border border-slate-700 bg-slate-950/60 hover:bg-slate-800/80 transition-all group">
            <span class="block font-bold text-xs text-emerald-400 group-hover:text-emerald-300">Admin</span>
            <span class="text-[10px] text-slate-400">admin / admin123</span>
        </button>
        <button type="button" onclick="fillCreds('staff', 'staff123')"
            class="p-2 rounded-xl border border-slate-700 bg-slate-950/60 hover:bg-slate-800/80 transition-all group">
            <span class="block font-bold text-xs text-blue-400 group-hover:text-blue-300">Staff</span>
            <span class="text-[10px] text-slate-400">staff / staff123</span>
        </button>
        <button type="button" onclick="fillCreds('supervisor', 'supervisor123')"
            class="p-2 rounded-xl border border-slate-700 bg-slate-950/60 hover:bg-slate-800/80 transition-all group">
            <span class="block font-bold text-xs text-amber-400 group-hover:text-amber-300">Supervisor</span>
            <span class="text-[10px] text-slate-400">supervisor / ...</span>
        </button>
    </div>
</div>

<script>
    function fillCreds(u, p) {
        document.getElementById('username').value = u;
        document.getElementById('password').value = p;
    }
</script>
@endsection
