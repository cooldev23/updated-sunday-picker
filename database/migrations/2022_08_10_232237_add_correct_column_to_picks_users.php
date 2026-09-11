<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCorrectColumnToPicksUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('picks_users', function (Blueprint $table) {
            $table->boolean('correct')->default(0)->after('weighted_order');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('picks_users', function (Blueprint $table) {
            $table->dropColumn('correct');
        });
    }
}
