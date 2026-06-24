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
                Schema::table('purchase_items', function (Blueprint $table) {
                    $table->decimal('qty_received', 10, 2)->default(0);
                    $table->decimal('qty_returned', 10, 2)->default(0);
                });
            }
            
            public function down(): void
            {
                Schema::table('purchase_items', function (Blueprint $table) {
                    $table->dropColumn(['qty_received', 'qty_returned']);
                });
            }
    };
