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
        Schema::table('customer_ledgers', function (Blueprint $table) {

            // Remove old columns
            $table->dropColumn([
                'amount',
                'note',
            ]);

            // Add new ERP columns
            $table->string('reference_no')->nullable()->after('reference_id');

            $table->decimal('debit', 15, 2)
                ->default(0)
                ->after('entry_type');

            $table->decimal('credit', 15, 2)
                ->default(0)
                ->after('debit');

            $table->text('remarks')
                ->nullable()
                ->after('balance_after');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('customer_ledgers', function (Blueprint $table) {

            $table->dropColumn([
                'reference_no',
                'debit',
                'credit',
                'remarks',
            ]);

            $table->decimal('amount', 15, 2)
                ->default(0)
                ->after('entry_type');

            $table->text('note')
                ->nullable()
                ->after('balance_after');
        });
    }
};