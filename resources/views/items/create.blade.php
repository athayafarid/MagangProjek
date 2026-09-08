@extends('layouts.app')

@section('title', 'Tambah Barang Baru')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-plus-circle-outline text-blue-600"></i>
            Tambah Barang Inventory Baru
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Isi formulir lengkap untuk meregistrasikan item barang baru ke dalam database warehouse</p>
    </div>
    
    <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition-colors">
        <i class="mdi mdi-arrow-left"></i> Kembali
    </a>
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs max-w-4xl mx-auto overflow-hidden">
    <div class="p-6 sm:p-8">
        <form action="{{ route('items.store') }}" method="POST" enctype="multipart/form-data" class="space-y-6">
            @csrf

            <!-- KODE & BARCODE -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="code" class="block text-xs font-semibold text-slate-700 mb-1.5">Kode Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="code" id="code" value="{{ old('code', $autoCode) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-blue-700 bg-blue-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    <p class="text-[11px] text-slate-400 mt-1">Kode unik identifikasi inventory (cth: BRG-00001)</p>
                </div>

                <div>
                    <label for="barcode" class="block text-xs font-semibold text-slate-700 mb-1.5">Barcode / QR Code String <span class="text-rose-500">*</span></label>
                    <input type="text" name="barcode" id="barcode" value="{{ old('barcode', $autoCode) }}" required
                           class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-mono font-bold text-emerald-700 bg-emerald-50/50 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600">
                    <p class="text-[11px] text-slate-400 mt-1">String yang di-encode ke barcode/QR Code</p>
                </div>
            </div>

            <!-- NAMA BARANG & KATEGORI -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="name" class="block text-xs font-semibold text-slate-700 mb-1.5">Nama Barang <span class="text-rose-500">*</span></label>
                    <input type="text" name="name" id="name" value="{{ old('name') }}" required
                           placeholder="Contoh: Ball Bearing 6205-2RS" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <div>
                    <label for="category_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Kategori <span class="text-rose-500">*</span></label>
                    <select name="category_id" id="category_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Kategori --</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->id }}" {{ old('category_id') == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- DESKRIPSI -->
            <div>
                <label for="description" class="block text-xs font-semibold text-slate-700 mb-1.5">Deskripsi Barang</label>
                <textarea name="description" id="description" rows="3"
                          placeholder="Spesifikasi teknis atau keterangan barang..."
                          class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">{{ old('description') }}</textarea>
            </div>

            <!-- STOK AWAL, MINIMUM STOK, SATUAN -->
            <div class="grid grid-cols-1 sm:grid-cols-3 gap-6">
                <div>
                    <label for="stock" class="block text-xs font-semibold text-slate-700 mb-1.5">Stok Awal <span class="text-rose-500">*</span></label>
                    <input type="number" name="stock" id="stock" value="{{ old('stock', 0) }}" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <div>
                    <label for="minimum_stock" class="block text-xs font-semibold text-slate-700 mb-1.5">Stok Minimum <span class="text-rose-500">*</span></label>
                    <input type="number" name="minimum_stock" id="minimum_stock" value="{{ old('minimum_stock', 5) }}" min="0" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <div>
                    <label for="unit_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Satuan Barang <span class="text-rose-500">*</span></label>
                    <select name="unit_id" id="unit_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Satuan --</option>
                        @foreach($units as $unit)
                            <option value="{{ $unit->id }}" {{ old('unit_id') == $unit->id ? 'selected' : '' }}>{{ $unit->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- LOKASI RAK & SUPPLIER -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                <div>
                    <label for="location_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Lokasi / Rak Penyimpanan <span class="text-rose-500">*</span></label>
                    <select name="location_id" id="location_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Lokasi Rak --</option>
                        @foreach($locations as $loc)
                            <option value="{{ $loc->id }}" {{ old('location_id') == $loc->id ? 'selected' : '' }}>{{ $loc->name }}</option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label for="supplier_id" class="block text-xs font-semibold text-slate-700 mb-1.5">Supplier Utama / Pemasok <span class="text-rose-500">*</span></label>
                    <select name="supplier_id" id="supplier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Supplier --</option>
                        @foreach($suppliers as $sup)
                            <option value="{{ $sup->id }}" {{ old('supplier_id') == $sup->id ? 'selected' : '' }}>{{ $sup->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <!-- UPLOAD GAMBAR BARANG & STATUS -->
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 pt-6 border-t border-slate-100">
                <div>
                    <label for="image" class="block text-xs font-semibold text-slate-700 mb-1.5">Gambar Barang (Foto Visual)</label>
                    <input type="file" name="image" id="image" accept="image/jpeg,image/png,image/webp"
                           class="w-full text-xs text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100 cursor-pointer">
                    <p class="text-[11px] text-slate-400 mt-1">Format: JPG, PNG, WEBP. Max 2MB.</p>
                </div>

                <div>
                    <label for="status" class="block text-xs font-semibold text-slate-700 mb-1.5">Status Barang <span class="text-rose-500">*</span></label>
                    <select name="status" id="status" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="active" {{ old('status') == 'active' ? 'selected' : '' }}>Aktif</option>
                        <option value="inactive" {{ old('status') == 'inactive' ? 'selected' : '' }}>Non-Aktif</option>
                    </select>
                </div>
            </div>

            <!-- SUBMIT BUTTONS -->
            <div class="flex items-center justify-end gap-3 pt-6 border-t border-slate-100">
                <a href="{{ route('items.index') }}" class="px-4 py-2.5 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs transition-colors">
                    Batal
                </a>
                <button type="submit" class="px-5 py-2.5 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 flex items-center gap-1.5 transition-all">
                    <i class="mdi mdi-check text-base"></i> Simpan Barang
                </button>
            </div>
        </form>
    </div>
</div>
@endsection
