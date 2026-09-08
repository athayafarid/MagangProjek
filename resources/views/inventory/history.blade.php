@extends('layouts.app')

@section('title', 'Riwayat Transaksi Inventory')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-history text-blue-600"></i>
            Riwayat Log Transaksi Inventory
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Jejak audit lengkap (ledger log) seluruh perubahan stok masuk, keluar, dan penyesuaian</p>
    </div>
</div>

<!-- FILTER BAR -->
<div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs mb-6">
    <form action="{{ route('inventory.history') }}" method="GET" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Cari Barang</label>
            <input type="text" name="search" value="{{ request('search') }}"
                   placeholder="Cari kode atau nama barang..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Jenis Transaksi</label>
            <select name="transaction_type" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">-- Semua Jenis --</option>
                <option value="IN" {{ request('transaction_type') == 'IN' ? 'selected' : '' }}>Barang Masuk (IN)</option>
                <option value="OUT" {{ request('transaction_type') == 'OUT' ? 'selected' : '' }}>Barang Keluar (OUT)</option>
                <option value="ADJUSTMENT" {{ request('transaction_type') == 'ADJUSTMENT' ? 'selected' : '' }}>Penyesuaian (ADJUSTMENT)</option>
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Tanggal</label>
            <input type="date" name="start_date" value="{{ request('start_date') }}" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="flex-1 py-2.5 px-4 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 transition-colors">
                <i class="mdi mdi-filter-outline text-base"></i> Filter
            </button>
            @if(request()->hasAny(['search', 'transaction_type', 'start_date']))
            <a href="{{ route('inventory.history') }}" class="py-2.5 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors" title="Reset">
                <i class="mdi mdi-refresh text-base"></i>
            </a>
            @endif
        </div>
    </form>
</div>

<!-- TABLE TRANSACTIONS -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">Tanggal & Waktu</th>
                    <th class="py-3.5 px-4">Ref / No Transaksi</th>
                    <th class="py-3.5 px-4">Barang Inventory</th>
                    <th class="py-3.5 px-4 text-center">Jenis Transaksi</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Qty</th>
                    <th class="py-3.5 px-4">Stok Sebelum &rarr; Sesudah</th>
                    <th class="py-3.5 px-4">Petugas Executer</th>
                    <th class="py-3.5 px-4">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($transactions as $index => $trx)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $transactions->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 text-slate-500 whitespace-nowrap">{{ $trx->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">{{ $trx->reference_number ?? '-' }}</td>
                    <td class="py-3.5 px-4">
                        <strong class="block text-slate-900 font-bold">{{ $trx->item->name }}</strong>
                        <span class="text-slate-400 text-[11px] font-mono block">{{ $trx->item->code }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        @if($trx->transaction_type == 'IN')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800">MASUK</span>
                        @elseif($trx->transaction_type == 'OUT')
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-rose-100 text-rose-800">KELUAR</span>
                        @else
                            <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-amber-100 text-amber-800">ADJUSTMENT</span>
                        @endif
                    </td>
                    <td class="py-3.5 px-4 text-center font-bold {{ $trx->quantity > 0 ? 'text-emerald-600' : ($trx->quantity < 0 ? 'text-rose-600' : 'text-slate-400') }}">
                        {{ $trx->quantity > 0 ? '+' . $trx->quantity : $trx->quantity }} {{ $trx->item->unit->name }}
                    </td>
                    <td class="py-3.5 px-4 font-mono text-slate-500">
                        {{ $trx->stock_before }} &rarr; <strong class="text-slate-900 font-bold">{{ $trx->stock_after }}</strong>
                    </td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $trx->user->name }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $trx->description ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-12 text-center text-slate-400">Belum ada data riwayat transaksi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($transactions->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $transactions->links() }}
    </div>
    @endif
</div>
@endsection
