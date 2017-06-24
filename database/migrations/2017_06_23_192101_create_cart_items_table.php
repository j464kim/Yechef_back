<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateCartItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('cart_items', function (Blueprint $table) {
			$table->increments('id');
			$table->integer('cart_id')->unsigned();
			$table->foreign('cart_id')->references('id')->on('carts')->onDelete('cascade');
			$table->integer('dish_id')->unsigned();
			$table->foreign('dish_id')->references('id')->on('dishes')->onDelete('cascade');
			$table->integer('quantity')->unsigned();
			$table->integer('price')->unsigned();
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
		Schema::dropIfExists('cart_items');

	}
}
