<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCoordsToKitchens extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::table('kitchens', function (Blueprint $table) {
			$table->decimal('lat', 9, 6)->after('address');
			$table->double('lng', 9, 6)->after('lat');
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::table('kitchens', function (Blueprint $table) {
			$table->dropColumn('lat');
			$table->dropColumn('lng');
		});
	}
}
