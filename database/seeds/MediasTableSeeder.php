<?php

use App\Models\Dish;
use App\Models\Kitchen;
use Illuminate\Database\Seeder;

class MediasTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$dishes = Dish::all();
		$kitchens = Kitchen::all();
		foreach ($dishes as $dish) {
			for ($i = 0; $i < 3; $i++) {
				$dish->medias()->save(factory(App\Models\Media::class)->make());
			}
		}
		foreach ($kitchens as $kitchen) {
			for ($i = 0; $i < 3; $i++) {
				$kitchen->medias()->save(factory(App\Models\Media::class)->make());
			}
		}
	}
}
