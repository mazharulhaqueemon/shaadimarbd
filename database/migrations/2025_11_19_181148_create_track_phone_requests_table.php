<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('track_phone_requests', function (Blueprint $table) {
            $table->id();

            // viewer (the user who is viewing phone numbers)
            $table->unsignedBigInteger('viewer_user_id')->unique();

            // JSON array of viewed profile ids (so we don't double-count)
            $table->json('viewed_profile_ids')->nullable();

            // count of distinct phone views (kept for quick queries)
            $table->unsignedInteger('count')->default(0);

            $table->timestamps();

            $table->foreign('viewer_user_id')
                  ->references('id')
                  ->on('users')
                  ->onDelete('cascade');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('track_phone_requests');
    }
};
