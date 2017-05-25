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
			$table->foreign('kitchen_id')->references('id')->on('kitchens')->onDelete('cascade');
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
			$table->dropForeign('dishes_kitchen_id_foreign');
			$table->dropColumn('kitchen_id');
			$table->dropColumn('price');
		});
	}
}
