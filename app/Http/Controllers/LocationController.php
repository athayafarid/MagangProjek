<?php

namespace App\Http\Controllers;

use App\Models\Location;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LocationController extends Controller
{
    public function index()
    {
        $locations = Location::withCount('items')->latest()->paginate(10);
        return view('master.locations.index', compact('locations'));
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:locations,name'],
            'description' => ['nullable', 'string'],
        ]);

        $loc = Location::create($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menambahkan lokasi penyimpan baru: ' . $loc->name,
        ]);

        return redirect()->route('locations.index')->with('success', 'Lokasi penyimpanan berhasil ditambahkan.');
    }

    public function update(Request $request, $id)
    {
        $location = Location::findOrFail($id);
        $validated = $request->validate([
            'name' => ['required', 'string', 'max:255', 'unique:locations,name,' . $location->id],
            'description' => ['nullable', 'string'],
        ]);

        $location->update($validated);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' memperbarui lokasi: ' . $location->name,
        ]);

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil diperbarui.');
    }

    public function destroy($id)
    {
        $location = Location::findOrFail($id);

        if ($location->items()->count() > 0) {
            return back()->withErrors(['error' => 'Lokasi ini tidak dapat dihapus karena terdapat barang yang tersimpan di lokasi ini.']);
        }

        $name = $location->name;
        $location->delete();

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Master Data',
            'description' => Auth::user()->name . ' menghapus lokasi: ' . $name,
        ]);

        return redirect()->route('locations.index')->with('success', 'Lokasi berhasil dihapus.');
    }
}
