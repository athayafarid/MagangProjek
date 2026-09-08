<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class BarcodeController extends Controller
{
    public function scanPage(Request $request)
    {
        $initialCode = $request->query('code');
        return view('barcode.scan', compact('initialCode'));
    }

    public function generatePage(Request $request)
    {
        $items = Item::with(['category', 'location'])->where('status', 'active')->get();
        $selectedItemId = $request->query('item_id');
        $selectedItem = $selectedItemId ? Item::with(['category', 'unit', 'location', 'supplier'])->find($selectedItemId) : null;

        return view('barcode.generate', compact('items', 'selectedItem'));
    }

    public function scanApi($code)
    {
        $decodedCode = urldecode($code);

        // Check if code contains a URL query or full URL (e.g. from HP camera scan)
        if (str_contains($decodedCode, 'code=')) {
            parse_str(parse_url($decodedCode, PHP_URL_QUERY) ?? '', $query);
            if (!empty($query['code'])) {
                $decodedCode = $query['code'];
            }
        } elseif (filter_var($decodedCode, FILTER_VALIDATE_URL)) {
            $path = parse_url($decodedCode, PHP_URL_PATH);
            $parts = explode('/', trim($path, '/'));
            $decodedCode = end($parts);
        }

        $item = Item::with(['category', 'unit', 'location', 'supplier'])
            ->where('code', $decodedCode)
            ->orWhere('barcode', $decodedCode)
            ->orWhere('id', $decodedCode)
            ->first();

        if (Auth::check()) {
            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Scan Barcode',
                'description' => Auth::user()->name . ' memindai barcode/QR Code: ' . $decodedCode . ($item ? ' (' . $item->name . ')' : ' (Barang tidak ditemukan)'),
            ]);
        }

        if (!$item) {
            return response()->json([
                'success' => false,
                'message' => 'Barang tidak ditemukan dalam sistem.',
                'code' => $decodedCode,
            ], 404);
        }

        return response()->json([
            'success' => true,
            'item' => [
                'id' => $item->id,
                'code' => $item->code,
                'barcode' => $item->barcode,
                'name' => $item->name,
                'category' => $item->category->name,
                'description' => $item->description,
                'stock' => $item->stock,
                'unit' => $item->unit->name,
                'stock_status' => $item->stock_status,
                'location' => $item->location->name,
                'supplier' => $item->supplier->name,
                'image_url' => $item->image ? asset($item->image) : asset('images/no-image.png'),
                'detail_url' => route('items.show', $item->id),
            ]
        ]);
    }
}
