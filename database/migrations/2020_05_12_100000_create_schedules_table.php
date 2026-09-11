<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSchedulesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('schedules', function (Blueprint $table) {
            $table->id();
            $table->integer('nfl_year');
            $table->integer('nfl_week');
            $table->string('home_team');
            $table->string('away_team');
            $table->integer('global_game_id');
            $table->integer('global_away_team_id')->nullable();
            $table->integer('global_home_team_id')->nullable();
            $table->datetime('game_time', 0)->nullable();
            $table->string('channel')->nullable();
            $table->string('stadium_name')->nullable();
            $table->string('city')->nullable();
            $table->string('state')->nullable();
            $table->string('playing_surface')->nullable();
            $table->string('stadium_type')->nullable();
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
        Schema::dropIfExists('schedules');
    }
}
