<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dispatch_items', function (Blueprint $table) {

            $table->id();

            $table->foreignId('dispatch_id')
                  ->constrained('dispatches')
                  ->cascadeOnDelete();

            $table->foreignId('delivery_challan_item_id')
                  ->nullable()
                  ->constrained('delivery_challan_items')
                  ->nullOnDelete();

            $table->foreignId('product_id')
                  ->constrained()
                  ->cascadeOnDelete();

            $table->decimal('quantity',12,2);

            $table->decimal('rate',12,2)->default(0);

            $table->decimal('gst_percent',5,2)->default(0);

            $table->decimal('gst_amount',12,2)->default(0);

            $table->decimal('amount',12,2)->default(0);

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dispatch_items');
    }
};