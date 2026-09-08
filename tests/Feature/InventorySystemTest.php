<?php

namespace Tests\Feature;

use Tests\TestCase;
use App\Models\User;
use App\Models\Category;
use App\Models\Unit;
use App\Models\Location;
use App\Models\Supplier;
use App\Models\Item;
use Illuminate\Foundation\Testing\RefreshDatabase;

class InventorySystemTest extends TestCase
{
    use RefreshDatabase;

    protected $admin;
    protected $staff;
    protected $supervisor;
    protected $item;

    protected function setUp(): void
    {
        parent::setUp();

        // 1. Create Roles Users
        $this->admin = User::create([
            'name' => 'Admin Test',
            'username' => 'admin_test',
            'email' => 'admin@test.com',
            'password' => bcrypt('password'),
            'role' => 'admin',
            'status' => 'active',
        ]);

        $this->staff = User::create([
            'name' => 'Staff Test',
            'username' => 'staff_test',
            'email' => 'staff@test.com',
            'password' => bcrypt('password'),
            'role' => 'staff',
            'status' => 'active',
        ]);

        $this->supervisor = User::create([
            'name' => 'Supervisor Test',
            'username' => 'supervisor_test',
            'email' => 'supervisor@test.com',
            'password' => bcrypt('password'),
            'role' => 'supervisor',
            'status' => 'active',
        ]);

        // 2. Create Master Entities
        $cat = Category::create(['name' => 'Sparepart']);
        $unit = Unit::create(['name' => 'PCS']);
        $loc = Location::create(['name' => 'Rak A-01']);
        $sup = Supplier::create(['name' => 'PT Besmindo Supplier']);

        // 3. Create Sample Item
        $this->item = Item::create([
            'code' => 'BRG-99999',
            'barcode' => 'BRG-99999',
            'name' => 'Test Bearing',
            'category_id' => $cat->id,
            'unit_id' => $unit->id,
            'stock' => 20,
            'minimum_stock' => 5,
            'location_id' => $loc->id,
            'supplier_id' => $sup->id,
            'status' => 'active',
        ]);
    }

    public function test_user_login_successful()
    {
        $response = $this->post('/login', [
            'username' => 'admin_test',
            'password' => 'password',
        ]);

        $response->assertRedirect('/dashboard');
        $this->assertAuthenticatedAs($this->admin);
    }

    public function test_user_login_failed_with_invalid_credentials()
    {
        $response = $this->post('/login', [
            'username' => 'admin_test',
            'password' => 'wrong_password',
        ]);

        $response->assertSessionHasErrors('username');
        $this->assertGuest();
    }

    public function test_incoming_item_increases_stock()
    {
        $this->actingAs($this->staff);

        $response = $this->post('/inventory/incoming', [
            'item_id' => $this->item->id,
            'supplier_id' => $this->item->supplier_id,
            'quantity' => 10,
            'transaction_date' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect('/inventory/incoming');
        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'stock' => 30, // 20 + 10 = 30
        ]);
    }

    public function test_outgoing_item_decreases_stock()
    {
        $this->actingAs($this->staff);

        $response = $this->post('/inventory/outgoing', [
            'item_id' => $this->item->id,
            'quantity' => 5,
            'destination' => 'Workshop 1',
            'requester' => 'Montir Budi',
            'transaction_date' => now()->toDateTimeString(),
        ]);

        $response->assertRedirect('/inventory/outgoing');
        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'stock' => 15, // 20 - 5 = 15
        ]);
    }

    public function test_outgoing_item_fails_when_stock_insufficient()
    {
        $this->actingAs($this->staff);

        $response = $this->post('/inventory/outgoing', [
            'item_id' => $this->item->id,
            'quantity' => 50, // Available is only 20
            'destination' => 'Workshop 1',
            'requester' => 'Montir Budi',
            'transaction_date' => now()->toDateTimeString(),
        ]);

        $response->assertSessionHasErrors('quantity');
        $this->assertDatabaseHas('items', [
            'id' => $this->item->id,
            'stock' => 20, // Stock unchanged
        ]);
    }

    public function test_barcode_scan_api_returns_correct_item()
    {
        $this->actingAs($this->staff);

        $response = $this->get('/barcode/scan-api/BRG-99999');
        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'item' => [
                'code' => 'BRG-99999',
                'name' => 'Test Bearing',
            ]
        ]);
    }

    public function test_barcode_scan_api_returns_404_if_not_found()
    {
        $this->actingAs($this->staff);

        $response = $this->get('/barcode/scan-api/BRG-UNKNOWN');
        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Barang tidak ditemukan dalam sistem.',
        ]);
    }

    public function test_staff_cannot_access_user_management()
    {
        $this->actingAs($this->staff);

        $response = $this->get('/users');
        $response->assertStatus(403);
    }
}
