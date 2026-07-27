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
      Schema::create('invoices', function (Blueprint $table) {
    $table->id();

    $table->string('invoice_no')->unique();

    $table->foreignId('dispatch_id')->constrained()->cascadeOnDelete();

    $table->foreignId('delivery_challan_id')->constrained()->cascadeOnDelete();

    $table->foreignId('customer_id')->constrained()->cascadeOnDelete();

    $table->date('invoice_date');

    $table->decimal('sub_total', 12, 2)->default(0);

    $table->decimal('gst_amount', 12, 2)->default(0);

    $table->decimal('discount', 12, 2)->default(0);

    $table->decimal('transport_charge', 12, 2)->default(0);

    $table->decimal('round_off', 12, 2)->default(0);

    $table->decimal('total_amount', 12, 2)->default(0);

    $table->enum('payment_status', [
        'Pending',
        'Partial',
        'Paid'
    ])->default('Pending');

    $table->string('invoice_pdf')->nullable();

    $table->text('remarks')->nullable();

    $table->foreignId('created_by')->nullable()->constrained('users');

    $table->foreignId('updated_by')->nullable()->constrained('users');

    $table->timestamps();
});
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('invoices');
    }
};
