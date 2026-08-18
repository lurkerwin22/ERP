<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('ligne_ventes', function (Blueprint $table) {
            $table->id();
            
            $table->foreignId('vente_id')->constrained('ventes')->cascadeOnDelete();
            
            // Set to NULL when product is deleted instead of deleting the sale line
            $table->foreignId('product_id')->nullable()->constrained('products')->nullOnDelete();
            
            // Snapshot columns to preserve historical accuracy
            $table->string('nom_produit');
            $table->integer('quantite');
            $table->decimal('prix_unitaire', 10, 2); 
            $table->decimal('sous_total', 10, 2);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('ligne_ventes');
    }
};