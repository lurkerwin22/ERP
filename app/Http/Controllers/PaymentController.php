<?php

namespace App\Http\Controllers;

use App\Models\Sale;
use App\Models\Payment;
use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function store(Request $request, Sale $sale)
    {
        // 1. Block payments on cancelled sales
        if ($sale->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot add payments to a cancelled sale.');
        }

        // 2. Check remaining balance using updated model accessor
        $remaining = round($sale->remaining_balance,2);

        if ($remaining <= 0) {
            return redirect()->back()->with('error', 'This sale is already fully paid.');
        }

        // 3. Server-side validation with dynamic remaining balance max limit
        $validated = $request->validate([
            'amount' => ['required', 'numeric', 'min:0.01', "max:{$remaining}"],
            'payment_method' => ['required', 'string', 'in:cash,card,bank_transfer,check'],
            'paid_at' => ['required', 'date'],
        ], [
            'amount.max' => "The payment amount cannot exceed the remaining balance of " . number_format($remaining, 2) . " TND.",
        ]);

        // 4. Store the payment record
        $sale->payments()->create([
            'amount' => $validated['amount'],
            'payment_method' => $validated['payment_method'],
            'paid_at' => $validated['paid_at'],
        ]);

        return redirect()->back()->with('success', 'Payment added successfully!');
    }

    public function destroy(Payment $payment)
    {
        // Block deleting payments if sale is cancelled
        if ($payment->sale->status === 'cancelled') {
            return redirect()->back()->with('error', 'Cannot alter payment history of a cancelled sale.');
        }

        $payment->delete();

        return redirect()->back()->with('success', 'Payment reversed successfully.');
    }
}