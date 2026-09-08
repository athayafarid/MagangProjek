<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\File;

class ItemController extends Controller
{
    public function index(Request $request)
    {
        $query = Item::with(['category', 'unit', 'location', 'supplier']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%")
                  ->orWhereHas('category', function($catQuery) use ($search) {
                      $catQuery->where('name', 'like', "%{$search}%");
                  })
                  ->orWhereHas('location', function($locQuery) use ($search) {
                      $locQuery->where('name', 'like', "%{$search}%");
                  });
            });
        }

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        if ($request->filled('status_stock')) {
            match ($request->status_stock) {
                'aman' => $query->whereColumn('stock', '>', 'minimum_stock'),
                'perhatian' => $query->whereColumn('stock', '=', 'minimum_stock'),
                'rendah' => $query->whereColumn('stock', '<', 'minimum_stock')->where('stock', '>', 0),
                'habis' => $query->where('stock', '<=', 0),
                default => null,
            };
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        $categories = Category::all();
        $locations = Location::all();

        $totalItemsCount = Item::count();
        $totalPhysicalStock = Item::sum('stock');
        $lowStockCount = Item::whereColumn('stock', '<=', 'minimum_stock')->count();
        $totalActiveItems = Item::where('status', 'active')->count();

        return view('items.index', compact(
            'items',
            'categories',
            'locations',
            'totalItemsCount',
            'totalPhysicalStock',
            'lowStockCount',
            'totalActiveItems'
        ));
    }

    public function create()
    {
        $categories = Category::all();
        $units = Unit::all();
        $locations = Location::all();
        $suppliers = Supplier::all();

        // Generate next automatic code: BRG-0000X
        $lastItem = Item::latest('id')->first();
        $nextId = $lastItem ? $lastItem->id + 1 : 1;
        $autoCode = 'BRG-' . str_pad($nextId, 5, '0', STR_PAD_LEFT);

        return view('items.create', compact('categories', 'units', 'locations', 'suppliers', 'autoCode'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:items,code'],
            'barcode' => ['required', 'string', 'unique:items,barcode'],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'unit_id' => ['required', 'exists:units,id'],
            'stock' => ['required', 'integer', 'min:0'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location_id' => ['required', 'exists:locations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($request->hasFile('image')) {
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/items'), $imageName);
            $validated['image'] = 'uploads/items/' . $imageName;
        }

        $item = Item::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Tambah Barang',
            'description' => "Menambahkan barang baru: {$item->name} ({$item->code})"
        ]);

        return redirect()->route('items.index')->with('success', "Barang '{$item->name}' berhasil ditambahkan.");
    }

    public function show($id)
    {
        $item = Item::with(['category', 'unit', 'location', 'supplier'])->findOrFail($id);
        $recentTransactions = $item->transactions()->with('user')->latest()->take(5)->get();

        return view('items.show', compact('item', 'recentTransactions'));
    }

    public function edit($id)
    {
        $item = Item::findOrFail($id);
        $categories = Category::all();
        $units = Unit::all();
        $locations = Location::all();
        $suppliers = Supplier::all();

        return view('items.edit', compact('item', 'categories', 'units', 'locations', 'suppliers'));
    }

    public function update(Request $request, $id)
    {
        $item = Item::findOrFail($id);

        $validated = $request->validate([
            'code' => ['required', 'string', 'unique:items,code,' . $item->id],
            'barcode' => ['required', 'string', 'unique:items,barcode,' . $item->id],
            'name' => ['required', 'string', 'max:255'],
            'category_id' => ['required', 'exists:categories,id'],
            'description' => ['nullable', 'string'],
            'unit_id' => ['required', 'exists:units,id'],
            'minimum_stock' => ['required', 'integer', 'min:0'],
            'location_id' => ['required', 'exists:locations,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'image' => ['nullable', 'image', 'mimes:jpeg,jpg,png,webp', 'max:2048'],
            'status' => ['required', 'in:active,inactive'],
        ]);

        if ($request->hasFile('image')) {
            if ($item->image && File::exists(public_path($item->image))) {
                File::delete(public_path($item->image));
            }
            $imageName = time() . '_' . uniqid() . '.' . $request->image->extension();
            $request->image->move(public_path('uploads/items'), $imageName);
            $validated['image'] = 'uploads/items/' . $imageName;
        }

        $item->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Update Barang',
            'description' => "Memperbarui data barang: {$item->name} ({$item->code})"
        ]);

        return redirect()->route('items.show', $item->id)->with('success', "Data barang '{$item->name}' berhasil diperbarui.");
    }

    public function destroy($id)
    {
        $item = Item::findOrFail($id);
        $itemName = $item->name;

        if ($item->image && File::exists(public_path($item->image))) {
            File::delete(public_path($item->image));
        }

        $item->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Hapus Barang',
            'description' => "Menghapus barang: {$itemName}"
        ]);

        return redirect()->route('items.index')->with('success', "Barang '{$itemName}' berhasil dihapus.");
    }
}
