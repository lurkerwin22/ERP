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
        Schema::create('ventes', function (Blueprint $table) {
            $table->id();
            
            // Client relationship (nullable if walk-in customer)
            $table->foreignId('client_id')->nullable()->constrained('clients')->nullOnDelete();
            
            $table->timestamp('date_vente')->useCurrent();
            $table->decimal('total', 10, 2)->default(0.00);
            
            // Status for sale management (completed, cancelled)
            $table->enum('statut', ['completee', 'annulee'])->default('completee');
            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ventes');
    }
};