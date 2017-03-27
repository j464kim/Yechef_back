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
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
		});
	}
}
