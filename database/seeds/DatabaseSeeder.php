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

		\DB::table('dishes')->delete();
		\DB::table('dish_ratings')->delete();
		\DB::table('users')->delete();
		\DB::table('kitchens')->delete();
		\DB::table('media')->delete();

		$this->call(UserTableSeeder::class);
		$this->call(KitchenTableSeeder::class);
		$this->call(DishTableSeeder::class);

		Model::reguard();
	}
}
