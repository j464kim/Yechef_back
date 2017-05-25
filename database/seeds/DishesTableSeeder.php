<?php

use App\Models\Dish;
use App\Models\Kitchen;
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
		$kitchens = Kitchen::all();
		foreach ($kitchens as $kitchen) {
			factory(Dish::class, 5)->create(['kitchen_id' => $kitchen->id]);
		}
	}
}
