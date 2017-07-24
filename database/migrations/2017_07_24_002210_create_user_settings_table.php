<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserSettingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('user_settings', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->boolean('show_phone');
			$table->boolean('show_subscription');
			$table->boolean('show_forks');
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
		Schema::dropIfExists('user_settings');
	}
}
