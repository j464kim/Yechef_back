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
		for ($i = 0; $i < 10; $i++) {
			$kitchen = Kitchen::findKitchen($i + 1);
			factory(Dish::class, 5)->create(['kitchen_id' => $kitchen->id]);
		}
	}
}
