<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLeagueStatsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('league_stats', function (Blueprint $table) {
            $table->id();
            $table->foreignId('league_id')->references('id')->on('leagues');
            $table->integer('total_correct_wk');
            $table->integer('total_games_wk');
            $table->integer('total_correct_yr');
            $table->integer('total_games_yr');
            $table->integer('total_members');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('league_stats');
    }
}
