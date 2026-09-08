<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class CategoryController extends Controller
{
    public function index()
    {
        $categories = Category::withCount('items')->latest()->paginate(10);
        return view('master.categories.index', compact('categories'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name'],
            'description' => ['nullable', 'string'],
        ]);

        $cat = Category::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menambahkan kategori baru: ' . $cat->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $category = Category::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:categories,name,' . $category->id],
            'description' => ['nullable', 'string'],
        ]);

        $category->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' mengubah kategori: ' . $category->name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $category = Category::findOrFail($id);

        if ($category->items()->count() > 0) {
            return back()->withErrors(['error' => 'Kategori ini tidak dapat dihapus karena masih terhubung dengan beberapa barang.']);
        }

        $name = $category->name;
        $category->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menghapus kategori: ' . $name,
        ]);

        return redirect()->route('categories.index')->with('success', 'Kategori berhasil dihapus.');
    }
}
