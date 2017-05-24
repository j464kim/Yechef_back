<?php

use Illuminate\Database\Seeder;

class DishesTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		for ($i = 0; $i < 5; $i++) {
			$kitchen = \App\Models\Kitchen::all()->random(1)->first();
			factory(App\Models\Dish::class, 10)->create(['kitchen_id' => $kitchen->id])->each(function ($u) {
				$user = \App\Models\User::all()->random(1)->first();
				for ($i = 0; $i < 10; $i++) {
					$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
				}
				for ($i = 0; $i < 3; $i++) {
					$u->medias()->save(factory(App\Models\Media::class)->make());
				}
			});
		}
	}
}
