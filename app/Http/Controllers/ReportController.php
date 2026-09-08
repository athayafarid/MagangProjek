<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\Category;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\IncomingItem;
use App\Models\OutgoingItem;
use App\Models\StockOpname;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class ReportController extends Controller
{
    // 1. LAPORAN STOK
    public function stockReport(Request $request)
    {
        $query = Item::with(['category', 'unit', 'location', 'supplier']);

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

        $items = $query->latest()->get();
        $categories = Category::all();
        $locations = Location::all();

        return view('reports.stock', compact('items', 'categories', 'locations'));
    }

    public function exportStockCsv(Request $request)
    {
        $query = Item::with(['category', 'unit', 'location', 'supplier']);

        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }

        if ($request->filled('location_id')) {
            $query->where('location_id', $request->location_id);
        }

        $items = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Stok_Inventory_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Kode Barang', 'Barcode', 'Nama Barang', 'Kategori', 'Stok', 'Minimum Stok', 'Satuan', 'Lokasi', 'Supplier', 'Status Stok']);

            foreach ($items as $index => $item) {
                fputcsv($file, [
                    $index + 1,
                    $item->code,
                    $item->barcode,
                    $item->name,
                    $item->category->name ?? '-',
                    $item->stock,
                    $item->minimum_stock,
                    $item->unit->name ?? '-',
                    $item->location->name ?? '-',
                    $item->supplier->name ?? '-',
                    $item->stock_status,
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // 2. LAPORAN BARANG MASUK
    public function incomingReport(Request $request)
    {
        $query = IncomingItem::with(['item', 'supplier', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }
        if ($request->filled('supplier_id')) {
            $query->where('supplier_id', $request->supplier_id);
        }

        $incomingItems = $query->latest('transaction_date')->get();
        $suppliers = Supplier::all();

        return view('reports.incoming', compact('incomingItems', 'suppliers'));
    }

    public function exportIncomingCsv(Request $request)
    {
        $query = IncomingItem::with(['item', 'supplier', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $items = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Barang_Masuk_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'No Transaksi', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Supplier', 'Jumlah', 'Petugas', 'Keterangan']);

            foreach ($items as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->transaction_number,
                    $row->transaction_date->format('Y-m-d H:i'),
                    $row->item->code ?? '-',
                    $row->item->name ?? '-',
                    $row->supplier->name ?? '-',
                    $row->quantity,
                    $row->user->name ?? '-',
                    $row->description ?? '-',
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // 3. LAPORAN BARANG KELUAR
    public function outgoingReport(Request $request)
    {
        $query = OutgoingItem::with(['item', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $outgoingItems = $query->latest('transaction_date')->get();

        return view('reports.outgoing', compact('outgoingItems'));
    }

    public function exportOutgoingCsv(Request $request)
    {
        $query = OutgoingItem::with(['item', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('transaction_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('transaction_date', '<=', $request->end_date);
        }

        $items = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Barang_Keluar_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($items) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'No Transaksi', 'Tanggal', 'Kode Barang', 'Nama Barang', 'Jumlah', 'Tujuan', 'Pemohon', 'Petugas', 'Keterangan']);

            foreach ($items as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->transaction_number,
                    $row->transaction_date->format('Y-m-d H:i'),
                    $row->item->code ?? '-',
                    $row->item->name ?? '-',
                    $row->quantity,
                    $row->destination,
                    $row->requester,
                    $row->user->name ?? '-',
                    $row->description ?? '-',
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }

    // 4. LAPORAN STOCK OPNAME
    public function opnameReport(Request $request)
    {
        $query = StockOpname::with(['item', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('opname_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('opname_date', '<=', $request->end_date);
        }
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $opnames = $query->latest('opname_date')->get();

        return view('reports.opname', compact('opnames'));
    }

    public function exportOpnameCsv(Request $request)
    {
        $query = StockOpname::with(['item', 'user']);

        if ($request->filled('start_date')) {
            $query->whereDate('opname_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('opname_date', '<=', $request->end_date);
        }

        $opnames = $query->get();

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="Laporan_Stock_Opname_' . date('Y-m-d') . '.csv"',
        ];

        $callback = function () use ($opnames) {
            $file = fopen('php://output', 'w');
            fputcsv($file, ['No', 'Tanggal Opname', 'Kode Barang', 'Nama Barang', 'Stok Sistem', 'Stok Fisik', 'Selisih', 'Status Opname', 'Petugas', 'Catatan']);

            foreach ($opnames as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->opname_date->format('Y-m-d H:i'),
                    $row->item->code ?? '-',
                    $row->item->name ?? '-',
                    $row->stock_system,
                    $row->stock_physical,
                    $row->difference,
                    $row->status,
                    $row->user->name ?? '-',
                    $row->notes ?? '-',
                ]);
            }
            fclose($file);
        };

        return new StreamedResponse($callback, 200, $headers);
    }
}
