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
        Schema::create('locations', function (Blueprint $table) {
            $table->id();
            $table->foreignId('profile_id')->constrained()->onDelete('cascade');
            $table->string('present_address', 100)->nullable();
            $table->string('permanent_address', 100)->nullable();
            $table->string('city', 100)->nullable();
            $table->string('address', 255)->nullable();
            $table->string('nationality', 100)->nullable();
            $table->enum('residence_status', ['citizen', 'permanent_resident', 'work_permit', 'student_visa', 'other'])->nullable();
            $table->enum('living_status', ['renting', 'owned', 'with_family', 'other'])->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('locations');
    }
};
