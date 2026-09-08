@extends('layouts.app')

@section('title', 'Pencatatan Barang Masuk')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-arrow-bottom-left text-emerald-600"></i>
            Pencatatan Transaksi Barang Masuk
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Pencatatan penerimaan barang dari supplier untuk menambah persediaan stok warehouse</p>
    </div>
    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-emerald-600 hover:bg-emerald-700 text-white font-semibold text-xs shadow-md shadow-emerald-600/20 transition-all">
        <i class="mdi mdi-plus text-base"></i> Catat Barang Masuk
    </button>
    @endif
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">No Transaksi</th>
                    <th class="py-3.5 px-4">Tanggal Masuk</th>
                    <th class="py-3.5 px-4">Barang Inventory</th>
                    <th class="py-3.5 px-4">Supplier</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Masuk</th>
                    <th class="py-3.5 px-4">Petugas Input</th>
                    <th class="py-3.5 px-4">Keterangan</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($incomingItems as $index => $row)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $incomingItems->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">{{ $row->transaction_number }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="py-3.5 px-4">
                        <strong class="block text-slate-900 font-bold">{{ $row->item->name }}</strong>
                        <span class="text-slate-400 text-[11px] font-mono block">{{ $row->item->code }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $row->supplier->name }}</td>
                    <td class="py-3.5 px-4 text-center font-bold text-emerald-600 text-sm">
                        +{{ number_format($row->quantity) }} {{ $row->item->unit->name }}
                    </td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $row->user->name }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->description ?? '-' }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">Belum ada riwayat transaksi barang masuk.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($incomingItems->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $incomingItems->links() }}
    </div>
    @endif
</div>

<!-- MODAL ADD TRANSAKSI MASUK -->
@if(Auth::user()->isAdmin() || Auth::user()->isStaff())
<div id="modalAdd" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 {{ request('item_id') ? '' : 'hidden' }}">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="mdi mdi-arrow-bottom-left text-emerald-600 text-lg"></i> Input Transaksi Barang Masuk
            </h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>

        <form action="{{ route('inventory.incoming.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Pilih Barang Inventory *</label>
                    <select name="item_id" id="item_id" required onchange="updateStockPreview(this)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit->name }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->code }} - {{ $item->name }} (Stok Saat ini: {{ $item->stock }} {{ $item->unit->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Supplier Pemasok *</label>
                        <select name="supplier_id" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                            <option value="">-- Pilih Supplier --</option>
                            @foreach($suppliers as $sup)
                                <option value="{{ $sup->id }}">{{ $sup->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Transaksi *</label>
                        <input type="datetime-local" name="transaction_date" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Jumlah Masuk (Qty) *</label>
                    <input type="number" name="quantity" id="incomingQty" min="1" required placeholder="0" oninput="calculateAfter()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-emerald-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <!-- STOCK PREVIEW CALCULATOR -->
                <div id="stockCalcBox" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Stok Sebelum:</span>
                        <span id="lblBefore" class="font-bold text-slate-800">0</span>
                    </div>
                    <div class="flex justify-between text-emerald-600">
                        <span>Barang Masuk:</span>
                        <span id="lblIncoming" class="font-bold">+0</span>
                    </div>
                    <div class="flex justify-between pt-1.5 border-t border-slate-200 font-bold">
                        <span>Stok Sesudah:</span>
                        <span id="lblAfter" class="text-emerald-600">0</span>
                    </div>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Keterangan / Catatan Transaksi</label>
                    <textarea name="description" rows="2" placeholder="Contoh: Pembelian rutin PO-2026/08..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50"></textarea>
                </div>
            </div>

            <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-emerald-600 text-white font-semibold text-xs hover:bg-emerald-700 shadow-md shadow-emerald-600/20 flex items-center gap-1">
                    <i class="mdi mdi-check text-base"></i> Simpan Transaksi
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    let currentStock = 0;
    let currentUnit = '';

    function updateStockPreview(select) {
        const opt = select.options[select.selectedIndex];
        if (opt.value) {
            currentStock = parseInt(opt.getAttribute('data-stock')) || 0;
            currentUnit = opt.getAttribute('data-unit') || '';
        } else {
            currentStock = 0;
            currentUnit = '';
        }
        calculateAfter();
    }

    function calculateAfter() {
        const qty = parseInt(document.getElementById('incomingQty').value) || 0;
        document.getElementById('lblBefore').innerText = currentStock + ' ' + currentUnit;
        document.getElementById('lblIncoming').innerText = '+' + qty + ' ' + currentUnit;
        document.getElementById('lblAfter').innerText = (currentStock + qty) + ' ' + currentUnit;
    }

    document.addEventListener("DOMContentLoaded", function() {
        const sel = document.getElementById('item_id');
        if (sel && sel.value) updateStockPreview(sel);
    });
</script>
@endpush
