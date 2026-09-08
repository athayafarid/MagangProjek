@extends('layouts.app')

@section('title', 'Pencatatan Barang Keluar')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-arrow-top-right text-rose-600"></i>
            Pencatatan Transaksi Barang Keluar
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Pencatatan pengeluaran barang inventory untuk kebutuhan operasional workshop/proyek</p>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-rose-600 hover:bg-rose-700 text-white font-semibold text-xs shadow-md shadow-rose-600/20 transition-all">
        <i class="mdi mdi-minus text-base"></i> Catat Barang Keluar
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
                    <th class="py-3.5 px-4">Tanggal Keluar</th>
                    <th class="py-3.5 px-4">Barang Inventory</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Keluar</th>
                    <th class="py-3.5 px-4">Tujuan Penggunaan</th>
                    <th class="py-3.5 px-4">Pemohon / Penerima</th>
                    <th class="py-3.5 px-4">Petugas Input</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($outgoingItems as $index => $row)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $outgoingItems->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-blue-700">{{ $row->transaction_number }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->transaction_date->format('d/m/Y H:i') }}</td>
                    <td class="py-3.5 px-4">
                        <strong class="block text-slate-900 font-bold">{{ $row->item->name }}</strong>
                        <span class="text-slate-400 text-[11px] font-mono block">{{ $row->item->code }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-center font-bold text-rose-600 text-sm">
                        -{{ number_format($row->quantity) }} {{ $row->item->unit->name }}
                    </td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $row->destination }}</td>
                    <td class="py-3.5 px-4 text-slate-700">{{ $row->requester }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">Belum ada riwayat transaksi barang keluar.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($outgoingItems->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $outgoingItems->links() }}
    </div>
    @endif
</div>

<!-- MODAL ADD TRANSAKSI KELUAR -->
@if(Auth::user()->isAdmin() || Auth::user()->isStaff())
<div id="modalAdd" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 {{ request('item_id') ? '' : 'hidden' }}">
    <div class="bg-white rounded-2xl max-w-lg w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="mdi mdi-arrow-top-right text-rose-600 text-lg"></i> Input Transaksi Barang Keluar
            </h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>

        <form action="{{ route('inventory.outgoing.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Pilih Barang Inventory *</label>
                    <select name="item_id" id="item_id_out" required onchange="updateOutStockPreview(this)" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}" data-unit="{{ $item->unit->name }}" {{ request('item_id') == $item->id ? 'selected' : '' }}>
                                {{ $item->code }} - {{ $item->name }} (Stok Tersedia: {{ $item->stock }} {{ $item->unit->name }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Tujuan Penggunaan *</label>
                        <input type="text" name="destination" required placeholder="Contoh: Workshop Utama" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Nama Pemohon *</label>
                        <input type="text" name="requester" required placeholder="Contoh: Budi Teknik" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                    </div>
                </div>

                <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Jumlah Keluar (Qty) *</label>
                        <input type="number" name="quantity" id="outgoingQty" min="1" required placeholder="0" oninput="calculateOutAfter()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-rose-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                    </div>

                    <div>
                        <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Transaksi *</label>
                        <input type="datetime-local" name="transaction_date" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                    </div>
                </div>

                <!-- STOCK PREVIEW CALCULATOR -->
                <div id="stockOutCalcBox" class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs space-y-1.5">
                    <div class="flex justify-between">
                        <span class="text-slate-500">Stok Sebelum:</span>
                        <span id="lblOutBefore" class="font-bold text-slate-800">0</span>
                    </div>
                    <div class="flex justify-between text-rose-600">
                        <span>Barang Keluar:</span>
                        <span id="lblOutgoing" class="font-bold">-0</span>
                    </div>
                    <div class="flex justify-between pt-1.5 border-t border-slate-200 font-bold">
                        <span>Stok Sisa:</span>
                        <span id="lblOutAfter">0</span>
                    </div>
                </div>

                <div id="warnInsufficientStock" class="p-3 rounded-xl bg-rose-50 border border-rose-200 text-rose-700 text-xs flex items-center gap-2 hidden">
                    <i class="mdi mdi-alert-outline text-base"></i>
                    <span>Stok Tidak Mencukupi! Jumlah diminta melebihi stok yang tersedia.</span>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Keterangan Tambahan</label>
                    <textarea name="description" rows="2" placeholder="Operasional penggantian conveyor belt..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50"></textarea>
                </div>
            </div>

            <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" id="btnSubmitOut" class="px-4 py-2 rounded-xl bg-rose-600 text-white font-semibold text-xs hover:bg-rose-700 shadow-md shadow-rose-600/20 flex items-center gap-1">
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
    let outStock = 0;
    let outUnit = '';

    function updateOutStockPreview(select) {
        const opt = select.options[select.selectedIndex];
        if (opt.value) {
            outStock = parseInt(opt.getAttribute('data-stock')) || 0;
            outUnit = opt.getAttribute('data-unit') || '';
        } else {
            outStock = 0;
            outUnit = '';
        }
        calculateOutAfter();
    }

    function calculateOutAfter() {
        const qty = parseInt(document.getElementById('outgoingQty').value) || 0;
        const after = outStock - qty;

        document.getElementById('lblOutBefore').innerText = outStock + ' ' + outUnit;
        document.getElementById('lblOutgoing').innerText = '-' + qty + ' ' + outUnit;
        
        const lblAfter = document.getElementById('lblOutAfter');
        lblAfter.innerText = after + ' ' + outUnit;

        const warn = document.getElementById('warnInsufficientStock');
        const btn = document.getElementById('btnSubmitOut');

        if (qty > outStock) {
            lblAfter.className = 'font-bold text-rose-600';
            warn.classList.remove('hidden');
            if (btn) btn.disabled = true;
        } else {
            lblAfter.className = 'font-bold text-slate-900';
            warn.classList.add('hidden');
            if (btn) btn.disabled = false;
        }
    }

    document.addEventListener("DOMContentLoaded", function() {
        const sel = document.getElementById('item_id_out');
        if (sel && sel.value) updateOutStockPreview(sel);
    });
</script>
@endpush
