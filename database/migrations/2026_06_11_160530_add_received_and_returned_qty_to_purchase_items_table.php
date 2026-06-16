<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {

            $table->integer('received_qty')->default(0)->after('qty');
            $table->integer('returned_qty')->default(0)->after('received_qty');

        });
    }

    public function down(): void
    {
        Schema::table('purchase_items', function (Blueprint $table) {

            $table->dropColumn('received_qty');
            $table->dropColumn('returned_qty');

        });
    }
};