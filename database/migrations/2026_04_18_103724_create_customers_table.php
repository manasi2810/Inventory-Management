<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('customers', function (Blueprint $table) {

            $table->id();

            // Basic Info
            $table->string('name');
            $table->string('company_name')->nullable();
            $table->string('customer_code')->unique();

            // Contact
            $table->string('mobile')->nullable();
            $table->string('alternate_mobile')->nullable();
            $table->string('email')->nullable();

            // Address
            $table->text('billing_address')->nullable();
            $table->text('shipping_address')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('pincode')->nullable();
            $table->string('country')->default('India');

            // Tax Details
            $table->string('gst_number')->nullable();
            $table->string('pan_number')->nullable();

            // Business Info
            $table->enum('customer_type', ['individual', 'business'])->default('business');

            // Finance
            $table->decimal('credit_limit', 12, 2)->default(0);
            $table->decimal('opening_balance', 12, 2)->default(0);

            // Status (IMPORTANT: use boolean)
            $table->boolean('status')->default(1);

            $table->text('notes')->nullable();

            // Audit
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('customers');
    }
};