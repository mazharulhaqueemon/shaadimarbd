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
        Schema::create('lifestyles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->enum('diet', ['vegetarian', 'non_vegetarian', 'vegan', 'halal', 'other'])->nullable();
            $table->enum('smoking', ['yes', 'no', 'occasionally'])->nullable();
            $table->enum('drinking', ['yes', 'no', 'occasionally'])->nullable();
            $table->text('hobbies')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('lifestyles');
    }
};
