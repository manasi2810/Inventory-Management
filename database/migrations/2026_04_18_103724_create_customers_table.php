<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations
     */
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            // Primary Key
            $table->id();

            // Basic Details
            $table->string('name'); 
            $table->string('company_name')->nullable();

            // Contact Details
            $table->string('mobile')->nullable();
            $table->string('alternate_mobile')->nullable();
            $table->string('email')->nullable();

            // Address Details
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();

            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->default('India');

            // Tax Details
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();

            // Business Type
            $table->enum('customer_type', ['individual', 'business'])
                  ->default('business');

            // Status
            $table->boolean('status')->default(1);  

            // Extra Notes
            $table->text('notes')->nullable();

            // Timestamps
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations
     */
    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};