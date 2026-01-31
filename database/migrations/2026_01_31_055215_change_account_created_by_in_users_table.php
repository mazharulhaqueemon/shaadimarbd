<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    public function up(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY account_created_by VARCHAR(50)
            NOT NULL
            DEFAULT 'self'
        ");
    }

    public function down(): void
    {
        DB::statement("
            ALTER TABLE users
            MODIFY account_created_by ENUM(
                'self','father','mother','sibling','relative','agent'
            )
            NOT NULL
            DEFAULT 'self'
        ");
    }
};
