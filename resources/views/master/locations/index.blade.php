@extends('layouts.app')

@section('title', 'Master Lokasi Penyimpanan')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-map-marker-outline text-blue-600"></i>
            Master Lokasi / Rak Penyimpanan
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola posisi rak dan tata letak penyimpanan barang di warehouse PT Besmindo</p>
    </div>

    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-600/20 transition-all">
        <i class="mdi mdi-plus text-base"></i> Tambah Lokasi
    </button>
</div>

<!-- TABLE LOCATIONS -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">Nama Rak / Lokasi</th>
                    <th class="py-3.5 px-4">Deskripsi / Detail Gudang</th>
                    <th class="py-3.5 px-4 text-center">Tersimpan Barang</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($locations as $index => $loc)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $locations->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 font-mono font-bold text-amber-700">{{ $loc->name }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $loc->description ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold border border-blue-200">{{ $loc->items_count }} item</span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="editLocation({{ json_encode($loc) }})" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                <i class="mdi mdi-pencil-outline text-base"></i>
                            </button>
                            <form action="{{ route('locations.destroy', $loc->id) }}" method="POST" onsubmit="return confirm('Hapus lokasi ini?')" class="inline">
                                @csrf
                                @method('DELETE')
                                <button type="submit" class="p-1.5 text-slate-500 hover:text-rose-600 hover:bg-rose-50 rounded-lg transition-colors" title="Hapus">
                                    <i class="mdi mdi-delete-outline text-base"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="5" class="py-12 text-center text-slate-400">Belum ada data lokasi.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($locations->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $locations->links() }}
    </div>
    @endif
</div>

<!-- MODAL ADD -->
<div id="modalAdd" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900">Tambah Lokasi Rak Baru</h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>
        <form action="{{ route('locations.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Nama Rak / Kode Lokasi *</label>
                    <input type="text" name="name" required placeholder="Contoh: Rak A-01" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi / Detail Gudang</label>
                    <textarea name="description" rows="3" placeholder="Contoh: Gudang Utama Sektor A Baris 1..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50"></textarea>
                </div>
            </div>
            <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalAdd').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 shadow-md shadow-blue-600/20">Simpan</button>
            </div>
        </form>
    </div>
</div>

<!-- MODAL EDIT -->
<div id="modalEdit" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900">Edit Lokasi Rak</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Nama Rak / Kode Lokasi *</label>
                    <input type="text" name="name" id="editName" required class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" id="editDescription" rows="3" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50"></textarea>
                </div>
            </div>
            <div class="p-4 px-6 border-t border-slate-100 bg-slate-50/50 flex justify-end gap-2">
                <button type="button" onclick="document.getElementById('modalEdit').classList.add('hidden')" class="px-4 py-2 rounded-xl bg-white border border-slate-200 text-slate-700 font-semibold text-xs hover:bg-slate-50">Batal</button>
                <button type="submit" class="px-4 py-2 rounded-xl bg-blue-600 text-white font-semibold text-xs hover:bg-blue-700 shadow-md shadow-blue-600/20">Simpan</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    function editLocation(loc) {
        document.getElementById('formEdit').action = '/locations/' + loc.id;
        document.getElementById('editName').value = loc.name;
        document.getElementById('editDescription').value = loc.description || '';
        document.getElementById('modalEdit').classList.remove('hidden');
    }
</script>
@endpush
