<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->string('price')->nullable()->after('plan_name');
            $table->string('duration')->nullable()->after('price');
            $table->boolean('popular')->default(false)->after('duration');
            $table->string('button_text')->nullable()->after('popular');
            $table->text('features')->nullable()->after('button_text'); // store as comma-separated values
        });
    }

    public function down(): void
    {
        Schema::table('plans', function (Blueprint $table) {
            $table->dropColumn(['price', 'duration', 'popular', 'button_text', 'features']);
        });
    }
};
