<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('vendor_ledgers', function (Blueprint $table) {
            $table->id();

            $table->foreignId('vendor_id')
                ->constrained()
                ->onDelete('cascade');

            // ERP CORE FIELD
            $table->enum('entry_type', ['DEBIT', 'CREDIT']);
            // DEBIT = Purchase (increase payable)
            // CREDIT = Payment (decrease payable)

            $table->decimal('amount', 15, 2);

            $table->string('reference_type')->nullable();
            // PURCHASE / PAYMENT / OPENING / ADJUSTMENT

            $table->unsignedBigInteger('reference_id')->nullable();

            // ERP IMPORTANT FIELD (running balance)
            $table->decimal('balance_after', 15, 2)->default(0);

            $table->text('note')->nullable();

            $table->unsignedBigInteger('created_by')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('vendor_ledgers');
    }
};