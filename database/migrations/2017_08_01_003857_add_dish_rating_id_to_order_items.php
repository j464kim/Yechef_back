<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddDishRatingIdToOrderItems extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('order_items', function (Blueprint $table) {
			$table->integer('dish_rating_id')->nullable()->unsigned()->after('dish_id');
			$table->foreign('dish_rating_id')->references('id')->on('dish_ratings');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('order_items', function (Blueprint $table) {
			$table->dropForeign('order_items_dish_rating_id_foreign');
			$table->dropColumn('dish_rating_id');
        });
    }
}
