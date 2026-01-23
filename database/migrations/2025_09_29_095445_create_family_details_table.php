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
        Schema::create('family_details', function (Blueprint $table) {
             $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');

            // Parents
            $table->string('father_name', 150)->nullable();
            $table->string('father_occupation', 150)->nullable();
            $table->string('mother_name', 150)->nullable();
            $table->string('mother_occupation', 150)->nullable();

            // Siblings breakdown
            $table->integer('brothers_unmarried')->default(0);
            $table->integer('brothers_married')->default(0);
            $table->integer('sisters_unmarried')->default(0);
            $table->integer('sisters_married')->default(0);
            $table->string('family_details', 555)->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('family_details');
    }
};
