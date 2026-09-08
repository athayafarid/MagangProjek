@extends('layouts.app')

@section('title', 'Laporan Barang Masuk')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-arrow-bottom-left text-emerald-600"></i>
            Laporan Barang Masuk
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Rekapitulasi penerimaan barang dari supplier berdasarkan rentang tanggal</p>
    </div>

    <div class="no-print flex items-center gap-2">
        <a href="{{ route('reports.incoming.export', request()->all()) }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 transition-all">
            <i class="mdi mdi-download text-base"></i> Export Excel/CSV
        </a>
        <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs shadow-2xs transition-colors">
            <i class="mdi mdi-printer text-base"></i> Cetak / PDF
        </button>
    </div>
</div>

<!-- FILTER BAR -->
<div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs mb-6 no-print">
    <form action="{{ route('reports.incoming') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-3 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Mulai:</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
        </div>
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal Selesai:</label>
            <input type="date" name="end_date" value="{{ request('end_date') }}" class="w-full px-3.5 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
        </div>
        <div>
            <button type="submit" class="w-full py-2 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 transition-colors">
                <i class="mdi mdi-filter-outline text-base"></i> Filter Laporan
            </button>
        </div>
    </form>
</div>

<!-- REPORT TABLE -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="p-6 hidden print:block text-black text-center border-b border-slate-200">
        <h2 class="text-lg font-extrabold uppercase tracking-wide">PT BESMINDO MATERI SAWATAMA</h2>
        <p class="text-xs font-bold mt-1">LAPORAN BARANG MASUK WAREHOUSE</p>
        <p class="text-[10px] text-slate-500 mt-1">Dicetak Tanggal: {{ date('d/m/Y H:i') }}</p>
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">No Transaksi</th>
                    <th class="py-3.5 px-4">Tanggal</th>
                    <th class="py-3.5 px-4">Kode & Nama Barang</th>
                    <th class="py-3.5 px-4">Supplier</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Masuk</th>
                    <th class="py-3.5 px-4">Petugas</th>
                    <th class="py-3.5 px-4">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($incomingItems as $index => $row)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $index + 1 }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">{{ $row->transaction_number }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="py-3.5 px-4">
                        <strong class="block text-slate-900 font-bold">{{ $row->item->name }}</strong>
                        <span class="text-slate-400 text-[11px] font-mono block">{{ $row->item->code }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $row->supplier->name }}</td>
                    <td class="py-3.5 px-4 text-center font-bold text-emerald-600 text-sm">+{{ number_format($row->quantity) }} {{ $row->item->unit->name }}</td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $row->user->name }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->description ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">Tidak ada data transaksi barang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>
@endsection
