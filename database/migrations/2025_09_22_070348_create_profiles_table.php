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
        Schema::create('profiles', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->enum('gender', ['male', 'female', 'other']);
            $table->date('dob')->nullable();
            $table->enum('marital_status', ['never_married', 'divorced', 'widow', 'separated'])->nullable();
            $table->float('height_feet')->nullable();
            $table->integer('weight_kg')->nullable();
            $table->enum('blood_group', ['A+', 'A-', 'B+', 'B-', 'O+', 'O-', 'AB+', 'AB-'])->nullable();
            $table->string('mother_tongue', 100)->nullable();
            $table->string('religion', 100)->nullable();
            $table->string('caste', 100)->nullable();
            $table->string('sub_caste', 100)->nullable();
            $table->text('bio')->nullable();
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('profiles');
    }
};
