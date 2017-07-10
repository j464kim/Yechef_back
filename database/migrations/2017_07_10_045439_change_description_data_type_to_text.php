<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class ChangeDescriptionDataTypeToText extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('dishes', function (Blueprint $table) {
			$table->text('description')->nullable()->change();
		});

		Schema::table('kitchens', function (Blueprint $table) {
			$table->text('description')->nullable()->change();
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('dishes', function (Blueprint $table) {
			$table->string('description')->change();
		});

		Schema::table('kitchens', function (Blueprint $table) {
			$table->text('description')->change();
		});
    }
}
