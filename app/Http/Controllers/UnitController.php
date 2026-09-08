<?php

namespace App\Http\Controllers;

use App\Models\Unit;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UnitController extends Controller
{
    public function index()
    {
        $units = Unit::withCount('items')->latest()->paginate(10);
        return view('master.units.index', compact('units'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name'],
        ]);

        $unit = Unit::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menambahkan satuan barang baru: ' . $unit->name,
        ]);

        return redirect()->route('units.index')->with('success', 'Satuan barang berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $unit = Unit::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:units,name,' . $unit->id],
        ]);

        $unit->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' memperbarui satuan: ' . $unit->name,
        ]);

        return redirect()->route('units.index')->with('success', 'Satuan berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $unit = Unit::findOrFail($id);

        if ($unit->items()->count() > 0) {
            return back()->withErrors(['error' => 'Satuan ini tidak dapat dihapus karena masih terikat dengan beberapa barang.']);
        }

        $name = $unit->name;
        $unit->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menghapus satuan: ' . $name,
        ]);

        return redirect()->route('units.index')->with('success', 'Satuan berhasil dihapus.');
    }
}
