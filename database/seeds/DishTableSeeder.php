<?php

use Illuminate\Database\Seeder;

class DishTableSeeder extends Seeder
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
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			for ($i = 0; $i < 10; $i++) {
				$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			}
		});
	}
}
