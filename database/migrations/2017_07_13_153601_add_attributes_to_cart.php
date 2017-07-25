<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddAttributesToCart extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('carts', function (Blueprint $table) {
			$table->unsignedInteger('kitchen_id')->after('user_id');
			$table->foreign('kitchen_id')->references('id')->on('kitchens');
			$table->dropColumn('total_price');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('carts', function (Blueprint $table) {
			$table->dropForeign('carts_kitchen_id_foreign');
			$table->dropColumn('kitchen_id');
			$table->integer('total_price');
		});
	}
}
