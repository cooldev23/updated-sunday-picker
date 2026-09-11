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
        if (!Schema::hasColumn('league_user', 'survivor_eliminated')) {
            Schema::table('league_user', function (Blueprint $table) {
                $table->boolean('survivor_eliminated')->after('league_id')->default(0);
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
        Schema::table('league_user', function (Blueprint $table) {
            $table->dropColumn('survivor_eliminated');
        });
    }
};
