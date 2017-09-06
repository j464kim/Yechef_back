<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateBusinessHoursTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::create('business_hours', function (Blueprint $table) {
			$table->increments('id');
			$table->unsignedInteger('kitchen_id');
			$table->foreign('kitchen_id')->references('id')->on('kitchens')->onDelete('cascade');
			$table->boolean('active');
			$table->unsignedInteger('day');
			$table->unsignedInteger('open_time');
			$table->unsignedInteger('close_time');
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
		Schema::dropIfExists('business_hours');
    }
}
