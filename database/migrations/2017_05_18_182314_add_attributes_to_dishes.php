<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributesToDishes extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dishes', function (Blueprint $table) {
			$table->double('price')->after('description');
			$table->unsignedInteger('kitchen_id')->after('price');
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
			$table->dropColumn('kitchen_id');
			$table->dropColumn('price');
		});
	}
}
