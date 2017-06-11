<?php

use App\Models\Dish;
use App\Models\Kitchen;
use App\Models\Media;
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
				$dish->medias()->save(factory(Media::class)->make());
			}
		}
		foreach ($kitchens as $kitchen) {
			$kitchen->medias()->save(factory(Media::class)->make(['url' => 'http://hbu.h-cdn.co/assets/17/08/1600x1028/gallery-1487868231-kitchen-1.jpg']));
		}

	}
}
