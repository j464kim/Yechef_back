<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateDishTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('dishes', function (Blueprint $table) {
            $table->increments('id');
			$table->string('slug');
            $table->string('name');
            $table->text('description');
            $table->timestamps();
		});

        Schema::create('dish_media', function(Blueprint $table)
        {
            $table->increments('id');
            $table->integer('dish_id')->unsigned();
            $table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
            $table->integer('media_id')->unsigned();
            $table->foreign('media_id')->references('id')->on('media')->onDelete('cascade');
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
		Schema::dropIfExists('dish_media');
        Schema::dropIfExists('dishes');
	}
}
