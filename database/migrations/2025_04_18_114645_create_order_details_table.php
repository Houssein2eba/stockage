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
        Schema::create('order_details', function (Blueprint $table) {
         
            $table->uuid('id')->primary();
            $table->integer('quantity');
            $table->decimal('unit_price', 10, 2); // snapshot of price at order time
            $table->decimal('total_price', 10, 2); // could be unit_price * quantity (for convenience)
            $table->foreignUuid('product_id')->constrained('products')->cascadeOnDelete();
            $table->foreignUuid('sale_register_id')->constrained('sale_registers')->cascadeOnDelete();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('order_details');
    }
};
