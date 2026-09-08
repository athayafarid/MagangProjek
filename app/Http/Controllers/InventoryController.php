<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Supplier;
use App\Models\IncomingItem;
use App\Models\OutgoingItem;
use App\Models\StockAdjustment;
use App\Models\StockOpname;
use App\Models\StockTransaction;
use App\Models\ActivityLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class InventoryController extends Controller
{
    // --- STOK BARANG ---
    public function stockIndex(Request $request)
    {
        $query = Item::with(['category', 'unit', 'location']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%")
                  ->orWhere('barcode', 'like', "%{$search}%");
            });
        }

        $items = $query->latest()->paginate(10)->withQueryString();
        return view('inventory.stock', compact('items'));
    }

    // --- BARANG MASUK ---
    public function incomingIndex(Request $request)
    {
        $query = IncomingItem::with(['item', 'supplier', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhere('transaction_number', 'like', "%{$search}%");
        }

        $incomingItems = $query->latest()->paginate(10)->withQueryString();
        $items = Item::where('status', 'active')->get();
        $suppliers = Supplier::all();

        return view('inventory.incoming', compact('incomingItems', 'items', 'suppliers'));
    }

    public function storeIncoming(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'supplier_id' => ['required', 'exists:suppliers,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        return DB::transaction(function () use ($validated, $request) {
            $item = Item::findOrFail($validated['item_id']);
            $trxNumber = 'TRX-IN-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $stockBefore = $item->stock;
            $stockAfter = $stockBefore + $validated['quantity'];

            // Save Incoming Transaction Record
            $incoming = IncomingItem::create([
                'transaction_number' => $trxNumber,
                'item_id' => $item->id,
                'supplier_id' => $validated['supplier_id'],
                'quantity' => $validated['quantity'],
                'description' => $validated['description'] ?? null,
                'user_id' => Auth::id(),
                'transaction_date' => $validated['transaction_date'],
            ]);

            // Update Item Stock
            $item->update(['stock' => $stockAfter]);

            // Unified Ledger Entry
            StockTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'transaction_type' => 'IN',
                'quantity' => $validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_number' => $trxNumber,
                'description' => $validated['description'] ?? 'Pencatatan Barang Masuk',
                'transaction_date' => $validated['transaction_date'],
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Barang Masuk',
                'description' => Auth::user()->name . ' mencatat barang masuk: ' . $item->name . ' (+' . $validated['quantity'] . ' ' . $item->unit->name . ')',
            ]);

            return redirect()->route('inventory.incoming')->with('success', 'Transaksi barang masuk berhasil disimpan. Stok bertambah dari ' . $stockBefore . ' menjadi ' . $stockAfter . '.');
        });
    }

    // --- BARANG KELUAR ---
    public function outgoingIndex(Request $request)
    {
        $query = OutgoingItem::with(['item', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            })->orWhere('transaction_number', 'like', "%{$search}%");
        }

        $outgoingItems = $query->latest()->paginate(10)->withQueryString();
        $items = Item::where('status', 'active')->get();

        return view('inventory.outgoing', compact('outgoingItems', 'items'));
    }

    public function storeOutgoing(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'quantity' => ['required', 'integer', 'min:1'],
            'destination' => ['required', 'string', 'max:255'],
            'requester' => ['required', 'string', 'max:255'],
            'transaction_date' => ['required', 'date'],
            'description' => ['nullable', 'string'],
        ]);

        $item = Item::findOrFail($validated['item_id']);

        // Validation rule PRD BR-04 & Section 19: Stok Tidak Mencukupi
        if ($validated['quantity'] > $item->stock) {
            return back()->withErrors([
                'quantity' => "⚠️ Stok Tidak Mencukupi! Stok tersedia: {$item->stock} {$item->unit->name}, Jumlah diminta: {$validated['quantity']} {$item->unit->name}.",
            ])->withInput();
        }

        return DB::transaction(function () use ($validated, $item) {
            $trxNumber = 'TRX-OUT-' . date('Ymd') . '-' . strtoupper(substr(uniqid(), -5));

            $stockBefore = $item->stock;
            $stockAfter = $stockBefore - $validated['quantity'];

            // Save Outgoing Transaction Record
            OutgoingItem::create([
                'transaction_number' => $trxNumber,
                'item_id' => $item->id,
                'quantity' => $validated['quantity'],
                'destination' => $validated['destination'],
                'requester' => $validated['requester'],
                'description' => $validated['description'] ?? null,
                'user_id' => Auth::id(),
                'transaction_date' => $validated['transaction_date'],
            ]);

            // Update Item Stock
            $item->update(['stock' => $stockAfter]);

            // Unified Ledger Entry
            StockTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'transaction_type' => 'OUT',
                'quantity' => -$validated['quantity'],
                'stock_before' => $stockBefore,
                'stock_after' => $stockAfter,
                'reference_number' => $trxNumber,
                'description' => 'Tujuan: ' . $validated['destination'] . ', Pemohon: ' . $validated['requester'],
                'transaction_date' => $validated['transaction_date'],
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Barang Keluar',
                'description' => Auth::user()->name . ' mencatat barang keluar: ' . $item->name . ' (-' . $validated['quantity'] . ' ' . $item->unit->name . ')',
            ]);

            return redirect()->route('inventory.outgoing')->with('success', 'Transaksi barang keluar berhasil disimpan. Stok berkurang menjadi ' . $stockAfter . '.');
        });
    }

    // --- PENYESUAIAN STOK ---
    public function adjustmentIndex()
    {
        $adjustments = StockAdjustment::with(['item', 'user'])->latest()->paginate(10);
        $items = Item::where('status', 'active')->get();
        return view('inventory.adjustments', compact('adjustments', 'items'));
    }

    public function storeAdjustment(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'stock_physical' => ['required', 'integer', 'min:0'],
            'reason' => ['required', 'string'],
            'adjustment_date' => ['required', 'date'],
        ]);

        return DB::transaction(function () use ($validated) {
            $item = Item::findOrFail($validated['item_id']);
            $stockSystem = $item->stock;
            $stockPhysical = (int) $validated['stock_physical'];
            $difference = $stockPhysical - $stockSystem;

            $adj = StockAdjustment::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'stock_system' => $stockSystem,
                'stock_physical' => $stockPhysical,
                'difference' => $difference,
                'reason' => $validated['reason'],
                'adjustment_date' => $validated['adjustment_date'],
            ]);

            // Update item stock directly to physical count
            $item->update(['stock' => $stockPhysical]);

            // Record transaction
            StockTransaction::create([
                'item_id' => $item->id,
                'user_id' => Auth::id(),
                'transaction_type' => 'ADJUSTMENT',
                'quantity' => $difference,
                'stock_before' => $stockSystem,
                'stock_after' => $stockPhysical,
                'reference_number' => 'ADJ-' . $adj->id,
                'description' => 'Penyesuaian Stok (Alasan: ' . $validated['reason'] . ')',
                'transaction_date' => $validated['adjustment_date'],
            ]);

            ActivityLog::create([
                'user_id' => Auth::id(),
                'activity_type' => 'Penyesuaian Stok',
                'description' => Auth::user()->name . ' melakukan penyesuaian stok barang ' . $item->name . ' (Fisik: ' . $stockPhysical . ', Selisih: ' . $difference . ')',
            ]);

            return redirect()->route('inventory.adjustments')->with('success', 'Penyesuaian stok berhasil disimpan.');
        });
    }

    // --- STOCK OPNAME ---
    public function opnameIndex()
    {
        $opnames = StockOpname::with(['item', 'user'])->latest()->paginate(10);
        $items = Item::where('status', 'active')->get();
        return view('inventory.opnames', compact('opnames', 'items'));
    }

    public function storeOpname(Request $request)
    {
        $validated = $request->validate([
            'item_id' => ['required', 'exists:items,id'],
            'stock_physical' => ['required', 'integer', 'min:0'],
            'notes' => ['nullable', 'string'],
            'opname_date' => ['required', 'date'],
        ]);

        $item = Item::findOrFail($validated['item_id']);
        $stockSystem = $item->stock;
        $stockPhysical = (int) $validated['stock_physical'];
        $difference = $stockPhysical - $stockSystem;

        if ($difference === 0) {
            $status = 'Sesuai';
        } elseif ($difference < 0) {
            $status = 'Kurang';
        } else {
            $status = 'Lebih';
        }

        StockOpname::create([
            'item_id' => $item->id,
            'user_id' => Auth::id(),
            'stock_system' => $stockSystem,
            'stock_physical' => $stockPhysical,
            'difference' => $difference,
            'status' => $status,
            'notes' => $validated['notes'],
            'opname_date' => $validated['opname_date'],
        ]);

        ActivityLog::create([
            'user_id' => Auth::id(),
            'activity_type' => 'Stock Opname',
            'description' => Auth::user()->name . ' melakukan Stock Opname untuk ' . $item->name . ' (Status: ' . $status . ')',
        ]);

        return redirect()->route('inventory.opnames')->with('success', 'Hasil Stock Opname berhasil dicatat.');
    }

    // --- RIWAYAT TRANSAKSI ---
    public function historyIndex(Request $request)
    {
        $query = StockTransaction::with(['item.category', 'item.location', 'user']);

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('item', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('code', 'like', "%{$search}%");
            });
        }

        if ($request->filled('transaction_type')) {
            $query->where('transaction_type', $request->transaction_type);
        }

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }

        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $transactions = $query->latest('transaction_date')->paginate(15)->withQueryString();

        return view('inventory.history', compact('transactions'));
    }
}
