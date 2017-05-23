<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPolymorphismToMedia extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		Schema::table('media', function (Blueprint $table) {
			$table->integer('mediable_id')->after('type');
			$table->string('mediable_type')->after('mediable_id');
		});
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		Schema::table('media', function (Blueprint $table) {
			$table->dropColumn('mediable_id');
			$table->dropColumn('mediable_type');
		});
    }
}
