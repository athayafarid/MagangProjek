@extends('layouts.app')

@section('title', 'Master Kategori Barang')

@section('content')
<div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 mb-6">
    <div>
        <h1 class="text-xl font-bold text-slate-900 flex items-center gap-2">
            <i class="mdi mdi-tag-multiple-outline text-blue-600"></i>
            Master Kategori Barang
        </h1>
        <p class="text-xs text-slate-500 mt-0.5">Kelola daftar pengelompokan jenis barang inventory</p>
    </div>

    <button onclick="document.getElementById('modalAdd').classList.remove('hidden')" class="inline-flex items-center gap-1.5 px-4 py-2 rounded-xl bg-blue-600 hover:bg-blue-700 text-white font-semibold text-xs shadow-md shadow-blue-600/20 transition-all">
        <i class="mdi mdi-plus text-base"></i> Tambah Kategori
    </button>
</div>

<!-- TABLE CATEGORIES -->
<div class="bg-white rounded-2xl border border-slate-200/80 shadow-xs overflow-hidden">
    <div class="overflow-x-auto">
        <table class="w-full text-left border-collapse">
            <thead>
                <tr class="bg-slate-50 border-b border-slate-200 text-[11px] font-bold text-slate-500 uppercase tracking-wider">
                    <th class="py-3.5 px-4 text-center w-14">No</th>
                    <th class="py-3.5 px-4">Nama Kategori</th>
                    <th class="py-3.5 px-4">Deskripsi</th>
                    <th class="py-3.5 px-4 text-center">Jumlah Barang</th>
                    <th class="py-3.5 px-4 text-center">Aksi</th>
                </tr>
            </thead>
            <tbody class="divide-y divide-slate-100 text-xs font-medium">
                @forelse($categories as $index => $cat)
                <tr class="hover:bg-slate-50/80 transition-colors">
                    <td class="py-3.5 px-4 text-center text-slate-400">{{ $categories->firstItem() + $index }}</td>
                    <td class="py-3.5 px-4 font-bold text-slate-900">{{ $cat->name }}</td>
                    <td class="py-3.5 px-4 text-slate-500">{{ $cat->description ?? '-' }}</td>
                    <td class="py-3.5 px-4 text-center">
                        <span class="px-2.5 py-0.5 rounded-full bg-blue-50 text-blue-700 font-bold border border-blue-200">{{ $cat->items_count }} item</span>
                    </td>
                    <td class="py-3.5 px-4 text-center">
                        <div class="flex items-center justify-center gap-1">
                            <button onclick="editCategory({{ json_encode($cat) }})" class="p-1.5 text-slate-500 hover:text-blue-600 hover:bg-blue-50 rounded-lg transition-colors" title="Edit">
                                <i class="mdi mdi-pencil-outline text-base"></i>
                            </button>
                            <form action="{{ route('categories.destroy', $cat->id) }}" method="POST" onsubmit="return confirm('Hapus kategori ini?')" class="inline">
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
                    <td colspan="5" class="py-12 text-center text-slate-400">Belum ada data kategori.</td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($categories->hasPages())
    <div class="p-4 border-t border-slate-100 bg-slate-50/50">
        {{ $categories->links() }}
    </div>
    @endif
</div>

<!-- MODAL ADD -->
<div id="modalAdd" class="fixed inset-0 bg-slate-900/50 backdrop-blur-xs z-50 flex items-center justify-center p-4 hidden">
    <div class="bg-white rounded-2xl max-w-md w-full shadow-2xl border border-slate-200 overflow-hidden">
        <div class="p-4 px-6 border-b border-slate-100 flex items-center justify-between bg-slate-50/50">
            <h3 class="text-sm font-bold text-slate-900">Tambah Kategori Baru</h3>
            <button onclick="document.getElementById('modalAdd').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>
        <form action="{{ route('categories.store') }}" method="POST">
            @csrf
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Nama Kategori *</label>
                    <input type="text" name="name" required placeholder="Contoh: Electrical" class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50">
                </div>
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Deskripsi</label>
                    <textarea name="description" rows="3" placeholder="Keterangan singkat kategori..." class="w-full px-3.5 py-2.5 rounded-xl border border-slate-300 text-sm focus:outline-none focus:ring-2 focus:ring-blue-500/20 focus:border-blue-600 bg-slate-50/50"></textarea>
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
            <h3 class="text-sm font-bold text-slate-900">Edit Kategori</h3>
            <button onclick="document.getElementById('modalEdit').classList.add('hidden')" class="text-slate-400 hover:text-slate-600"><i class="mdi mdi-close text-lg"></i></button>
        </div>
        <form id="formEdit" method="POST">
            @csrf
            @method('PUT')
            <div class="p-6 space-y-4 text-xs">
                <div>
                    <label class="block font-semibold text-slate-700 mb-1.5">Nama Kategori *</label>
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
    function editCategory(cat) {
        document.getElementById('formEdit').action = '/categories/' + cat.id;
        document.getElementById('editName').value = cat.name;
        document.getElementById('editDescription').value = cat.description || '';
        document.getElementById('modalEdit').classList.remove('hidden');
    }
</script>
@endpush
