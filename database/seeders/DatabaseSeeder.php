<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Item;
use App\Models\IncomingItem;
use App\Models\OutgoingItem;
use App\Models\StockTransaction;
use App\Models\ActivityLog;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Carbon\Carbon;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Create Users
        $admin = User::create([
            'name' => 'Administrator Inventory',
            'username' => 'admin',
            'email' => 'admin@besmindo.co.id',
            'password' => Hash::make('admin123'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $staff = User::create([
            'name' => 'Budi Warehouse Staff',
            'username' => 'staff',
            'email' => 'staff@besmindo.co.id',
            'password' => Hash::make('staff123'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $supervisor = User::create([
            'name' => 'Andi Supervisor',
            'username' => 'supervisor',
            'email' => 'supervisor@besmindo.co.id',
            'password' => Hash::make('supervisor123'),
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        // 2. Create Categories
        $categoriesData = [
            ['name' => 'Sparepart', 'description' => 'Komponen pengganti untuk mesin dan perlengkapan'],
            ['name' => 'Tools', 'description' => 'Peralatan teknikal dan mekanikal warehouse'],
            ['name' => 'Electrical', 'description' => 'Perlengkapan listrik dan komponen elektronik'],
            ['name' => 'Mechanical', 'description' => 'Komponen mekanik dan permesinan berat'],
            ['name' => 'Consumable', 'description' => 'Barang pakai habis'],
            ['name' => 'Safety', 'description' => 'Alat Pelindung Diri (APD) dan peralatan K3'],
            ['name' => 'Material', 'description' => 'Bahan baku operasional perusahaan'],
            ['name' => 'Lainnya', 'description' => 'Kategori umum lainnya'],
        ];

        $categories = [];
        foreach ($categoriesData as $cat) {
            $categories[$cat['name']] = Category::create($cat);
        }

        // 3. Create Units
        $unitsData = ['PCS', 'Set', 'Box', 'Roll', 'Unit', 'Meter', 'Kg'];
        $units = [];
        foreach ($unitsData as $unitName) {
            $units[$unitName] = Unit::create(['name' => $unitName]);
        }

        // 4. Create Locations
        $locationsData = [
            ['name' => 'Rak A-01', 'description' => 'Gudang Utama - Sektor A Baris 1'],
            ['name' => 'Rak A-02', 'description' => 'Gudang Utama - Sektor A Baris 2'],
            ['name' => 'Rak B-01', 'description' => 'Gudang Utama - Sektor B Baris 1'],
            ['name' => 'Rak B-02', 'description' => 'Gudang Utama - Sektor B Baris 2'],
            ['name' => 'Rak C-01', 'description' => 'Gudang Suku Cadang Heavy Equipment'],
        ];
        $locations = [];
        foreach ($locationsData as $loc) {
            $locations[$loc['name']] = Location::create($loc);
        }

        // 5. Create Suppliers
        $supplierA = Supplier::create([
            'name' => 'PT Besmindo Supply Chain',
            'address' => 'Jl. Industri Selatan No. 45, Cikarang',
            'phone' => '021-89837123',
            'email' => 'sales@besmindo-supply.com',
            'contact_person' => 'Hendra Setiawan',
            'description' => 'Pemasok utama sparepart permesinan dan tools industri',
        ]);

        $supplierB = Supplier::create([
            'name' => 'PT Utama Tools Indonesia',
            'address' => 'Kawasan Industri Jababeka V, Bekasi',
            'phone' => '021-89320112',
            'email' => 'info@utamatools.co.id',
            'contact_person' => 'Rina Wijaya',
            'description' => 'Distributor resmi perlengkapan K3 dan perlengkapan electrical',
        ]);

        // 6. Create Initial Items
        $itemsData = [
            [
                'code' => 'BRG-00001',
                'barcode' => 'BRG-00001',
                'name' => 'Ball Bearing 6205-2RS',
                'category_id' => $categories['Sparepart']->id,
                'description' => 'Bearing presisi tinggi untuk pompa dan motor listrik',
                'unit_id' => $units['PCS']->id,
                'stock' => 25,
                'minimum_stock' => 5,
                'location_id' => $locations['Rak A-01']->id,
                'supplier_id' => $supplierA->id,
                'status' => 'active',
            ],
            [
                'code' => 'BRG-00002',
                'barcode' => 'BRG-00002',
                'name' => 'High Tensile Bolt M12x50',
                'category_id' => $categories['Material']->id,
                'description' => 'Baut baja hitam grade 8.8 anti karat',
                'unit_id' => $units['PCS']->id,
                'stock' => 3,
                'minimum_stock' => 10,
                'location_id' => $locations['Rak A-02']->id,
                'supplier_id' => $supplierA->id,
                'status' => 'active',
            ],
            [
                'code' => 'BRG-00003',
                'barcode' => 'BRG-00003',
                'name' => 'Safety Helmet Red Heavy Duty',
                'category_id' => $categories['Safety']->id,
                'description' => 'Helm keselamatan proyek standar SNI & OSHA',
                'unit_id' => $units['Unit']->id,
                'stock' => 5,
                'minimum_stock' => 5,
                'location_id' => $locations['Rak B-01']->id,
                'supplier_id' => $supplierB->id,
                'status' => 'active',
            ],
            [
                'code' => 'BRG-00004',
                'barcode' => 'BRG-00004',
                'name' => 'Industrial Cable NYY 3x2.5mm',
                'category_id' => $categories['Electrical']->id,
                'description' => 'Kabel daya tembaga berisolasi ganda PVC',
                'unit_id' => $units['Roll']->id,
                'stock' => 0,
                'minimum_stock' => 2,
                'location_id' => $locations['Rak B-02']->id,
                'supplier_id' => $supplierB->id,
                'status' => 'active',
            ],
            [
                'code' => 'BRG-00005',
                'barcode' => 'BRG-00005',
                'name' => 'Digital Multimeter Fluke 179',
                'category_id' => $categories['Tools']->id,
                'description' => 'Alat ukur kelistrikan instrumen akurasi tinggi',
                'unit_id' => $units['Unit']->id,
                'stock' => 12,
                'minimum_stock' => 3,
                'location_id' => $locations['Rak C-01']->id,
                'supplier_id' => $supplierB->id,
                'status' => 'active',
            ],
        ];

        foreach ($itemsData as $itemData) {
            $item = Item::create($itemData);

            // Create initial stock transaction entry
            StockTransaction::create([
                'item_id' => $item->id,
                'user_id' => $admin->id,
                'transaction_type' => 'IN',
                'quantity' => $item->stock,
                'stock_before' => 0,
                'stock_after' => $item->stock,
                'reference_number' => 'INIT-' . $item->code,
                'description' => 'Stok awal barang',
                'transaction_date' => Carbon::now()->subDays(10),
            ]);
        }

        // 7. Seed Transactions for Chart/Activity Demo
        $bearing = Item::where('code', 'BRG-00001')->first();
        if ($bearing) {
            IncomingItem::create([
                'transaction_number' => 'TRX-IN-' . time() . '-01',
                'item_id' => $bearing->id,
                'supplier_id' => $supplierA->id,
                'quantity' => 20,
                'description' => 'Pembelian tambahan stok berkala',
                'user_id' => $admin->id,
                'transaction_date' => Carbon::now()->subDays(2),
            ]);

            OutgoingItem::create([
                'transaction_number' => 'TRX-OUT-' . time() . '-01',
                'item_id' => $bearing->id,
                'quantity' => 5,
                'destination' => 'Workshop Utama',
                'requester' => 'Budi Teknik',
                'description' => 'Perbaikan rutin conveyor belt line 2',
                'user_id' => $staff->id,
                'transaction_date' => Carbon::now()->subHours(5),
            ]);
        }

        // 8. Create Initial Activity Logs
        ActivityLog::create([
            'user_id' => $admin->id,
            'activity_type' => 'Master Data',
            'description' => 'Admin menginisialisasi sistem dan data master barang.',
        ]);

        ActivityLog::create([
            'user_id' => $staff->id,
            'activity_type' => 'Barang Keluar',
            'description' => 'Budi mencatat barang keluar: Ball Bearing 6205-2RS (5 PCS) ke Workshop Utama',
        ]);

        ActivityLog::create([
            'user_id' => $supervisor->id,
            'activity_type' => 'Scan Barcode',
            'description' => 'Andi melakukan scan QR code barang BRG-00001 (Ball Bearing 6205-2RS)',
        ]);
    }
}
