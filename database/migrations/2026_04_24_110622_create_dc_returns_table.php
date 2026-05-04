<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('dc_returns', function (Blueprint $table) {

            $table->id();
 
            $table->foreignId('delivery_challan_id')->constrained('delivery_challans')
                    ->onDelete('cascade');
            $table->foreignId('customer_id')->constrained('customers');
            $table->string('return_no')->unique();
            $table->date('return_date');
            $table->text('notes')->nullable();
            $table->enum('status', ['partial', 'full'])->default('partial');
            $table->foreignId('created_by')->nullable();
            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('dc_returns');
    }
};