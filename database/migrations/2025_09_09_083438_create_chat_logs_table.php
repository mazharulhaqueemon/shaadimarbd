<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('chat_logs', function (Blueprint $table) {
            $table->id();
            $table->string('chat_id');
            $table->unsignedBigInteger('sender_id');
            $table->unsignedBigInteger('receiver_id');
            $table->text('message');
            $table->enum('status', ['sent', 'delivered', 'seen'])->default('sent');
            $table->timestamps();

            $table->index('chat_id');
            $table->index('sender_id');
            $table->index('receiver_id');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_logs');
    }
};
