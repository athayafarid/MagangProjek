@extends('layouts.app')

@section('title', 'Detail Barang - ' . $item->name)

@section('content')
<!-- TOP HEADER BAR -->
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div class="flex items-center gap-3">
        <a href="{{ route('items.index') }}" class="p-2 rounded-xl bg-white border border-slate-200 text-slate-600 hover:bg-slate-50 transition-colors" title="Kembali">
            <i class="mdi mdi-arrow-left text-xl"></i>
        </a>
        <div>
            <div class="flex items-center gap-2 flex-wrap">
                <h1 class="text-xl font-bold text-slate-900">{{ $item->name }}</h1>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider
                    {{ $item->stock <= 0 ? 'bg-rose-100 text-rose-800' : ($item->stock < $item->minimum_stock ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800') }}">
                    {{ $item->stock_status }}
                </span>
            </div>
            <p class="text-xs font-mono text-blue-700 mt-0.5">
                Kode: <strong>{{ $item->code }}</strong> | Barcode: <strong>{{ $item->barcode }}</strong>
            </p>
        </div>
    </div>

    <div class="flex items-center gap-2 flex-wrap">
        @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
        <a href="{{ route('inventory.incoming') }}?item_id={{ $item->id }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 text-xs font-semibold transition-colors">
            <i class="mdi mdi-arrow-bottom-left text-base"></i> Barang Masuk
        </a>
        <a href="{{ route('inventory.outgoing') }}?item_id={{ $item->id }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 text-xs font-semibold transition-colors">
            <i class="mdi mdi-arrow-top-right text-base"></i> Barang Keluar
        </a>
        @endif

        @if(Auth::user()->isAdmin())
        <a href="{{ route('barcode.generate', ['item_id' => $item->id]) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-amber-50 text-amber-700 hover:bg-amber-100 border border-amber-200 text-xs font-semibold transition-colors">
            <i class="mdi mdi-printer-outline text-base"></i> Cetak Label
        </a>
        <a href="{{ route('items.edit', $item->id) }}" class="inline-flex items-center gap-1.5 px-3 py-2 rounded-xl bg-blue-50 text-blue-700 hover:bg-blue-100 border border-blue-200 text-xs font-semibold transition-colors">
            <i class="mdi mdi-pencil-outline text-base"></i> Edit
        </a>
        @endif
    </div>
</div>

<!-- MAIN GRID LAYOUT -->
<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
    
    <!-- LEFT COLUMN: MEDIA & BARCODE IDENTIFIERS -->
    <div class="space-y-6">
        
        <!-- CARD 1: FOTO VISUAL BARANG -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                    <i class="mdi mdi-camera-outline text-blue-600 text-base"></i> Foto Visual Barang
                </h2>
            </div>
            <div class="p-4">
                <div class="w-full h-56 rounded-xl bg-slate-50 border border-slate-200 overflow-hidden flex items-center justify-center">
                    @if ($item->image)
                        <img src="{{ asset($item->image) }}" alt="{{ $item->name }}" class="w-full h-full object-cover hover:scale-105 transition-transform duration-300">
                    @else
                        <div class="text-center p-4 text-slate-400">
                            <i class="mdi mdi-image-off-outline text-4xl block mb-1"></i>
                            <p class="text-xs">Foto barang belum diunggah</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- CARD 2: IDENTITAS DIGITAL (QR CODE & BARCODE) -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50">
                <h2 class="text-xs font-bold text-slate-500 uppercase tracking-wider flex items-center gap-2">
                    <i class="mdi mdi-qrcode-scan text-blue-600 text-base"></i> Identitas Digital Barang
                </h2>
            </div>
            <div class="p-5 text-center space-y-4">
                
                <!-- QR CODE SECTION -->
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">
                        <i class="mdi mdi-cellphone-scanner text-blue-600"></i> QR Code (Scan Kamera HP)
                    </span>
                    <div class="p-4 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center min-h-[160px]">
                        <div id="qrcodeCanvas"></div>
                    </div>
                </div>

                <!-- BARCODE 1D SECTION -->
                <div>
                    <span class="text-[11px] font-bold text-slate-500 uppercase tracking-wider block mb-2">
                        <i class="mdi mdi-barcode text-blue-600"></i> Barcode (1D Reader)
                    </span>
                    <div class="p-3 rounded-xl border border-slate-200 bg-white flex items-center justify-center">
                        <svg id="barcodeCanvas" class="w-full max-w-[280px] h-[75px]"></svg>
                    </div>
                </div>

            </div>
        </div>

    </div>

    <!-- RIGHT COLUMN: SPECIFICATIONS & TRANSACTION HISTORY -->
    <div class="lg:col-span-2 space-y-6">

        <!-- CARD 1: INFORMASI DETAIL UTAMA -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="mdi mdi-information-outline text-blue-600 text-lg"></i>
                    Informasi & Spesifikasi Barang
                </h2>
                <span class="px-2.5 py-0.5 rounded-full text-[11px] font-bold capitalize {{ $item->status == 'active' ? 'bg-emerald-100 text-emerald-800' : 'bg-slate-200 text-slate-700' }}">
                    Status: {{ $item->status }}
                </span>
            </div>

            <div class="p-6 space-y-6">
                <!-- STATS HIGHLIGHT CARDS -->
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Stok Fisik Saat Ini</span>
                        <span class="text-2xl font-extrabold text-slate-900 mt-1 block">
                            {{ number_format($item->stock) }} <span class="text-xs font-normal text-slate-500">{{ $item->unit->name }}</span>
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Batas Stok Minimum</span>
                        <span class="text-2xl font-extrabold text-amber-600 mt-1 block">
                            {{ number_format($item->minimum_stock) }} <span class="text-xs font-normal text-slate-500">{{ $item->unit->name }}</span>
                        </span>
                    </div>

                    <div class="p-4 rounded-xl bg-slate-50 border border-slate-200">
                        <span class="text-[11px] font-semibold text-slate-500 uppercase tracking-wider block">Lokasi Rak Warehouse</span>
                        <span class="text-lg font-bold text-slate-900 mt-1 block flex items-center gap-1.5">
                            <i class="mdi mdi-map-marker-outline text-blue-600"></i> {{ $item->location->name }}
                        </span>
                    </div>
                </div>

                <!-- DETAIL FIELD TABLE -->
                <div class="border border-slate-200 rounded-xl overflow-hidden text-xs">
                    <div class="grid grid-cols-3 p-3 bg-slate-50 border-b border-slate-200">
                        <span class="font-semibold text-slate-500">Kategori</span>
                        <span class="col-span-2 font-bold text-slate-900">{{ $item->category->name }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-3 border-b border-slate-100">
                        <span class="font-semibold text-slate-500">Supplier Utama</span>
                        <span class="col-span-2 font-semibold text-slate-800">{{ $item->supplier->name }} ({{ $item->supplier->phone ?? '-' }})</span>
                    </div>
                    <div class="grid grid-cols-3 p-3 bg-slate-50 border-b border-slate-200">
                        <span class="font-semibold text-slate-500">Satuan Kuantitas</span>
                        <span class="col-span-2 font-bold text-slate-900">{{ $item->unit->name }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-3 border-b border-slate-100">
                        <span class="font-semibold text-slate-500">String Barcode</span>
                        <span class="col-span-2 font-mono font-bold text-blue-700">{{ $item->barcode }}</span>
                    </div>
                    <div class="grid grid-cols-3 p-3 bg-slate-50">
                        <span class="font-semibold text-slate-500">Deskripsi / Keterangan</span>
                        <span class="col-span-2 text-slate-700 leading-relaxed">{{ $item->description ?? '-' }}</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- CARD 2: RIWAYAT TRANSAKSI TERAKHIR BARANG INI -->
        <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
            <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
                <h2 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                    <i class="mdi mdi-history text-blue-600 text-lg"></i>
                    Riwayat Mutasi Transaksi Barang
                </h2>
                <a href="{{ route('inventory.history') }}?item_id={{ $item->id }}" class="text-xs font-semibold text-blue-600 hover:underline">Lihat Semua</a>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse text-xs">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                            <th class="py-3 px-4">Tanggal</th>
                            <th class="py-3 px-4">Jenis Mutasi</th>
                            <th class="py-3 px-4">Jumlah</th>
                            <th class="py-3 px-4">Petugas</th>
                            <th class="py-3 px-4">Keterangan</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100 font-medium">
                        @forelse($recentTransactions as $trx)
                        <tr class="hover:bg-slate-50/60">
                            <td class="py-3 px-4 text-slate-600 whitespace-nowrap">{{ $trx->transaction_date->format('d/m/Y H:i') }}</td>
                            <td class="py-3 px-4">
                                <span class="inline-flex items-center px-2 py-0.5 rounded-full text-[10px] font-bold uppercase
                                    {{ $trx->transaction_type == 'IN' ? 'bg-emerald-100 text-emerald-800' : ($trx->transaction_type == 'OUT' ? 'bg-rose-100 text-rose-800' : 'bg-amber-100 text-amber-800') }}">
                                    {{ $trx->transaction_type == 'IN' ? 'Masuk' : ($trx->transaction_type == 'OUT' ? 'Keluar' : 'Penyesuaian') }}
                                </span>
                            </td>
                            <td class="py-3 px-4 font-bold {{ $trx->quantity > 0 ? 'text-emerald-600' : ($trx->quantity < 0 ? 'text-rose-600' : 'text-amber-600') }}">
                                {{ $trx->quantity > 0 ? '+' . $trx->quantity : $trx->quantity }} {{ $item->unit->name }}
                            </td>
                            <td class="py-3 px-4 text-slate-700">{{ $trx->user->name }}</td>
                            <td class="py-3 px-4 text-slate-500 truncate max-w-xs">{{ $trx->description ?? '-' }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="py-8 text-center text-slate-400">
                                <i class="mdi mdi-history text-3xl block mb-1"></i>
                                Belum ada riwayat transaksi mutasi untuk barang ini.
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>

    </div>

</div>

@endsection

@push('scripts')
<script>
    document.addEventListener("DOMContentLoaded", function() {
        const barcodeString = "{{ $item->barcode }}";

        // Generate 1D Barcode
        try {
            JsBarcode("#barcodeCanvas", barcodeString, {
                format: "CODE128",
                displayValue: true,
                fontSize: 12,
                margin: 2,
                height: 45
            });
        } catch (e) {
            console.error("Barcode rendering error:", e);
        }

        // Generate QR Code
        try {
            const qr = qrcode(0, 'M');
            qr.addData(barcodeString);
            qr.make();
            document.getElementById('qrcodeCanvas').innerHTML = qr.createImgTag(5);
        } catch (e) {
            console.error("QR Code rendering error:", e);
        }
    });
</script>
@endpush
