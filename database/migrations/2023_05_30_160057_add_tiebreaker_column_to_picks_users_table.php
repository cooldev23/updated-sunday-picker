<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        if (!Schema::hasColumn('picks_users', 'tiebreaker')) {
            Schema::table('picks_users', function (Blueprint $table) {
                $table->integer('tiebreaker')->after('weighted_order')->nullable();
            });
        }
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picks_users', function (Blueprint $table) {
            $table->dropColumn('tiebreaker');
        });
    }
};
