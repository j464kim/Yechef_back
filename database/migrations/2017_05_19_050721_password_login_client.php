<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class PasswordLoginClient extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::table('oauth_clients')->insertGetId([
			'id' => 10,
			'name' => 'password',
			'secret' => 'NWftDkQwYUArTcRcmVQF6jFdtiJoHTNQaItry43q',
			'redirect' => 'http://localhost',
			'personal_access_client' => 0,
			'password_client' => 1,
			'revoked' => 0
		]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::table('oauth_clients')->where('id', 10)->delete();
    }
}
