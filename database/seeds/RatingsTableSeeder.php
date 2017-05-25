<?php

use App\Models\Dish;
use App\Models\DishRating;
use App\Models\User;
use Illuminate\Database\Seeder;

class RatingsTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$dishes = Dish::all();
		foreach ($dishes as $dish) {
			$users = User::all();
			foreach ($users as $user) {
				factory(DishRating::class, 1)->create(['dish_id' => $dish->id, 'user_id' => $user->id]);
			}
		}
	}
}
