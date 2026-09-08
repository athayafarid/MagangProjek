@extends('layouts.app')

@section('title', 'Master Data Barang Inventory')

@section('content')

<!-- TOP SUMMARY STATS -->
<div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 mb-6">
    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total SKU Barang</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($totalItemsCount) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Produk terdaftar</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-package-variant-closed"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Fisik Stok</span>
            <span class="text-2xl font-extrabold text-slate-900 mt-1 block">{{ number_format($totalPhysicalStock) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Akumulasi kuantitas</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 border border-emerald-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-layers-outline"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Stok Rendah / Habis</span>
            <span class="text-2xl font-extrabold text-rose-600 mt-1 block">{{ number_format($lowStockCount) }}</span>
            <span class="text-[11px] font-medium text-rose-500 mt-0.5 block">Butuh perhatian</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-rose-50 text-rose-600 border border-rose-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-alert-circle-outline"></i>
        </div>
    </div>

    <div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs flex items-center justify-between hover:shadow-md transition-shadow">
        <div>
            <span class="text-xs font-semibold text-slate-500 uppercase tracking-wider block">Total Barang Aktif</span>
            <span class="text-2xl font-extrabold text-purple-600 mt-1 block">{{ number_format($totalActiveItems) }}</span>
            <span class="text-[11px] font-medium text-slate-400 mt-0.5 block">Status siap dipakai</span>
        </div>
        <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 border border-purple-100 flex items-center justify-center text-xl shrink-0">
            <i class="mdi mdi-check-decagram-outline"></i>
        </div>
    </div>
</div>

<!-- FILTER & SEARCH CARD -->
<div class="bg-white rounded-2xl p-5 border border-slate-200/80 shadow-xs mb-6">
    <form method="GET" action="{{ route('items.index') }}" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-5 gap-4 items-end">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Cari Kode / Nama</label>
            <div class="relative">
                <i class="mdi mdi-magnify absolute left-3.5 top-1/2 -translate-y-1/2 text-slate-400 text-base"></i>
                <input type="text" name="search" value="{{ request('search') }}" placeholder="Cari nama, SKU, barcode..."
                    class="w-full pl-9 pr-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
            </div>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Filter Kategori</label>
            <select name="category_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">Semua Kategori</option>
                @foreach ($categories as $cat)
                    <option value="{{ $cat->id }}" {{ request('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Filter Lokasi / Rak</label>
            <select name="location_id" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">Semua Lokasi</option>
                @foreach ($locations as $loc)
                    <option value="{{ $loc->id }}" {{ request('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                @endforeach
            </select>
        </div>

        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Status Stok</label>
            <select name="stock_status" class="w-full px-3 py-2 rounded-xl border border-slate-300 text-xs focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">Semua Status</option>
                <option value="habis" {{ request('stock_status') == 'habis' ? 'selected' : '' }}>Stok Habis</option>
                <option value="rendah" {{ request('stock_status') == 'rendah' ? 'selected' : '' }}>Stok Rendah</option>
                <option value="normal" {{ request('stock_status') == 'normal' ? 'selected' : '' }}>Stok Normal</option>
            </select>
        </div>

        <div class="flex items-center gap-2">
            <button type="submit" class="flex-1 py-2 px-3 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-sm flex items-center justify-center gap-1.5 transition-colors">
                <i class="mdi mdi-filter-outline text-base"></i> Filter
            </button>
            <a href="{{ route('items.index') }}" class="py-2 px-3 rounded-xl bg-slate-100 hover:bg-slate-200 text-slate-700 font-semibold text-xs transition-colors" title="Reset Filter">
                <i class="mdi mdi-refresh text-base"></i>
            </a>
        </div>
    </form>
</div>

<!-- MAIN DATA TABLE CARD -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="p-4 sm:px-6 sm:py-4 border-b border-slate-100 flex flex-col sm:flex-row sm:items-center justify-between gap-4 bg-slate-50/50">
        <div>
            <h2 class="text-base font-bold text-slate-900">Daftar Barang Inventory</h2>
            <p class="text-xs text-slate-500 mt-0.5">Kelola seluruh master data katalog barang dan kuantitas stok</p>
        </div>
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('items.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 transition-all hover:shadow-lg">
            <i class="mdi mdi-plus text-base"></i>
            <span>Tambah Barang Baru</span>
        </a>
        @endif
    </div>

    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">Gambar</th>
                    <th class="py-3.5 px-4">Kode & Barcode</th>
                    <th class="py-3.5 px-4">Nama Barang</th>
                    <th class="py-3.5 px-4">Kategori</th>
                    <th class="py-3.5 px-4">Stok Saat Ini</th>
                    <th class="py-3.5 px-4">Stok Minimum</th>
                    <th class="py-3.5 px-4">Lokasi</th>
                    <th class="py-3.5 px-4">Status</th>
                    <th class="py-3.5 px-4 text-right">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse ($items as $item)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3 px-4 text-center">
                        <div class="w-11 h-11 rounded-lg border border-slate-200 bg-slate-50 overflow-hidden flex items-center justify-center mx-auto shadow-2xs">
                            @if($item->image)
                                <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover">
                            @else
                                <i class="mdi mdi-package-variant text-slate-400 text-xl"></i>
                            @endif
                        </div>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-mono font-bold text-blue-700 bg-blue-50 px-2 py-0.5 rounded border border-blue-200/60 block w-fit mb-1">{{ $item->code }}</span>
                        <span class="text-[10px] font-mono text-slate-400 block">{{ $item->barcode }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <a href="{{ route('items.show', $item->id) }}" class="font-semibold text-slate-900 hover:text-blue-600 transition-colors block">
                            {{ $item->name }}
                        </a>
                        @if($item->description)
                            <span class="text-[11px] text-slate-400 truncate max-w-xs block mt-0.5">{{ Str::limit($item->description, 45) }}</span>
                        @endif
                    </td>
                    <td class="py-3 px-4 text-slate-600">
                        <span class="px-2 py-0.5 rounded-md bg-slate-100 text-slate-700 text-[11px] font-medium border border-slate-200">{{ $item->category->name }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="font-extrabold text-sm {{ $item->stock <= 0 ? 'text-rose-600' : ($item->stock < $item->minimum_stock ? 'text-amber-600' : 'text-slate-900') }}">
                            {{ number_format($item->stock) }}
                        </span>
                        <span class="text-[11px] text-slate-500 font-normal"> {{ $item->unit->name }}</span>
                    </td>
                    <td class="py-3 px-4 text-slate-500">
                        {{ number_format($item->minimum_stock) }} {{ $item->unit->name }}
                    </td>
                    <td class="py-3 px-4 text-slate-600">
                        <span class="inline-flex items-center gap-1 text-[11px]"><i class="mdi mdi-map-marker-outline text-slate-400"></i> {{ $item->location->name }}</span>
                    </td>
                    <td class="py-3 px-4">
                        <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                            {{ $item->stock <= 0 ? 'bg-rose-100 text-rose-800' : ($item->stock < $item->minimum_stock ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                            {{ $item->stock_status }}
                        </span>
                    </td>
                    <td class="py-3 px-4 text-right">
                        <div class="flex items-center justify-end gap-1">
                            <a href="{{ route('items.show', $item->id) }}" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Detail">
                                <i class="mdi mdi-eye-outline text-base"></i>
                            </a>
                            @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                            <a href="{{ route('items.edit', $item->id) }}" class="p-1.5 text-slate-500 hover:text-amber-600 hover:bg-amber-50 rounded-lg transition-colors" title="Edit">
                                <i class="mdi mdi-pencil-outline text-base"></i>
                            </a>
                            @endif
                            @if(Auth::user()->isAdmin())
                            <form action="{{ route('items.destroy', $item->id) }}" method="POST" class="inline" onsubmit="return confirm('Apakah Anda yakin ingin menghapus barang ini?')">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <i class="mdi mdi-trash-can-outline text-base"></i>
                                </button>
                            </form>
                            @endif
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9" class="py-12 text-center text-slate-400">
                        <i class="mdi mdi-package-variant-closed text-4xl block mb-2"></i>
                        <p class="text-xs font-semibold">Tidak ada data barang yang ditemukan.</p>
                    </td>
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
