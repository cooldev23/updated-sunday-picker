<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateTeamsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('teams', function (Blueprint $table) {
            $table->id();
            $table->string('team_key');
            $table->string('conference');
            $table->string('division');
            $table->string('fullname');
            $table->integer('bye_week')->unsigned();
            $table->integer('global_team_id')->unsigned();
            $table->string('primary_color');
            $table->string('secondary_color');
            $table->string('tertiary_color')->nullable();
            $table->string('quaternary_color')->nullable();
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
        Schema::dropIfExists('teams');
    }
}
