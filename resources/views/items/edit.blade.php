@extends('layouts.app')

@section('title', 'Edit Barang - ' . $item->name)

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-pencil-outline text-amber-600"></i>
            Edit Data Barang Inventory
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Ubah rincian informasi data barang {{ $item->code }}</p>
    </div>

    <a href="{{ route('items.show', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition-colors">
        <i class="mdi mdi-arrow-left"></i> Kembali ke Detail
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs max-w-4xl mx-auto overflow-hidden">
    <div class="p-6 sm:p-8">
        <form action="{{ route('items.update', $item->id) }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf
            @method('PUT')

            <!-- KODE & BARCODE -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 mb-1.5">Kode Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $item->code) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-blue-700 bg-blue-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                </div>

                <div>
                    <label for="barcode" class="block text-xs font-semibold text-slate-700 mb-1.5">Barcode / QR String <span class="text-rose-500">*</span></label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $item->barcode) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-emerald-700 bg-emerald-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                </div>
            </div>

            <!-- NAMA BARANG & KATEGORI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name', $item->name) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id', $item->category_id) == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi Barang</label>
                <textarea name="description" id="description" rows="3"
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">{{ old('description', $item->description) }}</textarea>
            </div>

            <!-- STOK MINIMUM & SATUAN -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label class="block text-xs font-semibold text-slate-500 mb-1.5">Stok Saat Ini (Readonly)</label>
                    <input type="text" disabled value="{{ $item->stock }} {{ $item->unit->name }}"
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-200 text-sm font-bold bg-slate-100 text-slate-700 cursor-not-allowed">
                </div>

                <div>
                    <label for="minimum_stock" class="block text-xs font-semibold text-slate-700 mb-1.5">Stok Minimum <span class="text-rose-500">*</span></label>
                    <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', $item->minimum_stock) }}" min="0" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <div>
                    <label for="unit_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Satuan Barang <span class="text-rose-500">*</span></label>
                    <select name="unit_id" id="unit_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id', $item->unit_id) == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- LOKASI RAK & SUPPLIER -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="location_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Lokasi / Rak Penyimpanan <span class="text-rose-500">*</span></label>
                    <select name="location_id" id="location_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id', $item->location_id) == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="supplier_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Supplier Utama / Pemasok <span class="text-rose-500">*</span></label>
                    <select name="supplier_id" id="supplier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id', $item->supplier_id) == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- UPLOAD GAMBAR BARANG & STATUS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                <div>
                    <label for="image" class="block text-xs font-semibold text-slate-700 mb-1.5">Ganti Gambar Barang</label>
                    @if ($item->image)
                    <div class="flex items-center gap-3 mb-2">
                        <img src="{{ asset($item->image) }}" class="w-12 h-12 rounded-xl object-cover border border-slate-200">
                        <span class="text-xs text-slate-400">Gambar saat ini</span>
                    </div>
                    @endif
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Status Barang <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="active" {{ old('status', $item->status) == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status', $item->status) == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('items.show', $item->id) }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition-all">
                    <i class="mdi mdi-check text-base"></i> Simpan Perubahan
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
