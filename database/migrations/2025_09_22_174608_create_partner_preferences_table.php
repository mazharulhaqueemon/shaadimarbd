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
        Schema::create('partner_preferences', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->integer('preferred_age_min')->nullable();
            $table->integer('preferred_age_max')->nullable();
            $table->integer('preferred_height_min')->nullable();
            $table->integer('preferred_height_max')->nullable();
            $table->string('preferred_religion', 100)->nullable();
            $table->string('preferred_caste', 100)->nullable();
            $table->string('preferred_education', 150)->nullable();
            $table->string('preferred_country', 100)->nullable();
            $table->string('preferred_profession', 100)->nullable();
            $table->text('other_expectations')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('partner_preferences');
    }
};
