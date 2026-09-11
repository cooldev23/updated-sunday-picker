<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePicksUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('picks_users', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id_FK');
            $table->foreignId('league_id_FK');
            $table->integer('game_id');
            $table->integer('nfl_week');
            $table->string('winner')->nullable();
            $table->integer('weighted_order')->nullable();
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
        Schema::dropIfExists('picks_users');
    }
}
