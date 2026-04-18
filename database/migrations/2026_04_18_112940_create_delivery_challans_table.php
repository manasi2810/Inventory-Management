<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('delivery_challans', function (Blueprint $table) {

            $table->id();

            // Challan Info
            $table->string('challan_no')->unique();
            $table->date('challan_date');

            // Customer
            $table->foreignId('customer_id')
                  ->constrained('customers')
                  ->onDelete('cascade');

            // Transport Details
            $table->string('transport_mode')->nullable();
            $table->string('vehicle_no')->nullable();
            $table->string('lr_no')->nullable();

            // Addresses
            $table->string('dispatch_from')->nullable();
            $table->text('delivery_to')->nullable();

            // Totals
            $table->decimal('total_qty', 10, 2)->default(0);
            $table->decimal('total_amount', 10, 2)->default(0);

            // Status
            $table->enum('status', ['pending', 'partial', 'delivered', 'cancelled'])
                  ->default('pending');

            $table->text('notes')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('delivery_challans');
    }
};