<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('stock_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('product_id')->constrained()->onDelete('cascade');
 
            $table->enum('movement_type', ['IN', 'OUT']);

           $table->unsignedInteger('qty');
 
            $table->string('reference_type')->nullable();
 
            $table->unsignedBigInteger('reference_id')->nullable();
  
            $table->integer('balance_after')->default(0);

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('stock_ledgers');
    }
};