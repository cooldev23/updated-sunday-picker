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
            $table->integer('previous_nfl_week')->nullable()->after('current_nfl_week');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasColumn('current_week', 'previous_nfl_week')) {
            Schema::table('current_week', function (Blueprint $table) {
                $table->dropColumn('previous_nfl_week');
            });
        }
    }
};
