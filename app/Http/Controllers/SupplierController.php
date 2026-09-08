<?php

namespace App\Http\Controllers;

use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class SupplierController extends Controller
{
    public function index()
    {
        $suppliers = Supplier::withCount('items')->latest()->paginate(10);
        return view('master.suppliers.index', compact('suppliers'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $supplier = Supplier::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menambahkan supplier baru: ' . $supplier->name,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $supplier = Supplier::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'address' => ['nullable', 'string'],
            'phone' => ['nullable', 'string', 'max:50'],
            'email' => ['nullable', 'email', 'max:255'],
            'contact_person' => ['nullable', 'string', 'max:100'],
            'description' => ['nullable', 'string'],
        ]);

        $supplier->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' memperbarui data supplier: ' . $supplier->name,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Data Supplier berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $supplier = Supplier::findOrFail($id);

        if ($supplier->items()->count() > 0) {
            return back()->withErrors(['error' => 'Supplier tidak dapat dihapus karena terhubung dengan barang inventory.']);
        }

        $name = $supplier->name;
        $supplier->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menghapus data supplier: ' . $name,
        ]);

        return redirect()->route('suppliers.index')->with('success', 'Supplier berhasil dihapus.');
    }
}
