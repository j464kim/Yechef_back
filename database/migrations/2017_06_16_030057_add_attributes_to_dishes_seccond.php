<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddAttributesToDishesSeccond extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('dishes', function (Blueprint $table) {
			$table->string('nationality')->after('name');
			$table->boolean('gluten_free')->after('price');
			$table->boolean('vegan')->after('price');
			$table->boolean('vegetarian')->after('price');
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
			$table->dropColumn('nationality');
			$table->dropColumn('gluten_free');
			$table->dropColumn('vegan');
			$table->dropColumn('vegetarian');
		});
	}
}
