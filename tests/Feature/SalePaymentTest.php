<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Sale;
use App\Models\Customer;
use App\Models\Payment;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class SalePaymentTest extends TestCase
{
    use RefreshDatabase;

    public function test_sale_calculates_remaining_amount_and_payment_status_correctly()
    {
        $user = User::factory()->create();
        $customer = Customer::factory()->create();

        $sale = Sale::create([
            'user_id' => $user->id,
            'customer_id' => $customer->id,
            'total' => 500.00,
            'status' => 'completed',
        ]);

        // Assert initially unpaid
        $this->assertEquals(0.00, $sale->amount_paid);
        $this->assertEquals(500.00, $sale->remaining_amount);
        $this->assertEquals('unpaid', $sale->payment_status);

        // Add Partial Payment
        Payment::create([
            'sale_id' => $sale->id,
            'amount' => 200.00,
            'payment_method' => 'cash',
            'paid_at' => now(),
        ]);

        $sale->refresh();

        $this->assertEquals(200.00, $sale->amount_paid);
        $this->assertEquals(300.00, $sale->remaining_amount);
        $this->assertEquals('partial', $sale->payment_status);

        // Add Final Payment
        Payment::create([
            'sale_id' => $sale->id,
            'amount' => 300.00,
            'payment_method' => 'card',
            'paid_at' => now(),
        ]);

        $sale->refresh();

        $this->assertEquals(500.00, $sale->amount_paid);
        $this->assertEquals(0.00, $sale->remaining_amount);
        $this->assertEquals('paid', $sale->payment_status);
    }
}