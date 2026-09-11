<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('current_week', function (Blueprint $table) {
            $table->integer('current_nfl_season')->nullable()->after('current_nfl_week');
            $table->boolean('games_locked')->default(0)->after('current_nfl_season');
            $table->dateTime('locked_datetime')->nullable()->after('games_locked');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('current_week', 'current_nfl_season')) {
            Schema::table('current_week', function (Blueprint $table) {
                $table->dropColumn('current_nfl_season');
            });
        }
        if (Schema::hasColumn('current_week', 'games_locked')) {
            Schema::table('current_week', function (Blueprint $table) {
                $table->dropColumn('games_locked');
            });
        }
        if (Schema::hasColumn('current_week', 'locked_datetime')) {
            Schema::table('current_week', function (Blueprint $table) {
                $table->dropColumn('locked_datetime');
            });
        }
    }
};