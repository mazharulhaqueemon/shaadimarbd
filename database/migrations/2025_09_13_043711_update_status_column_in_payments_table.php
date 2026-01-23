<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Change status column to ENUM with default 'pending'
            $table->enum('status', ['pending', 'approved', 'rejected'])
                  ->default('pending')
                  ->change();
        });
    }

    public function down(): void
    {
        Schema::table('payments', function (Blueprint $table) {
            // Revert back to plain string (if needed)
            $table->string('status')->nullable()->change();
        });
    }
};
