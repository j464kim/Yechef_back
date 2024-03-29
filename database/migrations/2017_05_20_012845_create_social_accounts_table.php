<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateSocialAccountsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
    	// some social platform allow null email
		Schema::table('users', function (Blueprint $table) {
			$table->string('email')->nullable()->change();
		});

        Schema::create('social_accounts', function (Blueprint $table) {
			$table->integer('user_id');
			$table->string('provider_user_id');
			$table->string('provider');
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
		Schema::table('users', function (Blueprint $table) {
			$table->string('email')->nullable(false)->change();
		});
        Schema::dropIfExists('social_accounts');
    }
}
