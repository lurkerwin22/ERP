<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('sales', function (Blueprint $table) {
            $table->id();
            
            // Foreign key with nullOnDelete
            $table->foreignId('customer_id')->nullable()->constrained('customers')->nullOnDelete();
            
            // --- CLIENT SNAPSHOT FIELDS ---
            $table->string('customer_name')->nullable();
            $table->string('customer_phone')->nullable();
            $table->string('customer_address')->nullable();

            $table->timestamp('sale_date')->useCurrent();
            $table->decimal('total', 10, 2)->default(0.00);
            $table->enum('status', ['completed', 'cancelled'])->default('completed');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('sales');
    }
};