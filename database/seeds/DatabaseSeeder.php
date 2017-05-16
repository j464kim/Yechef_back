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
		\DB::table('users')->delete();
		\DB::table('kitchens')->delete();
		\DB::table('media')->delete();

		$this->call(DishTableSeeder::class);
		$this->call(UserTableSeeder::class);
		$this->call(KitchenTableSeeder::class);

		Model::reguard();
	}
}
