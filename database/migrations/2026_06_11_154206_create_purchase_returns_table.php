<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
        {
            Schema::create('purchase_returns', function (Blueprint $table) {
                $table->id(); 
                $table->unsignedBigInteger('purchase_id');
                $table->unsignedBigInteger('vendor_id'); 
                $table->date('return_date');
                $table->decimal('total_amount', 12, 2)->default(0); 
                $table->text('note')->nullable(); 
                $table->timestamps(); 
                $table->foreign('purchase_id')->references('id')->on('purchases')->onDelete('cascade');
                $table->foreign('vendor_id')->references('id')->on('vendors')->onDelete('cascade');
            });
        }

    public function down(): void
        {
            Schema::dropIfExists('purchase_returns');
        }
};