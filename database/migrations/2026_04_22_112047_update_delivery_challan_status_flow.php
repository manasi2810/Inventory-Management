<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
{
    // Step 1: Change ENUM first
    Schema::table('delivery_challans', function (Blueprint $table) {

        $table->enum('status', [
            'pending',      // keep old temporarily
            'partial',
            'draft',        // add new
            'approved',
            'dispatched',
            'delivered',
            'cancelled'
        ])->default('draft')->change();

        $table->timestamp('dispatched_at')->nullable()->after('status');
        $table->unsignedBigInteger('approved_by')->nullable()->after('dispatched_at');
        $table->unsignedBigInteger('dispatched_by')->nullable()->after('approved_by');
    });

    // Step 2: Now update old data
    DB::statement("UPDATE delivery_challans SET status = 'draft' WHERE status = 'pending'");

    // Step 3: Optional cleanup (remove old values)
    Schema::table('delivery_challans', function (Blueprint $table) {

        $table->enum('status', [
            'draft',
            'approved',
            'dispatched',
            'delivered',
            'cancelled'
        ])->default('draft')->change();
    });
}
    public function down(): void
    {
        Schema::table('delivery_challans', function (Blueprint $table) {

            // Revert status (old structure)
            $table->enum('status', [
                'pending',
                'partial',
                'delivered',
                'cancelled'
            ])->default('pending')->change();

            // Drop added columns
            $table->dropColumn([
                'dispatched_at',
                'approved_by',
                'dispatched_by'
            ]);
        });
    }
};