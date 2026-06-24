<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
        {
            Schema::table('vendors', function (Blueprint $table) {

                // ONLY ADD NEW COLUMNS (NO DUPLICATES)

                if (!Schema::hasColumn('vendors', 'vendor_code')) {
                    $table->string('vendor_code')->unique();
                }

                if (!Schema::hasColumn('vendors', 'credit_limit')) {
                    $table->decimal('credit_limit', 12, 2)->default(0);
                }

                if (!Schema::hasColumn('vendors', 'opening_balance')) {
                    $table->decimal('opening_balance', 12, 2)->default(0);
                }

                if (!Schema::hasColumn('vendors', 'created_by')) {
                    $table->unsignedBigInteger('created_by')->nullable();
                }

                if (!Schema::hasColumn('vendors', 'updated_by')) {
                    $table->unsignedBigInteger('updated_by')->nullable();
                }

                if (!Schema::hasColumn('vendors', 'deleted_at')) {
                    $table->softDeletes();
                }
            });
        }

    public function down(): void
        {
            Schema::table('vendors', function (Blueprint $table) {

                $table->dropSoftDeletes();

                $table->dropColumn([
                    'vendor_code',
                    'credit_limit',
                    'opening_balance',
                    'status',
                    'created_by',
                    'updated_by'
                ]);
            });
        }
};