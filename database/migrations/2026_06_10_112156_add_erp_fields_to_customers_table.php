<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            if (!Schema::hasColumn('customers', 'customer_code')) {
                $table->string('customer_code')->unique()->after('id');
            }

            if (!Schema::hasColumn('customers', 'credit_limit')) {
                $table->decimal('credit_limit', 12, 2)->default(0)->after('country');
            }

            if (!Schema::hasColumn('customers', 'opening_balance')) {
                $table->decimal('opening_balance', 12, 2)->default(0)->after('credit_limit');
            }

            if (!Schema::hasColumn('customers', 'created_by')) {
                $table->unsignedBigInteger('created_by')->nullable()->after('opening_balance');
            }

            if (!Schema::hasColumn('customers', 'updated_by')) {
                $table->unsignedBigInteger('updated_by')->nullable()->after('created_by');
            }

            if (!Schema::hasColumn('customers', 'deleted_at')) {
                $table->softDeletes();
            }
        });
    }

    public function down(): void
    {
        Schema::table('customers', function (Blueprint $table) {

            if (Schema::hasColumn('customers', 'deleted_at')) {
                $table->dropSoftDeletes();
            }

            $table->dropColumn([
                'created_by',
                'updated_by'
            ]);
        });
    }
};