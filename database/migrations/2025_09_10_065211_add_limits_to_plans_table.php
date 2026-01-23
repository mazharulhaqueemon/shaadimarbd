<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->unsignedInteger('profile_picture_limit')->default(0);
            $table->unsignedInteger('phone_request_limit')->default(0);
            $table->unsignedInteger('chat_duration_days')->default(0);
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['profile_picture_limit', 'phone_request_limit', 'chat_duration_days']);
        });
    }
};
