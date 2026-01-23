<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration {
    public function up(): void
    {
        // MySQL doesn't support modifying ENUMs directly via schema builder,
        // so we run a raw query here.
        DB::statement("ALTER TABLE `users` MODIFY `account_created_by` ENUM('self','referral','system') NOT NULL DEFAULT 'self'");
    }

    public function down(): void
    {
        // Rollback to the original ENUM without 'system'
        DB::statement("ALTER TABLE `users` MODIFY `account_created_by` ENUM('self','referral') NOT NULL DEFAULT 'self'");
    }
};
