@extends('layouts.app')

@section('title', 'Scan Barcode / QR Code')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-qrcode-scan text-blue-600"></i>
            Scan Barcode / QR Code Barang
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Arahkan kamera ke barcode/QR Code barang untuk menampilkan detail stok dan lokasi penyimpanan secara otomatis</p>
    </div>

    <a href="{{ route('items.index') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 text-xs font-semibold shadow-2xs transition-colors">
        <i class="mdi mdi-package-variant-closed text-base"></i> Data Barang
    </a>
</div>

<div class="grid grid-cols-1 lg:grid-cols-3 gap-6">

    <!-- LEFT COLUMN: CAMERA VIEWPORT & MANUAL INPUT -->
    <div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
        <div class="px-5 py-3.5 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
            <span class="font-bold text-slate-800 text-xs flex items-center gap-1.5">
                <i class="mdi mdi-camera text-emerald-600 text-base"></i> Scanner Kamera Browser
            </span>
            <span class="px-2 py-0.5 text-[10px] font-bold uppercase rounded-full bg-emerald-100 text-emerald-800">KAMERA AKTIF</span>
        </div>
        <div class="p-5">
            <!-- CAMERA VIEWPORT CONTAINER -->
            <div class="rounded-xl overflow-hidden bg-slate-950 border border-slate-800 mb-4 min-h-[260px]">
                <div id="reader" class="w-full"></div>
            </div>

            <!-- MANUAL CODE FALLBACK INPUT -->
            <div class="pt-4 border-t border-slate-100">
                <label class="block text-xs font-semibold text-slate-700 mb-1.5">Ketik Kode Barang / Barcode Manual:</label>
                <div class="flex gap-2">
                    <input type="text" id="manualCodeInput" placeholder="Contoh: BRG-00001" class="flex-1 px-3.5 py-2 rounded-xl border border-slate-300 text-xs font-mono focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                    <button onclick="processScan(document.getElementById('manualCodeInput').value)" class="px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-sm transition-colors flex items-center gap-1">
                        <i class="mdi mdi-magnify text-base"></i> Cari
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- RIGHT COLUMN: SCAN RESULT DISPLAY -->
    <div class="lg:col-span-2">
        <!-- INITIAL STATE / WAITING FOR SCAN -->
        <div id="scanIdleState" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-12 text-center h-full flex flex-col items-center justify-center">
            <div class="w-16 h-16 rounded-2xl bg-blue-50 text-blue-600 flex items-center justify-center text-3xl mb-4">
                <i class="mdi mdi-barcode-scan"></i>
            </div>
            <h3 class="font-bold text-slate-900 text-base mb-1">Siap Memindai</h3>
            <p class="text-xs text-slate-500 max-w-xs">Posisikan kode barcode/QR di depan kamera. Informasi barang akan langsung ditampilkan di sini.</p>
        </div>

        <!-- RESULT: ITEM FOUND CARD -->
        <div id="scanSuccessState" class="bg-white rounded-2xl border-l-4 border-l-emerald-500 border-t border-r border-b border-slate-200/80 shadow-xs overflow-hidden hidden">
            <div class="px-5 py-3 border-b border-slate-100 bg-slate-50/50 flex items-center justify-between">
                <span class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider bg-emerald-100 text-emerald-800 flex items-center gap-1">
                    <i class="mdi mdi-check-circle-outline"></i> Barang Ditemukan
                </span>
                <span id="resBadgeStatus" class="px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider"></span>
            </div>

            <div class="p-6">
                <div class="flex gap-4 mb-6">
                    <div class="w-20 h-20 rounded-xl border border-slate-200 overflow-hidden shrink-0 bg-slate-50">
                        <img id="resImg" src="" alt="Foto Barang" class="w-full h-full object-cover">
                    </div>
                    <div class="flex-1 min-w-0">
                        <span id="resCode" class="font-mono text-blue-700 font-bold text-xs block mb-0.5"></span>
                        <h2 id="resName" class="text-base font-bold text-slate-900 mb-1"></h2>
                        <p id="resCategory" class="text-xs text-slate-500"></p>
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4 mb-6">
                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block mb-0.5">Stok Tersedia</span>
                        <span id="resStock" class="text-lg font-bold text-slate-900"></span>
                    </div>

                    <div class="bg-slate-50 border border-slate-200 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block mb-0.5">Lokasi Penyimpanan</span>
                        <span id="resLocation" class="text-sm font-bold text-amber-700 flex items-center gap-1 mt-0.5">
                            <i class="mdi mdi-map-marker-outline"></i> <span id="resLocName"></span>
                        </span>
                    </div>

                    <div class="sm:col-span-2 bg-slate-50 border border-slate-200 rounded-xl p-3">
                        <span class="text-slate-500 text-[10px] uppercase font-bold tracking-wider block mb-0.5">Supplier Utama</span>
                        <span id="resSupplier" class="font-semibold text-slate-800 text-xs"></span>
                    </div>
                </div>

                <!-- QUICK ACTION BUTTONS -->
                <div class="grid grid-cols-3 gap-3 pt-4 border-t border-slate-100">
                    <a id="btnResDetail" href="#" class="py-2 px-3 rounded-xl bg-white border border-slate-200 text-slate-700 hover:bg-slate-50 font-semibold text-xs text-center flex items-center justify-center gap-1 transition-colors">
                        <i class="mdi mdi-eye-outline text-sm"></i> Detail
                    </a>
                    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
                    <a id="btnResIncoming" href="#" class="py-2 px-3 rounded-xl bg-emerald-50 text-emerald-700 hover:bg-emerald-100 border border-emerald-200 font-semibold text-xs text-center flex items-center justify-center gap-1 transition-colors">
                        <i class="mdi mdi-plus text-sm"></i> Masuk
                    </a>
                    <a id="btnResOutgoing" href="#" class="py-2 px-3 rounded-xl bg-rose-50 text-rose-700 hover:bg-rose-100 border border-rose-200 font-semibold text-xs text-center flex items-center justify-center gap-1 transition-colors">
                        <i class="mdi mdi-minus text-sm"></i> Keluar
                    </a>
                    @endif
                </div>
            </div>
        </div>

        <!-- RESULT: ITEM NOT FOUND CARD -->
        <div id="scanNotFoundState" class="bg-white rounded-2xl border border-slate-200/80 shadow-xs p-8 text-center hidden">
            <div class="w-14 h-14 rounded-2xl bg-rose-50 text-rose-600 flex items-center justify-center text-2xl mx-auto mb-4">
                <i class="mdi mdi-alert-outline"></i>
            </div>
            <h3 class="text-rose-600 font-bold text-sm mb-1">Barang Tidak Ditemukan</h3>
            <p class="text-slate-500 text-xs mb-4">Kode barcode <strong id="nfCode" class="font-mono text-slate-900"></strong> tidak terdaftar dalam database sistem.</p>

            @if(Auth::user()->isAdmin())
            <div class="pt-4 border-t border-slate-100">
                <a href="{{ route('items.create') }}" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-600/20 transition-all">
                    <i class="mdi mdi-plus text-base"></i> Tambah Barang Baru
                </a>
            </div>
            @endif
        </div>
    </div>

</div>
@endsection

@push('scripts')
<script>
    let html5QrcodeScanner = null;
    let isProcessing = false;

    function processScan(code) {
        if (!code || isProcessing) return;
        isProcessing = true;

        fetch(`/barcode/api-lookup?code=${encodeURIComponent(code)}`)
            .then(res => res.json())
            .then(data => {
                document.getElementById('scanIdleState').style.display = 'none';
                if (data.status === 'success') {
                    const item = data.data;
                    document.getElementById('scanNotFoundState').style.display = 'none';
                    document.getElementById('scanSuccessState').style.display = 'block';

                    document.getElementById('resCode').innerText = item.code + ' | Barcode: ' + item.barcode;
                    document.getElementById('resName').innerText = item.name;
                    document.getElementById('resCategory').innerText = 'Kategori: ' + item.category_name;
                    document.getElementById('resStock').innerText = item.stock + ' ' + item.unit_name;
                    document.getElementById('resLocName').innerText = item.location_name;
                    document.getElementById('resSupplier').innerText = item.supplier_name;
                    document.getElementById('resImg').src = item.image_url;

                    const statusBadge = document.getElementById('resBadgeStatus');
                    statusBadge.innerText = item.stock_status;
                    statusBadge.className = 'px-2.5 py-0.5 rounded-full text-[10px] font-bold uppercase tracking-wider ' + 
                        (item.stock <= 0 ? 'bg-rose-100 text-rose-800' : (item.stock < item.minimum_stock ? 'bg-amber-100 text-amber-800' : 'bg-emerald-100 text-emerald-800'));

                    document.getElementById('btnResDetail').href = `/items/${item.id}`;
                    const btnIn = document.getElementById('btnResIncoming');
                    if (btnIn) btnIn.href = `/inventory/incoming?item_id=${item.id}`;
                    const btnOut = document.getElementById('btnResOutgoing');
                    if (btnOut) btnOut.href = `/inventory/outgoing?item_id=${item.id}`;
                } else {
                    document.getElementById('scanSuccessState').style.display = 'none';
                    document.getElementById('scanNotFoundState').style.display = 'block';
                    document.getElementById('nfCode').innerText = code;
                }
            })
            .catch(err => {
                console.error("Scan lookup error:", err);
            })
            .finally(() => {
                setTimeout(() => { isProcessing = false; }, 1500);
            });
    }

    document.addEventListener("DOMContentLoaded", function () {
        const urlParams = new URLSearchParams(window.location.search);
        const codeParam = urlParams.get('code');
        if (codeParam) {
            processScan(codeParam);
        }

        try {
            html5QrcodeScanner = new Html5QrcodeScanner("reader", { fps: 10, qrbox: { width: 220, height: 220 } }, false);
            html5QrcodeScanner.render((decodedText) => {
                processScan(decodedText);
            }, (error) => {});
        } catch (e) {
            console.error("HTML5-QRCode init error:", e);
        }
    });
</script>
@endpush
