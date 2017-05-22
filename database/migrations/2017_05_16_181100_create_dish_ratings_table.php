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
			$table->morphs('ratingable');
			$table->unsignedInteger('author_id');
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
		Schema::dropIfExists('dish_ratings');
	}
}
