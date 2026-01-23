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
        Schema::create('payments', function (Blueprint $table) {
            $table->id();

            // Relations
            $table->foreignId('user_id')->constrained()->onDelete('cascade');
            $table->foreignId('plan_id')->constrained()->onDelete('cascade');

            // Payment details
            $table->string('payment_method'); // Bkash / Rocket / Nagad / Bank
            $table->string('transaction_id')->unique();
            $table->string('sender_name')->nullable();
            $table->string('sender_phone')->nullable();
            $table->string('screenshot_path')->nullable(); // optional screenshot

            // Status: pending / approved / rejected
            $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('payments');
    }
};
