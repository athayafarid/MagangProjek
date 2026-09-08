@extends('layouts.app')

@section('title', 'Laporan Stok Inventory')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-file-document-outline text-blue-600"></i>
            Laporan Stok Inventory Warehouse
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi posisi stok barang, lokasi penyimpanan, dan status kondisi stok</p>
    </div>

    <div class="no-print flex items-center gap-2">
        <a href="{{ route('reports.stock.export', request()->all()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 transition-all">
            <i class="mdi mdi-download text-base"></i> Export Excel/CSV
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs shadow-2xs transition-colors">
            <i class="mdi mdi-printer text-base"></i> Cetak / PDF
        </button>
    </div>
</div>

<!-- FILTER BAR -->
<div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs mb-6 no-print">
    <form action="{{ route('reports.stock') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori</label>
            <select name="category_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">-- Semua Kategori --</option>
                @foreach($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Lokasi Rak</label>
            <select name="location_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">-- Semua Lokasi Rak --</option>
                @foreach($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Stok</label>
            <select name="status_stock" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">-- Semua Status Stok --</option>
                <option value="aman" {{ request('status_stock') == 'aman' ? 'selected' : '' }}>Stok Aman</option>
                <option value="perhatian" {{ request('status_stock') == 'perhatian' ? 'selected' : '' }}>Stok Perhatian</option>
                <option value="rendah" {{ request('status_stock') == 'rendah' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="habis" {{ request('status_stock') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
            </select>
        </div>
    </form>
</div>

<!-- REPORT TABLE -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="p-6 hidden print:block text-black text-center border-b border-slate-200">
        <h2 class="text-lg font-extrabold uppercase tracking-wide">PT BESMINDO MATERI SAWATAMA</h2>
        <p class="text-xs font-bold mt-1">LAPORAN STOK INVENTORY WAREHOUSE</p>
        <p class="text-[10px] text-slate-500 mt-1">Dicetak Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">Kode Barang</th>
                    <th class="py-3.5 px-4">Nama Barang</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Stok</th>
                    <th class="py-3.5 px-4 text-center">Stok Min</th>
                    <th class="py-3.5 px-4">Satuan</th>
                    <th class="py-3.5 px-4">Lokasi Rak</th>
                    <th class="py-3.5 px-4">Supplier Utama</th>
                    <th class="py-3.5 px-4">Status Kondisi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($items as $index => $item)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $index + 1 }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">{{ $item->code }}</td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->name }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $item->category->name }}</td>
                    <td class="py-3.5 px-4 text-center font-bold text-slate-900">{{ number_format($item->stock) }}</td>
                    <td class="py-3.5 px-4 text-center text-slate-500">{{ number_format($item->minimum_stock) }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $item->unit->name }}</td>
                    <td class="py-3.5 px-4 font-mono text-amber-700 font-bold">{{ $item->location->name }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $item->supplier->name }}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $item->stock <= 0 ? 'bg-rose-100 text-rose-800' : ($item->stock < $item->minimum_stock ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                            {{ $item->stock_status }}
                        </span>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="10" class="py-12 text-center text-slate-400">Tidak ada data laporan stok sesuai filter.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
