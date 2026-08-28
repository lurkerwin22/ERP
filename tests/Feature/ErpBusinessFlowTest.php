<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Customer;
use App\Models\Payment;
use App\Models\Product;
use App\Models\Purchase;
use App\Models\PurchaseItem;
use App\Models\Quote;
use App\Models\Sale;
use App\Models\SaleItem;
use App\Models\StockMovement;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Facades\Schema;
use Tests\TestCase;

class ErpBusinessFlowTest extends TestCase
{
    use RefreshDatabase;

    protected User $user;
    protected Category $category;
    protected Customer $customer;
    protected Supplier $supplier;
    protected Product $product;

    protected function setUp(): void
    {
        parent::setUp();

        $this->user = User::factory()->create([
            'role' => 'superadmin',
            'status' => 'active',
        ]);

        $this->category = Category::factory()->create([
            'name' => 'Test Category',
        ]);

        $this->customer = Customer::factory()->create([
            'name' => 'Test Customer',
        ]);

        $this->supplier = Supplier::create([
            'name' => 'Test Supplier',
            'email' => 'supplier@test.com',
            'phone' => '12345678',
        ]);

        $this->product = Product::factory()->create([
            'name' => 'Test Product',
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
            'price' => 20.00,
            'purchase_price' => 10.00,
            'stock' => 20,
            'alert_threshold' => 5,
        ]);

        $this->actingAs($this->user);
    }

    /*
    |--------------------------------------------------------------------------
    | PRODUCTS
    |--------------------------------------------------------------------------
    */

    public function test_product_can_be_created_and_updated(): void
    {
        $response = $this->post(route('products.store'), [
            'name' => 'New Product',
            'description' => 'Test description',
            'price' => 30,
            'purchase_price' => 15,
            'stock' => 10,
            'alert_threshold' => 5,
            'category_id' => $this->category->id,
            'supplier_id' => $this->supplier->id,
        ]);

        $response->assertRedirect();

        $product = Product::where('name', 'New Product')->first();

        $this->assertNotNull($product);
        $this->assertEquals(10, $product->stock);

        $response = $this->patch(
            route('products.update', $product),
            [
                'name' => 'Updated Product',
                'description' => 'Updated description',
                'price' => 35,
                'purchase_price' => 15,
                'stock' => 10,
                'alert_threshold' => 5,
                'category_id' => $this->category->id,
                'supplier_id' => $this->supplier->id,
            ]
        );

        $response->assertRedirect();

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product',
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | STOCK ADJUSTMENTS
    |--------------------------------------------------------------------------
    */

    public function test_adding_stock_updates_product_and_creates_movement(): void
    {
        $response = $this->post(
            route('stock.add', $this->product),
            [
                'quantity' => 10,
                'reason' => 'Test restock',
            ]
        );

        $response->assertRedirect();

        $this->assertEquals(30, $this->product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => 'Test restock',
        ]);
    }

    public function test_removing_stock_updates_product_and_creates_movement(): void
    {
        $response = $this->post(
            route('stock.remove', $this->product),
            [
                'quantity' => 5,
                'reason' => 'Test reduction',
            ]
        );

        $response->assertRedirect();

        $this->assertEquals(15, $this->product->fresh()->stock);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 5,
            'reason' => 'Test reduction',
        ]);
    }

    public function test_cannot_remove_more_stock_than_available(): void
    {
        $response = $this->post(
            route('stock.remove', $this->product),
            [
                'quantity' => 100,
                'reason' => 'Invalid removal',
            ]
        );

        $response->assertSessionHasErrors('quantity');

        $this->assertEquals(20, $this->product->fresh()->stock);

        $this->assertDatabaseCount('stock_movements', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | PURCHASES
    |--------------------------------------------------------------------------
    */

    public function test_purchase_increases_stock_and_creates_stock_movement(): void
    {
        $response = $this->post(route('purchases.store'), [
            'supplier_id' => $this->supplier->id,
            'purchase_date' => now()->format('Y-m-d'),
            'notes' => 'Test purchase',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                    'unit_price' => 12,
                ],
            ],
        ]);

        $response->assertRedirect();

        $product = $this->product->fresh();

        $this->assertEquals(30, $product->stock);
        $this->assertEquals(12.000, (float) $product->purchase_price);

        $purchase = Purchase::latest()->first();

        $this->assertNotNull($purchase);

        $this->assertEquals(120.000, (float) $purchase->total);

        $this->assertDatabaseHas('purchase_items', [
            'purchase_id' => $purchase->id,
            'product_id' => $this->product->id,
            'quantity' => 10,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 10,
            'reason' => "Purchase #{$purchase->id}",
        ]);
    }

    /*
    |--------------------------------------------------------------------------
    | SALES
    |--------------------------------------------------------------------------
    */

    public function test_sale_decreases_stock_and_creates_stock_movement(): void
    {
        $response = $this->post(route('sales.store'), [
            'customer_id' => $this->customer->id,
            'notes' => 'Test sale',
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 3,
                ],
            ],
        ]);

        $response->assertRedirect();

        $product = $this->product->fresh();

        $this->assertEquals(17, $product->stock);

        $sale = Sale::latest()->first();

        $this->assertNotNull($sale);
        $this->assertEquals(60, (float) $sale->total);
        $this->assertEquals('completed', $sale->status);

        $this->assertDatabaseHas('sale_items', [
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'quantity' => 3,
            'unit_price' => 20,
            'subtotal' => 60,
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 3,
            'reason' => "Sale #{$sale->id}",
        ]);
    }

    public function test_sale_cannot_exceed_available_stock(): void
    {
        $response = $this->post(route('sales.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 100,
                ],
            ],
        ]);

        $this->assertTrue(
            $response->isRedirect() || $response->isClientError(),
            'Sale should not be accepted when stock is insufficient.'
        );

        $this->assertEquals(20, $this->product->fresh()->stock);
    }

    public function test_same_product_cannot_be_sold_twice_beyond_available_stock(): void
    {
        $response = $this->post(route('sales.store'), [
            'customer_id' => $this->customer->id,
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 15,
                ],
                [
                    'product_id' => $this->product->id,
                    'quantity' => 10,
                ],
            ],
        ]);

        $this->assertTrue(
            $response->isRedirect() || $response->isClientError(),
            'The combined quantity should not exceed available stock.'
        );

        $this->assertEquals(20, $this->product->fresh()->stock);
    }

    /*
    |--------------------------------------------------------------------------
    | SALE CANCELLATION
    |--------------------------------------------------------------------------
    */

    public function test_cancelling_sale_restores_stock_and_creates_reversal_movement(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'customer_phone' => $this->customer->phone,
            'customer_address' => $this->customer->address,
            'sale_date' => now(),
            'total' => 60,
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'unit_price' => 20,
            'quantity' => 3,
            'subtotal' => 60,
        ]);

        $this->product->decrement('stock', 3);

        StockMovement::create([
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 3,
            'reason' => "Sale #{$sale->id}",
        ]);

        $this->assertEquals(17, $this->product->fresh()->stock);

        $response = $this->patch(
            route('sales.cancel', $sale)
        );

        $response->assertRedirect();

        $this->assertEquals(20, $this->product->fresh()->stock);

        $this->assertDatabaseHas('sales', [
            'id' => $sale->id,
            'status' => 'cancelled',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'in',
            'quantity' => 3,
        ]);
    }

    public function test_cancelled_sale_cannot_be_cancelled_again(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_date' => now(),
            'total' => 20,
            'status' => 'cancelled',
        ]);

        $response = $this->patch(
            route('sales.cancel', $sale)
        );

        $response->assertSessionHas('error');

        $this->assertEquals(
            20,
            $this->product->fresh()->stock
        );
    }

    /*
    |--------------------------------------------------------------------------
    | PAYMENTS
    |--------------------------------------------------------------------------
    */

    public function test_partial_and_full_payment_are_calculated_correctly(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_date' => now(),
            'total' => 500,
            'status' => 'completed',
        ]);

        $this->assertEquals(0, $sale->amount_paid);
        $this->assertEquals(500, $sale->remaining_balance);
        $this->assertEquals('unpaid', $sale->payment_status);

        $response = $this->post(
            route('sales.payments.store', $sale),
            [
                'amount' => 200,
                'payment_method' => 'cash',
                'paid_at' => now()->format('Y-m-d H:i:s'),
            ]
        );

        $response->assertRedirect();

        $sale->refresh();

        $this->assertEquals(200, $sale->amount_paid);
        $this->assertEquals(300, $sale->remaining_balance);
        $this->assertEquals('partial', $sale->payment_status);

        $response = $this->post(
            route('sales.payments.store', $sale),
            [
                'amount' => 300,
                'payment_method' => 'card',
                'paid_at' => now()->format('Y-m-d H:i:s'),
            ]
        );

        $response->assertRedirect();

        $sale->refresh();

        $this->assertEquals(500, $sale->amount_paid);
        $this->assertEquals(0, $sale->remaining_balance);
        $this->assertEquals('paid', $sale->payment_status);
    }

    public function test_payment_cannot_exceed_remaining_balance(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_date' => now(),
            'total' => 100,
            'status' => 'completed',
        ]);

        $response = $this->post(
            route('sales.payments.store', $sale),
            [
                'amount' => 101,
                'payment_method' => 'cash',
                'paid_at' => now()->format('Y-m-d H:i:s'),
            ]
        );

        $response->assertSessionHasErrors('amount');

        $this->assertDatabaseCount('payments', 0);
    }

    public function test_payment_cannot_be_added_to_cancelled_sale(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_date' => now(),
            'total' => 100,
            'status' => 'cancelled',
        ]);

        $response = $this->post(
            route('sales.payments.store', $sale),
            [
                'amount' => 50,
                'payment_method' => 'cash',
                'paid_at' => now()->format('Y-m-d H:i:s'),
            ]
        );

        $response->assertSessionHas('error');

        $this->assertDatabaseCount('payments', 0);
    }

    /*
    |--------------------------------------------------------------------------
    | QUOTES
    |--------------------------------------------------------------------------
    */

    public function test_quote_creation_does_not_change_stock(): void
    {
        $startingStock = $this->product->stock;

        $response = $this->post(route('quotes.store'), [
            'customer_id' => $this->customer->id,
            'date' => now()->format('Y-m-d'),
            'items' => [
                [
                    'product_id' => $this->product->id,
                    'quantity' => 2,
                    'unit_price' => 20,
                ],
            ],
        ]);

        $response->assertRedirect();

        $this->assertEquals(
            $startingStock,
            $this->product->fresh()->stock
        );

        $quote = Quote::latest()->first();

        $this->assertNotNull($quote);
        $this->assertEquals('draft', $quote->status);
        $this->assertEquals(40, (float) $quote->total);
    }

    public function test_quote_can_be_converted_to_sale_and_decreases_stock(): void
    {
        $quote = Quote::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'quote_number' => 'TEST-000001',
            'date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total' => 40,
        ]);

        $quote->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 2,
            'unit_price' => 20,
            'subtotal' => 40,
        ]);

        $response = $this->post(
            route('quotes.convert', $quote)
        );

        $response->assertRedirect();

        $quote->refresh();

        $this->assertEquals('accepted', $quote->status);
        $this->assertNotNull($quote->sale_id);

        $this->assertEquals(18, $this->product->fresh()->stock);

        $this->assertDatabaseHas('sales', [
            'id' => $quote->sale_id,
            'total' => 40,
            'status' => 'completed',
        ]);

        $this->assertDatabaseHas('stock_movements', [
            'product_id' => $this->product->id,
            'type' => 'out',
            'quantity' => 2,
        ]);
    }

    public function test_quote_cannot_be_converted_twice(): void
    {
        $quote = Quote::create([
            'customer_id' => $this->customer->id,
            'user_id' => $this->user->id,
            'quote_number' => 'TEST-000002',
            'date' => now()->format('Y-m-d'),
            'status' => 'draft',
            'total' => 20,
        ]);

        $quote->items()->create([
            'product_id' => $this->product->id,
            'quantity' => 1,
            'unit_price' => 20,
            'subtotal' => 20,
        ]);

        $this->post(route('quotes.convert', $quote));

        $quote->refresh();

        $saleId = $quote->sale_id;
        $stockAfterFirstConversion = $this->product->fresh()->stock;

        $response = $this->post(
            route('quotes.convert', $quote)
        );

        $response->assertSessionHas('error');

        $quote->refresh();

        $this->assertEquals($saleId, $quote->sale_id);
        $this->assertEquals(
            $stockAfterFirstConversion,
            $this->product->fresh()->stock
        );

        $this->assertEquals(
            1,
            Sale::where('id', $saleId)->count()
        );
    }

    /*
    |--------------------------------------------------------------------------
    | CUSTOMER DEBT
    |--------------------------------------------------------------------------
    */

    public function test_customer_debt_is_calculated_correctly(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_date' => now(),
            'total' => 100,
            'status' => 'completed',
        ]);

        Payment::create([
            'sale_id' => $sale->id,
            'amount' => 40,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        $customer = $this->customer->fresh();

        $this->assertEquals(100, $customer->total_purchases);
        $this->assertEquals(40, $customer->total_paid);
        $this->assertEquals(60, $customer->total_outstanding_debt);
    }

    /*
    |--------------------------------------------------------------------------
    | HISTORICAL DATA
    |--------------------------------------------------------------------------
    */

    public function test_sale_item_keeps_product_name_when_product_is_deleted(): void
    {
        $sale = Sale::create([
            'customer_id' => $this->customer->id,
            'customer_name' => $this->customer->name,
            'sale_date' => now(),
            'total' => 20,
            'status' => 'completed',
        ]);

        SaleItem::create([
            'sale_id' => $sale->id,
            'product_id' => $this->product->id,
            'product_name' => $this->product->name,
            'unit_price' => 20,
            'quantity' => 1,
            'subtotal' => 20,
        ]);

        $productName = $this->product->name;

        $this->product->delete();

        $item = SaleItem::where('sale_id', $sale->id)->first();

        $this->assertNotNull($item);
        $this->assertNull($item->product_id);
        $this->assertEquals($productName, $item->product_name);
    }

    /*
    |--------------------------------------------------------------------------
    | AUTHORIZATION
    |--------------------------------------------------------------------------
    */

    public function test_only_superadmin_can_access_user_management(): void
    {
        $this->get(route('users.index'))
            ->assertOk();

        $employee = User::factory()->create([
            'role' => 'employee',
            'status' => 'active',
        ]);

        $this->actingAs($employee);

        $response = $this->get(route('users.index'));

        $response->assertStatus(403);
    }

    /*
    |--------------------------------------------------------------------------
    | BASIC APPLICATION ACCESS
    |--------------------------------------------------------------------------
    */

    public function test_authenticated_user_can_access_dashboard(): void
    {
        $this->get(route('dashboard'))
            ->assertOk();
    }

    public function test_guest_cannot_access_dashboard(): void
    {
        auth()->logout();

        $this->get(route('dashboard'))
            ->assertRedirect(route('login'));
    }
}