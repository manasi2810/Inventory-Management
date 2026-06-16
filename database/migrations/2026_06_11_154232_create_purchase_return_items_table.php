<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('purchase_return_items', function (Blueprint $table) {
            $table->id();

            $table->unsignedBigInteger('purchase_return_id');
            $table->unsignedBigInteger('product_id');

            $table->integer('qty');
            $table->decimal('price', 12, 2);
            $table->decimal('subtotal', 12, 2);

            $table->timestamps();

            $table->foreign('purchase_return_id')
                ->references('id')->on('purchase_returns')
                ->onDelete('cascade');

            $table->foreign('product_id')
                ->references('id')->on('products')
                ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('purchase_return_items');
    }
};