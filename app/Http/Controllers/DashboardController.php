<?php

namespace App\Http\Controllers;

use App\Models\Item;
use App\Models\IncomingItem;
use App\Models\OutgoingItem;
use App\Models\ActivityLog;
use Illuminate\Support\Facades\DB;

class DashboardController extends Controller
{
    public function index()
    {
        $totalItems = Item::where('status', 'active')->count();
        $totalStock = Item::where('status', 'active')->sum('stock');
        $incomingCount = IncomingItem::count();
        $outgoingCount = OutgoingItem::count();
        
        $lowStockCount = Item::where('status', 'active')
            ->whereColumn('stock', '<', 'minimum_stock')
            ->count();

        // Recent Activities
        $recentActivities = ActivityLog::with('user')
            ->latest()
            ->take(10)
            ->get();

        // Chart Data (Last 6 Months)
        $months = [];
        $incomingMonthly = [];
        $outgoingMonthly = [];

        for ($i = 5; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $monthLabel = $date->translatedFormat('M Y');
            $months[] = $monthLabel;

            $incQty = IncomingItem::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('quantity');

            $outQty = OutgoingItem::whereYear('transaction_date', $date->year)
                ->whereMonth('transaction_date', $date->month)
                ->sum('quantity');

            $incomingMonthly[] = (int) $incQty;
            $outgoingMonthly[] = (int) $outQty;
        }

        $lowStockItems = Item::with(['category', 'location', 'unit'])
            ->where('status', 'active')
            ->whereColumn('stock', '<=', 'minimum_stock')
            ->orderBy('stock', 'asc')
            ->take(5)
            ->get();

        return view('dashboard', compact(
            'totalItems',
            'totalStock',
            'incomingCount',
            'outgoingCount',
            'lowStockCount',
            'recentActivities',
            'months',
            'incomingMonthly',
            'outgoingMonthly',
            'lowStockItems'
        ));
    }
}
