<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->decimal('sub_total', 10, 2)->default(0)->after('total_qty');
        });
    }

    public function down(): void
    {
        Schema::table('delivery_challans', function (Blueprint $table) {
            $table->dropColumn('sub_total');
        });
    }
};