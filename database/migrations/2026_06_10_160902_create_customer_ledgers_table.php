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
            Schema::create('customer_ledgers', function (Blueprint $table) {
            $table->id(); 
            $table->foreignId('customer_id')->constrained()->onDelete('cascade'); 
            $table->enum('entry_type', ['DEBIT', 'CREDIT']); 
            $table->decimal('amount', 15, 2); 
            $table->string('reference_type')->nullable(); 
            $table->unsignedBigInteger('reference_id')->nullable(); 
            $table->decimal('balance_after', 15, 2)->default(0); 
            $table->text('note')->nullable(); 
            $table->unsignedBigInteger('created_by')->nullable(); 
            $table->timestamps();
        });
        }

    /**
     * Reverse the migrations.
     */
    public function down(): void
        {
            Schema::dropIfExists('customer_ledgers');
        }
};
