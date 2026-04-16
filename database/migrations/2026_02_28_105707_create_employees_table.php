<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('employees', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');  
            $table->string('contact_no')->nullable();
            $table->text('address')->nullable();  
            $table->string('department')->nullable();
            $table->string('designation')->nullable();
            $table->date('date_of_join')->nullable();
            $table->decimal('salary', 12, 2)->nullable(); 
            $table->string('profile_photo')->nullable();
            $table->string('resume')->nullable();
            $table->json('certificates')->nullable();
            $table->string('id_proof')->nullable();

            $table->timestamps();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('employees');
    }
};