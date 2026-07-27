<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE customer_ledgers
            MODIFY entry_type ENUM(
                'DEBIT',
                'CREDIT',
                'DISPATCH',
                'PAYMENT',
                'SALE',
                'DELIVERY_CHALLAN',
                'RETURN'
            ) NOT NULL
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE customer_ledgers
            MODIFY entry_type ENUM(
                'DEBIT',
                'CREDIT'
            ) NOT NULL
        ");
    }
};