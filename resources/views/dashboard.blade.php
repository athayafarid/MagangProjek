@extends('layouts.app')

@section('title', 'Dashboard Monitoring Inventory')

@section('content')

<!-- TOP SUMMARY WIDGET CARDS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 mb-6">

    <!-- Total Jenis Barang -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Barang</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($totalItems) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Jenis barang aktif</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-package-variant-closed"></i>
        </div>
    </div>

    <!-- Total Stok keseluruhan -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Stok</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($totalStock) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Unit dalam warehouse</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-layers-outline"></i>
        </div>
    </div>

    <!-- Barang Masuk -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Barang Masuk</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($incomingCount) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Total transaksi masuk</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-sky-50 text-sky-600 border border-sky-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-arrow-bottom-left"></i>
        </div>
    </div>

    <!-- Barang Keluar -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Barang Keluar</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($outgoingCount) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Total transaksi keluar</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-amber-50 text-amber-600 border border-amber-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-arrow-top-right"></i>
        </div>
    </div>

    <!-- Stok Rendah Warning -->
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Rendah</span>
            <span class="text-2xl font-extrabold text-rose-600 mt-1 block">{{ number_format($lowStockCount) }}</span>
            <span class="text-[11px] font-medium text-rose-500 mt-0.5 block">Perlu restock segera</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-alert-outline"></i>
        </div>
    </div>

</div>

<!-- MAIN MIDDLE SECTION: CHART + RECENT ACTIVITIES -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6 mb-6">

    <!-- CHART: GRAFIK BARANG MASUK & KELUAR -->
    <div class="lg:col-span-2 bg-white rounded-2xl p-6 border border-slate-200/80 shadow-xs">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 pb-4 border-b border-slate-100 mb-4">
            <div>
                <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                    <i class="mdi mdi-trending-up text-blue-600"></i>
                    Grafik Barang Masuk vs Barang Keluar
                </h2>
                <p class="text-xs text-slate-500 mt-0.5">Perkembangan tren mutasi stok barang 6 bulan terakhir</p>
            </div>
            <div class="flex items-center gap-4 text-xs font-semibold">
                <span class="inline-flex items-center gap-1.5 text-blue-600">
                    <span class="w-2.5 h-2.5 rounded-full bg-blue-600"></span> Masuk
                </span>
                <span class="inline-flex items-center gap-1.5 text-rose-500">
                    <span class="w-2.5 h-2.5 rounded-full bg-rose-500"></span> Keluar
                </span>
            </div>
        </div>
        <div class="h-72 w-full">
            <canvas id="inventoryChart"></canvas>
        </div>
    </div>

    <!-- RECENT ACTIVITIES FEED -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs flex flex-col overflow-hidden">
        <div class="p-4 sm:px-6 sm:py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="mdi mdi-pulse text-emerald-500"></i>
                Aktivitas Terbaru
            </h2>
            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-50 text-emerald-600 border border-emerald-200">Realtime</span>
        </div>
        <div class="divide-y divide-slate-100 max-h-[340px] overflow-y-auto">
            @forelse ($recentActivities as $act)
                <div class="p-4 flex items-start gap-3 hover:bg-slate-50/80 transition-colors">
                    <div class="w-8 h-8 rounded-lg flex items-center justify-center shrink-0 mt-0.5 text-base
                        @if(str_contains($act->activity_type, 'Masuk'))
                            bg-emerald-50 text-emerald-600 border border-emerald-100
                        @elseif(str_contains($act->activity_type, 'Keluar'))
                            bg-rose-50 text-rose-600 border border-rose-100
                        @elseif(str_contains($act->activity_type, 'Scan'))
                            bg-amber-50 text-amber-600 border border-amber-100
                        @else
                            bg-blue-50 text-blue-600 border border-blue-100
                        @endif
                    ">
                        @if(str_contains($act->activity_type, 'Masuk'))
                            <i class="mdi mdi-arrow-bottom-left"></i>
                        @elseif(str_contains($act->activity_type, 'Keluar'))
                            <i class="mdi mdi-arrow-top-right"></i>
                        @elseif(str_contains($act->activity_type, 'Scan'))
                            <i class="mdi mdi-qrcode-scan"></i>
                        @else
                            <i class="mdi mdi-account-outline"></i>
                        @endif
                    </div>
                    <div class="flex-1 min-w-0">
                        <p class="text-xs font-semibold text-slate-800 leading-snug">{{ $act->description }}</p>
                        <span class="text-[10px] font-medium text-slate-400 mt-1 block">
                            {{ $act->created_at->diffForHumans() }}
                        </span>
                    </div>
                </div>
            @empty
                <div class="p-8 text-center text-slate-400">
                    <i class="mdi mdi-pulse text-3xl block mb-1"></i>
                    <p class="text-xs">Belum ada aktivitas tercatat.</p>
                </div>
            @endforelse
        </div>
    </div>

</div>

<!-- BOTTOM SECTION: LOW STOCK ITEMS ALERT LIST -->
@if ($lowStockItems->count() > 0)
<div class="bg-white rounded-2xl border-l-4 border-l-rose-500 border-t border-r border-b border-slate-200/80 shadow-xs overflow-hidden">
    <div class="p-4 sm:p-6 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
            <h2 class="text-base font-bold text-slate-900 flex items-center gap-2">
                <i class="mdi mdi-alert-circle-outline text-rose-600 text-lg"></i>
                Peringatan Stok Rendah / Habis
            </h2>
            <p class="text-xs text-slate-500 mt-0.5">Daftar barang yang telah mencapai atau berada di bawah batas stok minimum</p>
        </div>
        <a href="{{ route('inventory.stock') }}?status_stock=rendah" class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-semibold bg-slate-100 hover:bg-slate-200 text-slate-700 transition-colors">
            <span>Lihat Semua</span>
            <i class="mdi mdi-arrow-right"></i>
        </a>
    </div>
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50/80 border-b border-slate-100 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3 px-4">Kode</th>
                    <th class="py-3 px-4">Nama Barang</th>
                    <th class="py-3 px-4">Kategori</th>
                    <th class="py-3 px-4">Stok Saat Ini</th>
                    <th class="py-3 px-4">Stok Minimum</th>
                    <th class="py-3 px-4">Lokasi</th>
                    <th class="py-3 px-4">Status</th>
                    <th class="py-3 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @foreach ($lowStockItems as $item)
                <tr class="hover:bg-slate-50/60 transition-colors">
                    <td class="py-3.5 px-4 font-mono font-bold text-slate-600">{{ $item->code }}</td>
                    <td class="py-3.5 px-4 font-semibold text-slate-900">{{ $item->name }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $item->category->name }}</td>
                    <td class="py-3.5 px-4 font-extrabold {{ $item->stock <= 0 ? 'text-rose-600' : 'text-amber-600' }}">
                        {{ $item->stock }} {{ $item->unit->name }}
                    </td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $item->minimum_stock }} {{ $item->unit->name }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $item->location->name }}</td>
                    <td class="py-3.5 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider {{ $item->stock <= 0 ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800' }}">
                            {{ $item->stock_status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-right">
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <a href="{{ route('inventory.incoming') }}?item_id={{ $item->id }}"
                           class="inline-flex items-center gap-1 px-2.5 py-1 rounded-lg text-xs font-bold bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 transition-colors">
                            <i class="mdi mdi-plus text-sm"></i> Restock
                        </a>
                        @endif
                    </td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>
@endif

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const ctx = document.getElementById('inventoryChart').getContext('2d');

        const labels = @json($months);
        const incomingData = @json($incomingMonthly);
        const outgoingData = @json($outgoingMonthly);

        new Chart(ctx, {
            type: 'line',
            data: {
                labels: labels,
                datasets: [
                    {
                        label: 'Barang Masuk',
                        data: incomingData,
                        borderColor: '#059669',
                        backgroundColor: 'rgba(5, 150, 105, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#059669',
                        pointRadius: 4,
                    },
                    {
                        label: 'Barang Keluar',
                        data: outgoingData,
                        borderColor: '#f43f5e',
                        backgroundColor: 'rgba(244, 63, 94, 0.08)',
                        borderWidth: 2.5,
                        fill: true,
                        tension: 0.4,
                        pointBackgroundColor: '#f43f5e',
                        pointRadius: 4,
                    }
                ]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        mode: 'index',
                        intersect: false,
                        backgroundColor: '#ffffff',
                        titleColor: '#0f172a',
                        bodyColor: '#475569',
                        borderColor: '#e2e8f0',
                        borderWidth: 1,
                        padding: 12,
                        boxShadow: '0 4px 6px -1px rgba(0, 0, 0, 0.1)',
                    }
                },
                scales: {
                    x: {
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 11 } }
                    },
                    y: {
                        grid: { color: '#f1f5f9' },
                        ticks: { color: '#64748b', font: { family: 'Plus Jakarta Sans', size: 11 } },
                        beginAtZero: true,
                    }
                }
            }
        });
    });
</script>
@endpush
