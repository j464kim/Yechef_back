<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class AddPrivacyToUsers extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('users', function (Blueprint $table) {
            //
			$table->boolean('show_phone')->after('verified');
			$table->boolean('show_subscription')->after('show_phone');
			$table->boolean('show_forks')->after('show_subscription');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('users', function (Blueprint $table) {
            //
			$table->dropColumn('show_phone');
			$table->dropColumn('show_subscription');
			$table->dropColumn('show_forks');
		});
    }
}
