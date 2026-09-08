@extends('layouts.app')

@section('title', 'Stok Barang Realtime')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-layers-outline text-blue-600"></i>
            Stok Barang Realtime
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Pengecekan jumlah stok barang secara otomatis berdasarkan transaksi mutasi fisik</p>
    </div>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">Kode Barang</th>
                    <th class="py-3.5 px-4">Nama Barang</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4">Jumlah Stok Saat Ini</th>
                    <th class="py-3.5 px-4">Batas Minimum</th>
                    <th class="py-3.5 px-4">Lokasi Rak</th>
                    <th class="py-3.5 px-4">Status Kondisi</th>
                    <th class="py-3.5 px-4 text-center">Aksi Operasional</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse ($items as $index => $item)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $items->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">{{ $item->code }}</td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ $item->name }}</td>
                    <td class="py-3.5 px-4 text-slate-600">{{ $item->category->name }}</td>
                    <td class="py-3.5 px-4">
                        <span class="font-extrabold text-sm text-slate-900">{{ number_format($item->stock) }}</span>
                        <span class="text-slate-400 text-xs ml-0.5">{{ $item->unit->name }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-500">{{ number_format($item->minimum_stock) }} {{ $item->unit->name }}</td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-md bg-amber-50 text-amber-800 text-[11px] font-semibold border border-amber-200">
                            {{ $item->location->name }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $item->stock_status == 'Aman' ? 'bg-emerald-100 text-emerald-800' : ($item->stock_status == 'Perhatian' ? 'bg-amber-100 text-amber-800' : 'bg-rose-100 text-rose-800') }}">
                            {{ $item->stock_status }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                        <div class="flex items-center justify-center gap-1.5">
                            <a href="{{ route('inventory.incoming') }}?item_id={{ $item->id }}" class="px-2.5 py-1 rounded-lg bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-bold transition-colors">
                                + Masuk
                            </a>
                            <a href="{{ route('inventory.outgoing') }}?item_id={{ $item->id }}" class="px-2.5 py-1 rounded-lg bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-bold transition-colors">
                                - Keluar
                            </a>
                        </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-12 text-center text-slate-400">Belum ada barang terdaftar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($items->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $items->links() }}
    </div>
    @endif
</div>
@endsection
