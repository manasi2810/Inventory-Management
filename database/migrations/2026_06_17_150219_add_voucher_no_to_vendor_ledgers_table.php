<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
   public function up()
    {
        Schema::table('vendor_ledgers', function (Blueprint $table) {
            $table->string('voucher_no')->nullable()->after('note');
        });
    }
    
    public function down()
    {
        Schema::table('vendor_ledgers', function (Blueprint $table) {
            $table->dropColumn('voucher_no');
        });
    }
    };
