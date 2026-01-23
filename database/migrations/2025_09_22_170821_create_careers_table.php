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
        Schema::create('careers', function (Blueprint $table) {
             $table->id();
    $table->foreignId('profile_id')->constrained()->onDelete('cascade');
    $table->string('profession', 150);
    $table->string('job_title', 150);
    $table->string('company', 150)->nullable();
    $table->decimal('annual_income', 12, 2);
    $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('careers');
    }
};
