<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		Model::unguard();

//		\DB::table('dish_ratings')->truncate();
//		\DB::table('media')->truncate();
//		\DB::table('dishes')->truncate();
//		\DB::table('kitchens')->truncate();
//		\DB::table('users')->truncate();

		$this->call(UserTableSeeder::class);
		$this->call(DishTableSeeder::class);
		$this->call(KitchenTableSeeder::class);

		Model::reguard();
	}
}
