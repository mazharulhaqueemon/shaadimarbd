<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::create('chat_list', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');       // Owner of this chat row
            $table->string('chat_id');                   // Unique chat ID: sorted sender_receiver
            $table->unsignedBigInteger('other_user_id'); // The other participant
            $table->text('last_message')->nullable();
            $table->timestamp('last_message_at')->nullable();
            $table->integer('unread_count')->default(0);
            $table->timestamps();

            $table->unique(['user_id', 'chat_id']);
            $table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
            $table->foreign('other_user_id')->references('id')->on('users')->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('chat_list');
    }
};
