<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('sale_items', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('sale_id')->constrained('sales')->cascadeOnDelete();
            
            // Set to NULL when product is deleted instead of deleting the sale line
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            // Snapshot columns to preserve historical accuracy
            $table->string('product_name');
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); 
            $table->decimal('subtotal', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('sale_items');
    }
};