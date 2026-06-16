<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
   public function up(): void
{
    Schema::table('customers', function (Blueprint $table) {

        $table->string('customer_code')
              ->unique()
              ->nullable()
              ->after('id');

        $table->decimal('credit_limit', 12, 2)
              ->default(0)
              ->after('country');

        $table->decimal('opening_balance', 12, 2)
              ->default(0)
              ->after('credit_limit');

        $table->unsignedBigInteger('created_by')
              ->nullable()
              ->after('opening_balance');

        $table->unsignedBigInteger('updated_by')
              ->nullable()
              ->after('created_by');

        $table->softDeletes();
    });
}
public function down(): void
{
    Schema::table('customers', function (Blueprint $table) {

        $table->dropSoftDeletes();

        $table->dropColumn([
            'created_by',
            'updated_by'
        ]);
    });
}
};