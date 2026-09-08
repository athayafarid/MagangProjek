@extends('layouts.app')

@section('title', 'Cetak Barcode & QR Code Label')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-printer-outline text-amber-600"></i>
            Cetak Barcode & QR Code Label Barang
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Pilih barang dan cetak label barcode / QR Code untuk ditempelkan pada fisik barang di rak warehouse</p>
    </div>

    <button onclick="window.print()" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-md shadow-amber-600/20 transition-all no-print">
        <i class="mdi mdi-printer text-base"></i> Cetak Label (Print)
    </button>
</div>

<!-- ITEM SELECTOR FORM -->
<div class="bg-white rounded-2xl p-4 border border-slate-200/80 shadow-xs mb-6 no-print">
    <form action="{{ route('barcode.generate') }}" method="GET">
        <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Pilih Barang untuk Dicetak Labelnya:</label>
            <select name="item_id" onchange="this.form.submit()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                <option value="">-- Pilih Barang --</option>
                @foreach($items as $item)
                    <option value="{{ $item->id }}" {{ optional($selectedItem)->id == $item->id ? 'selected' : '' }}>
                        {{ $item->code }} - {{ $item->name }} (Lokasi: {{ $item->location->name }})
                    </option>
                @endforeach
            </select>
        </div>
    </form>
</div>

<!-- LABEL PRINT PREVIEW AREA -->
@if($selectedItem)
<div class="bg-white rounded-2xl p-6 sm:p-8 border border-slate-200/80 shadow-xs text-center">
    <h2 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-6 no-print">Pratinjau Cetak Label Barang</h2>

    <!-- PHYSICAL LABEL BOX DESIGN -->
    <div class="mx-auto w-80 bg-white text-slate-900 p-6 rounded-2xl border-2 border-slate-900 shadow-md">
        <div class="text-center border-b border-slate-900 pb-2 mb-3">
            <strong class="text-xs uppercase tracking-wider block font-bold">PT BESMINDO MATERI SAWATAMA</strong>
            <span class="text-[9px] font-bold text-slate-600 block">WAREHOUSE INVENTORY LABEL</span>
        </div>

        <div class="flex justify-center mb-3">
            <div id="selectedQrCanvas"></div>
        </div>

        <div class="bg-slate-50 p-2 rounded-lg border border-slate-200 mb-3">
            <svg id="selectedBarcodeCanvas" class="w-full"></svg>
        </div>

        <div class="text-center">
            <p class="font-mono font-bold text-blue-700 text-sm mb-1">{{ $selectedItem->code }}</p>
            <h4 class="text-sm font-bold text-slate-900 mb-2 leading-tight">{{ $selectedItem->name }}</h4>
            <div class="flex items-center justify-between text-[11px] font-bold pt-2 border-t border-slate-200">
                <span>Kat: {{ $selectedItem->category->name }}</span>
                <span class="bg-slate-900 text-white px-2 py-0.5 rounded">Lokasi: {{ $selectedItem->location->name }}</span>
            </div>
        </div>
    </div>

    <div class="no-print mt-6">
        <button onclick="window.print()" class="px-6 py-2.5 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-md shadow-amber-600/20 inline-flex items-center gap-2 transition-all">
            <i class="mdi mdi-printer text-base"></i> Cetak Label Sekarang
        </button>
    </div>
</div>
@else
<div class="bg-white rounded-2xl p-12 border border-slate-200/80 shadow-xs text-center no-print">
    <i class="mdi mdi-qrcode-scan text-slate-300 text-5xl block mb-2"></i>
    <p class="text-slate-500 font-semibold text-xs">Silakan pilih barang di atas untuk membuat dan mencetak label barcode/QR Code.</p>
</div>
@endif
@endsection

@push('scripts')
@if($selectedItem)
<script>
    document.addEventListener("DOMContentLoaded", function () {
        // Render Barcode
        JsBarcode("#selectedBarcodeCanvas", "{{ $selectedItem->barcode }}", {
            format: "CODE128",
            lineColor: "#000000",
            background: "transparent",
            width: 2,
            height: 45,
            displayValue: true,
            fontSize: 12
        });

        // Render QR Code
        const qr = qrcode(0, 'M');
        qr.addData("{{ route('barcode.scan', ['code' => $selectedItem->code]) }}");
        qr.make();
        document.getElementById('selectedQrCanvas').innerHTML = qr.createImgTag(5, 10);
    });
</script>
@endif
@endpush
