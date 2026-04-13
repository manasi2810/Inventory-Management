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
        Schema::create('products', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->unsignedBigInteger('category_id');
    $table->string('sku')->nullable()->unique();
    $table->text('description')->nullable();
    $table->integer('opening_stock')->default(0);
    $table->string('pack_size')->nullable();
    $table->integer('moq')->nullable();
    $table->string('uom');
    $table->decimal('price', 10, 2)->default(0);
    $table->decimal('cost_price', 10, 2)->nullable();
    $table->boolean('feature_product')->default(false);
    $table->integer('sequence')->default(0);
    $table->enum('status', ['active','inactive'])->default('active');
    $table->string('page_title')->nullable();
    $table->string('alt_text')->nullable();
    $table->text('meta_keywords')->nullable();
    $table->timestamps();

    $table->foreign('category_id')->references('id')->on('categories')->onDelete('cascade');
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('products');
    }
};
