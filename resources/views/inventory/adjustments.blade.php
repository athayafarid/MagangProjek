@extends('layouts.app')

@section('title', 'Penyesuaian Stok')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-tune-variant text-amber-600"></i>
            Penyesuaian Stok (Stock Adjustment)
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Pencocokan stok sistem jika terjadi selisih akibat kerusakan, kehilangan, atau kesalahan pencatatan</p>
    </div>

    @if(Auth::user()->isAdmin() || Auth::user()->isStaff())
    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-amber-600 hover:bg-amber-700 text-white font-semibold text-xs shadow-md shadow-amber-600/20 transition-all">
        <i class="mdi mdi-plus text-base"></i> Catat Penyesuaian
    </button>
    @endif
</div>

<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">Tanggal Adjust</th>
                    <th class="py-3.5 px-4">Barang Inventory</th>
                    <th class="py-3.5 px-4 text-center">Stok Sistem</th>
                    <th class="py-3.5 px-4 text-center">Stok Fisik</th>
                    <th class="py-3.5 px-4 text-center">Selisih</th>
                    <th class="py-3.5 px-4">Alasan Penyesuaian</th>
                    <th class="py-3.5 px-4">Petugas</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($adjustments as $index => $row)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $adjustments->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->adjustment_date->format('d/m/Y H:i') }}</td>
                    <td class="py-3.5 px-4">
                        <strong class="block text-slate-900 font-bold">{{ $row->item->name }}</strong>
                        <span class="text-slate-400 text-[11px] font-mono block">{{ $row->item->code }}</span>
                    </td>
                    <td class="py-3.5 px-4 text-center font-bold text-slate-700">{{ $row->stock_system }}</td>
                    <td class="py-3.5 px-4 text-center font-bold text-slate-700">{{ $row->stock_physical }}</td>
                    <td class="py-3.5 px-4 text-center font-bold {{ $row->difference < 0 ? 'text-rose-600' : ($row->difference > 0 ? 'text-emerald-600' : 'text-slate-400') }}">
                        {{ $row->difference > 0 ? '+' . $row->difference : $row->difference }}
                    </td>
                    <td class="py-3.5 px-4">
                        <span class="px-2.5 py-0.5 rounded-full text-[10px] font-semibold bg-slate-100 text-slate-700 border border-slate-200">
                            {{ $row->reason }}
                        </span>
                    </td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $row->user->name }}</td>
                </tr>
                @empty
                <tr>
                    <td colspan="8" class="py-12 text-center text-slate-400">Belum ada riwayat penyesuaian stok.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($adjustments->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $adjustments->links() }}
    </div>
    @endif
</div>

<!-- MODAL ADD PENYESUAIAN -->
@if(Auth::user()->isAdmin() || Auth::user()->isStaff())
<div id="modalAdd" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900 flex items-center gap-2">
                <i class="mdi mdi-tune-variant text-amber-600 text-lg"></i> Input Penyesuaian Stok
            </h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>

        <form action="{{ route('inventory.adjustments.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Pilih Barang Inventory *</label>
                    <select name="item_id" id="adjItemId" required onchange="calcDiff()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="">-- Pilih Barang --</option>
                        @foreach($items as $item)
                            <option value="{{ $item->id }}" data-stock="{{ $item->stock }}">
                                {{ $item->code }} - {{ $item->name }} (Stok Sistem: {{ $item->stock }})
                            </option>
                        @endforeach
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Stok Hasil Penghitungan Fisik *</label>
                    <input type="number" name="stock_physical" id="stockPhysical" min="0" required placeholder="0" oninput="calcDiff()" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm font-bold text-amber-600 focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>

                <!-- DIFF PREVIEW -->
                <div class="p-3 rounded-xl bg-slate-50 border border-slate-200 text-xs flex justify-between items-center">
                    <span class="text-slate-500">Selisih Stok:</span>
                    <span id="lblDiff" class="font-bold text-base text-slate-800">0</span>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Alasan Penyesuaian *</label>
                    <select name="reason" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                        <option value="Kerusakan">Kerusakan Barang</option>
                        <option value="Kehilangan">Kehilangan</option>
                        <option value="Kesalahan pencatatan">Kesalahan Pencatatan</option>
                        <option value="Lainnya">Lainnya</option>
                    </select>
                </div>

                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Tanggal Adjust *</label>
                    <input type="datetime-local" name="adjustment_date" value="{{ date('Y-m-d\TH:i') }}" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>
            </div>

            <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-amber-600 text-white font-semibold text-xs hover:bg-amber-700 shadow-md shadow-amber-600/20 flex items-center gap-1">
                    <i class="mdi mdi-check text-base"></i> Simpan Adjustment
                </button>
            </div>
        </form>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>
    function calcDiff() {
        const select = document.getElementById('adjItemId');
        const option = select.options[select.selectedIndex];
        const sysStock = option && option.value ? parseInt(option.getAttribute('data-stock')) || 0 : 0;
        const physStock = parseInt(document.getElementById('stockPhysical').value) || 0;
        const diff = physStock - sysStock;

        const lbl = document.getElementById('lblDiff');
        lbl.innerText = (diff > 0 ? '+' : '') + diff;
        if (diff < 0) {
            lbl.className = 'font-bold text-base text-rose-600';
        } else if (diff > 0) {
            lbl.className = 'font-bold text-base text-emerald-600';
        } else {
            lbl.className = 'font-bold text-base text-slate-400';
        }
    }
</script>
@endpush
