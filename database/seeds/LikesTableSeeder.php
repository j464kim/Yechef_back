<?php

use Illuminate\Database\Seeder;
use App\Models\Kitchen;
use App\Models\User;

class LikesTableSeeder extends Seeder
{

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{

		\DB::table('likes')->delete();

		$kitchens = Kitchen::get();

		// Assumption: there is 11 users
		// some of the users leave 'like' to every kitchen
		for ($i = 1; $i <= 6; $i++) {
			$user = User::find($i);
			foreach ($kitchens as $kitchen) {
				\DB::table('likes')->insert(array(
					0 =>
						array(
							'isLike'       => 1,
							'user_id'      => $user->id,
							'likable_id'   => $kitchen->id,
							'likable_type' => get_class($kitchen),
							'created_at'   => '2017-05-24 05:49:45',
							'updated_at'   => '2017-05-24 05:49:45',
						),
				));
			}
		}
		// some of the users leave 'dislike' to every kitchen
		for ($i = 7; $i <= 11; $i++) {
			$user = User::find($i);
			foreach ($kitchens as $kitchen) {
				\DB::table('likes')->insert(array(
					0 =>
						array(
							'isLike'       => 0,
							'user_id'      => $user->id,
							'likable_id'   => $kitchen->id,
							'likable_type' => get_class($kitchen),
							'created_at'   => '2017-05-24 05:49:45',
							'updated_at'   => '2017-05-24 05:49:45',
						),
				));
			}
		}

	}
}