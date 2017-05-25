<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDishRatingsTable extends Migration
{
	/**
	 * Run the migrations.
	 *
	 * @return void
	 */
	public function up()
	{
		Schema::create('dish_ratings', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('taste_rating');
			$table->integer('visual_rating');
			$table->integer('quantity_rating');
			$table->text('comment');
			$table->unsignedInteger('dish_id');
			$table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
			$table->unsignedInteger('user_id');
			$table->foreign('user_id')->references('id')->on('users');
			$table->timestamps();
			$table->softDeletes();
		});
	}

	/**
	 * Reverse the migrations.
	 *
	 * @return void
	 */
	public function down()
	{
		Schema::disableForeignKeyConstraints();
		Schema::dropIfExists('dish_ratings');
		Schema::enableForeignKeyConstraints();
	}
}
