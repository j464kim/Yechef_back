<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class DropKitchenMediaAndDishMediaTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::dropIfExists('dish_media');
		Schema::dropIfExists('kitchen_media');
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::create('dish_media', function(Blueprint $table)
		{
			$table->increments('id');
			$table->integer('dish_id')->unsigned();
			$table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
			$table->integer('media_id')->unsigned();
			$table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
			$table->timestamps();
		});

		Schema::create('kitchen_media', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('kitchen_id')->unsigned();
			$table->foreign('kitchen_id')->references('id')->on('kitchens')->onDelete('cascade');
			$table->integer('media_id')->unsigned();
			$table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
			$table->timestamps();
		});

    }
}
