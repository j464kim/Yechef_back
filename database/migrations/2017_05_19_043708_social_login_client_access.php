<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

class SocialLoginClientAccess extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
		DB::table('oauth_clients')->insert([
			'id' => 10,
			'name' => 'social-login',
			'secret' => 'DRpnZa1AOH3dJn7AkUGXqL0w3ZlCNLoWdvoKlfWA',
			'redirect' => 'http://localhost',
			'personal_access_client' => 1,
			'password_client' => 0,
			'revoked' => 0
		]);

		DB::table('oauth_personal_access_clients')->insert([
			'client_id' => 10
		]);
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
		DB::table('oauth_personal_access_clients')->where('client_id', 10)->delete();
		DB::table('oauth_clients')->where('id', 10)->delete();
    }
}
