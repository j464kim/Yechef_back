<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateMessageRoomUserTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('message_room_user', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('message_room_id')->unsigned();
			$table->foreign('message_room_id')->references('id')->on('message_rooms');
			$table->unsignedInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
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
		Schema::dropIfExists('message_room_user');
	}
}
