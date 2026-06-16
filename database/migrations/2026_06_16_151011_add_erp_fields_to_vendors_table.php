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
        Schema::table('vendors', function (Blueprint $table) {

            $table->string('pan_number')
                ->nullable()
                ->after('gst_number');

            $table->integer('payment_days')
                ->default(0)
                ->comment('0=Immediate,30=Net30')
                ->after('credit_limit');

            $table->string('bank_name')
                ->nullable()
                ->after('payment_days');

            $table->string('bank_account_no')
                ->nullable()
                ->after('bank_name');

            $table->string('ifsc_code')
                ->nullable()
                ->after('bank_account_no');

            $table->enum('opening_balance_type', ['DR', 'CR'])
                ->default('CR')
                ->after('opening_balance');

            $table->text('remarks')
                ->nullable()
                ->after('ifsc_code');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('vendors', function (Blueprint $table) {

            $table->dropColumn([
                'pan_number',
                'payment_days',
                'bank_name',
                'bank_account_no',
                'ifsc_code',
                'opening_balance_type',
                'remarks'
            ]);

        });
    }
};