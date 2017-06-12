<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttrubutesToKitchens extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	Schema::table('kitchens', function (Blueprint $table) {
    		$table->unsignedInteger('user_id')->after('description');
    		$table->foreign('user_id')->references('id')->on('users')->onDelete('cascade');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
    	Schema::table('dishes', function (Blueprint $table) {
    		$table->dropForeign('kitchens_user_id_foreign');
    		$table->dropColumn('user_id');
		});
    }
}
