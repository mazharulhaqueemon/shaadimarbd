<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up()
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->integer('profile_completion')->default(0);
        $table->integer('last_completed_step')->default(0);
    });
}

public function down()
{
    Schema::table('profiles', function (Blueprint $table) {
        $table->dropColumn(['profile_completion', 'last_completed_step']);
    });
}

};
