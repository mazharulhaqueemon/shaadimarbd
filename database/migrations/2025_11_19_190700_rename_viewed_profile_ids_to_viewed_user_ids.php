<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('track_phone_requests', function (Blueprint $table) {
            $table->renameColumn('viewed_profile_ids', 'viewed_user_ids');
        });
    }

    public function down(): void
    {
        Schema::table('track_phone_requests', function (Blueprint $table) {
            $table->renameColumn('viewed_user_ids', 'viewed_profile_ids');
        });
    }
};
