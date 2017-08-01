<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddOrderItemIdToDishRatings extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('dish_ratings', function (Blueprint $table) {
            $table->integer('order_item_id')->unsigned()->after('dish_id');
			$table->foreign('order_item_id')->references('id')->on('order_items');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('dish_ratings', function (Blueprint $table) {
			$table->dropForeign('dish_ratings_order_item_id_foreign');
			$table->dropColumn('order_item_id');
        });
    }
}