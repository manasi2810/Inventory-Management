<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('inventory_logs', function (Blueprint $table) {
    $table->id();

    $table->unsignedBigInteger('purchase_id')->nullable();
    $table->unsignedBigInteger('product_id')->nullable();

    $table->string('action_type');

    $table->integer('qty')->nullable();
    $table->decimal('amount', 10, 2)->nullable();

    $table->string('status_from')->nullable();
    $table->string('status_to')->nullable();

    $table->text('remarks')->nullable();

    $table->unsignedBigInteger('created_by')->nullable();

    $table->timestamps();

    // indexes
    $table->index('purchase_id');
    $table->index('product_id');
    $table->index('action_type');
}); 
    }

    public function down(): void
    {
        Schema::dropIfExists('inventory_logs');
    }
};