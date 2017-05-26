<?php

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Schema;

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

		Schema::disableForeignKeyConstraints();

		\DB::table('dish_ratings')->truncate();
		\DB::table('media')->truncate();
		\DB::table('dishes')->truncate();
		\DB::table('kitchens')->truncate();
		\DB::table('users')->truncate();

		$this->call(UsersTableSeeder::class);
		$this->call(KitchensTableSeeder::class);
		$this->call(DishesTableSeeder::class);
		$this->call(RatingsTableSeeder::class);
		$this->call(MediasTableSeeder::class);

		Schema::enableForeignKeyConstraints();

		Model::reguard();
	}
}
