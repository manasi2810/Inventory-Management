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
    Schema::table('stock_ins', function (Blueprint $table) {
        $table->unsignedBigInteger('purchase_id')->nullable()->after('product_id');
        $table->string('po_no')->nullable()->after('purchase_id');
    });
}

public function down(): void
{
    Schema::table('stock_ins', function (Blueprint $table) {

        if (Schema::hasColumn('stock_ins', 'purchase_id')) {
            $table->dropColumn('purchase_id');
        }

        if (Schema::hasColumn('stock_ins', 'po_no')) {
            $table->dropColumn('po_no');
        }

    });
}
};
