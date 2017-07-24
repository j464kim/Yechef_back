<?php

use App\Models\User;
use App\Models\UserSetting;
use Illuminate\Database\Seeder;

class UserSettingsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		\DB::table('user_settings')->delete();

		\DB::table('user_settings')->insert(array(
			0 =>
				array(
					'id'                => 1,
					'user_id'           => 1,
					'show_phone'        => true,
					'show_forks'        => true,
					'show_subscription' => true,
					'created_at' => '2017-05-24 04:03:52',
					'updated_at' => '2017-05-24 04:03:52',
				),
		));
		$users = User::where('id', '>', 1)->get();
		foreach ($users as $user) {
			factory(UserSetting::class, 1)->create(['user_id' => $user->id]);
		}
	}
}
