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
    Schema::create('vendors', function (Blueprint $table) {
        $table->id();
        $table->string('name');
        $table->string('email')->nullable();
        $table->string('phone')->nullable();
        $table->text('address')->nullable();
        $table->string('gst_number')->nullable();
        $table->string('company_name')->nullable();
        $table->enum('status', ['active', 'inactive'])->default('active');
        $table->timestamps();
    });
}

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('vendors');
    }
};
