<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateKitchenUserTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('kitchen_user', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('kitchen_id')->unsigned();
			$table->foreign('kitchen_id')->references('id')->on('kitchens');
			$table->unsignedInteger('user_id')->unsigned();
			$table->foreign('user_id')->references('id')->on('users');
			$table->tinyInteger('role');
			$table->boolean('verified');
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
		Schema::dropIfExists('kitchen_user');
    }
}
