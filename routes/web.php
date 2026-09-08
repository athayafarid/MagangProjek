<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\ItemController;
use App\Http\Controllers\CategoryController;
use App\Http\Controllers\UnitController;
use App\Http\Controllers\LocationController;
use App\Http\Controllers\SupplierController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\InventoryController;
use App\Http\Controllers\BarcodeController;
use App\Http\Controllers\ReportController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/login', [AuthController::class, 'showLoginForm'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

/*
|--------------------------------------------------------------------------
| Authenticated User Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['auth'])->group(function () {

    // Dashboard (All roles can access)
    Route::get('/dashboard', [DashboardController::class, 'index'])->name('dashboard');

    // Barcode Scanning API & View (All roles can access scan)
    Route::get('/barcode/scan', [BarcodeController::class, 'scanPage'])->name('barcode.scan');
    Route::get('/barcode/scan-api/{code}', [BarcodeController::class, 'scanApi'])->name('barcode.scan.api');

    // Barang Read-Only Details (All roles can view detail)
    Route::get('/items/detail/{id}', [ItemController::class, 'show'])->name('items.show');

    /*
    |--------------------------------------------------------------------------
    | ADMIN ONLY ROUTES
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin'])->group(function () {
        // Master User Management
        Route::resource('users', UserController::class)->except(['create', 'show', 'edit']);

        // Full Item CRUD Management (Add/Edit/Delete)
        Route::get('/items/create', [ItemController::class, 'create'])->name('items.create');
        Route::post('/items', [ItemController::class, 'store'])->name('items.store');
        Route::get('/items/{id}/edit', [ItemController::class, 'edit'])->name('items.edit');
        Route::put('/items/{id}', [ItemController::class, 'update'])->name('items.update');
        Route::delete('/items/{id}', [ItemController::class, 'destroy'])->name('items.destroy');

        // Master Data CRUD (Categories, Units, Locations, Suppliers)
        Route::resource('categories', CategoryController::class)->except(['create', 'show', 'edit']);
        Route::resource('units', UnitController::class)->except(['create', 'show', 'edit']);
        Route::resource('locations', LocationController::class)->except(['create', 'show', 'edit']);
        Route::resource('suppliers', SupplierController::class)->except(['create', 'show', 'edit']);

        // Barcode Generation & Printing
        Route::get('/barcode/generate', [BarcodeController::class, 'generatePage'])->name('barcode.generate');
    });

    /*
    |--------------------------------------------------------------------------
    | ADMIN & STAFF ROUTES (Operational Access)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,staff'])->group(function () {
        // Inventory Transactions Creation
        Route::post('/inventory/incoming', [InventoryController::class, 'storeIncoming'])->name('inventory.incoming.store');
        Route::post('/inventory/outgoing', [InventoryController::class, 'storeOutgoing'])->name('inventory.outgoing.store');
        Route::post('/inventory/adjustments', [InventoryController::class, 'storeAdjustment'])->name('inventory.adjustments.store');
        Route::post('/inventory/opnames', [InventoryController::class, 'storeOpname'])->name('inventory.opnames.store');
    });

    /*
    |--------------------------------------------------------------------------
    | ALL ROLES VIEW ROUTES (Admin, Staff, Supervisor)
    |--------------------------------------------------------------------------
    */
    Route::middleware(['role:admin,staff,supervisor'])->group(function () {
        // Items Listing
        Route::get('/items', [ItemController::class, 'index'])->name('items.index');

        // Inventory Stock & Transaction Pages
        Route::get('/inventory/stock', [InventoryController::class, 'stockIndex'])->name('inventory.stock');
        Route::get('/inventory/incoming', [InventoryController::class, 'incomingIndex'])->name('inventory.incoming');
        Route::get('/inventory/outgoing', [InventoryController::class, 'outgoingIndex'])->name('inventory.outgoing');
        Route::get('/inventory/adjustments', [InventoryController::class, 'adjustmentIndex'])->name('inventory.adjustments');
        Route::get('/inventory/opnames', [InventoryController::class, 'opnameIndex'])->name('inventory.opnames');
        Route::get('/inventory/history', [InventoryController::class, 'historyIndex'])->name('inventory.history');

        // Reports
        Route::get('/reports/stock', [ReportController::class, 'stockReport'])->name('reports.stock');
        Route::get('/reports/stock/export', [ReportController::class, 'exportStockCsv'])->name('reports.stock.export');

        Route::get('/reports/incoming', [ReportController::class, 'incomingReport'])->name('reports.incoming');
        Route::get('/reports/incoming/export', [ReportController::class, 'exportIncomingCsv'])->name('reports.incoming.export');

        Route::get('/reports/outgoing', [ReportController::class, 'outgoingReport'])->name('reports.outgoing');
        Route::get('/reports/outgoing/export', [ReportController::class, 'exportOutgoingCsv'])->name('reports.outgoing.export');

        Route::get('/reports/opname', [ReportController::class, 'opnameReport'])->name('reports.opname');
        Route::get('/reports/opname/export', [ReportController::class, 'exportOpnameCsv'])->name('reports.opname.export');
    });
});
