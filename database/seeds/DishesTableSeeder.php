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
		factory(App\Models\Dish::class, 50)->create()->each(function ($u) {
			$user = \App\Models\User::first();
			for ($i = 0; $i < 10; $i++) {
				$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			}
			for ($i = 0; $i < 3; $i++) {
				$u->medias()->save(factory(App\Models\Media::class)->make());
			}
		});
	}
}
