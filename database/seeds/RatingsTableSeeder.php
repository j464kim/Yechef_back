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
		for ($i = 0; $i < 50; $i++) {
			$dish = Dish::findDish($i + 1);
			$user = User::findOrFail(($i % 10) + 1);
			factory(DishRating::class, 10)->create(['dish_id' => $dish->id, 'user_id' => $user->id]);
		}
	}
}
