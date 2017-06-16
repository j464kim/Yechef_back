<?php

use Illuminate\Database\Seeder;
use App\Models\Kitchen;
use App\Models\Dish;
use App\Models\User;

class ReactionsTableSeeder extends Seeder
{

	/**
	 * Auto generated seed file
	 *
	 * @return void
	 */
	public function run()
	{

		\DB::table('reactions')->delete();

		$kitchens = Kitchen::get();
		$dishes = Dish::get();

		// Assumption: there is 11 users
		// some of the users leave 'like' to every kitchen
		for ($i = 1; $i <= 6; $i++) {
			$user = User::find($i);

			// Seed Kitchen with Likes
			foreach ($kitchens as $kitchen) {
				\DB::table('reactions')->insert(array(
					0 =>
						array(
							'kind'              => 1,
							'user_id'           => $user->id,
							'reactionable_id'   => $kitchen->id,
							'reactionable_type' => get_class($kitchen),
							'created_at'        => '2017-05-24 05:49:45',
							'updated_at'        => '2017-05-24 05:49:45',
						),
				));
			}

			// Seed Kitchens with Forks
			foreach ($dishes as $dish) {
				\DB::table('reactions')->insert(array(
					0 =>
						array(
							'kind'              => 2,
							'user_id'           => $user->id,
							'reactionable_id'   => $dish->id,
							'reactionable_type' => get_class($dish),
							'created_at'        => '2017-05-24 05:49:45',
							'updated_at'        => '2017-05-24 05:49:45',
						),
				));
			}
		}
		for ($i = 7; $i <= 11; $i++) {
			$user = User::find($i);
			foreach ($kitchens as $kitchen) {
				// Seed Kitchens with Dislike
				\DB::table('reactions')->insert(array(
					0 =>
						array(
							'kind'              => 0,
							'user_id'           => $user->id,
							'reactionable_id'   => $kitchen->id,
							'reactionable_type' => get_class($kitchen),
							'created_at'        => '2017-05-24 05:49:45',
							'updated_at'        => '2017-05-24 05:49:45',
						),
				));
				// Seed Kitchens with Subscribe
				\DB::table('reactions')->insert(array(
					0 =>
						array(
							'kind'              => 3,
							'user_id'           => $user->id,
							'reactionable_id'   => $kitchen->id,
							'reactionable_type' => get_class($kitchen),
							'created_at'        => '2017-05-24 05:49:45',
							'updated_at'        => '2017-05-24 05:49:45',
						),
				));
			}
		}

	}
}