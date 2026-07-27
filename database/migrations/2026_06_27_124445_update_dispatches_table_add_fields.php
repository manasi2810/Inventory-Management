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
            Schema::table('dispatches', function (Blueprint $table) {
        
                $table->foreignId('customer_id')
                    ->after('delivery_challan_id')
                    ->constrained()
                    ->cascadeOnDelete();
        
                $table->string('status')->default('completed')->after('dispatch_date');
        
                $table->text('remarks')->nullable()->after('status');
        
            });
        }
    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
