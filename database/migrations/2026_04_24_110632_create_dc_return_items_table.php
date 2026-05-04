<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
{
    Schema::create('dc_return_items', function (Blueprint $table) {

        $table->id();

        $table->foreignId('dc_return_id')->constrained('dc_returns')
                ->onDelete('cascade');
        $table->foreignId('dc_item_id')->nullable()->constrained('delivery_challan_items')
            ->onDelete('cascade');
        $table->foreignId('product_id')
            ->constrained('products');
        $table->decimal('return_qty', 10, 2);
        $table->decimal('previous_returned_qty', 10, 2)->default(0);
        $table->enum('condition', ['good', 'damaged', 'scrap']);
        $table->string('reason')->nullable();
        $table->timestamps();
    });
}

    public function down(): void
    {
        Schema::table('dc_return_items', function (Blueprint $table) {

            $table->dropConstrainedForeignId('dc_item_id');
            $table->dropColumn('previous_returned_qty');
            $table->dropColumn('reason');

        });
    }
};