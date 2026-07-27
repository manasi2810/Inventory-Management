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
       Schema::create('invoice_items', function (Blueprint $table) {

    $table->id();

    $table->foreignId('invoice_id')->constrained()->cascadeOnDelete();

    $table->foreignId('dispatch_item_id')->constrained()->cascadeOnDelete();

    $table->foreignId('product_id')->constrained()->cascadeOnDelete();

    $table->decimal('quantity',12,2);

    $table->decimal('rate',12,2);

    $table->decimal('gst_percent',5,2);

    $table->decimal('gst_amount',12,2);

    $table->decimal('amount',12,2);

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoice_items');
    }
};
