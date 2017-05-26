<?php

use Illuminate\Support\Facades\Schema;
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

		Schema::disableForeignKeyConstraints();

		\DB::table('media')->truncate();
		\DB::table('dishes')->truncate();
		\DB::table('kitchens')->truncate();
		\DB::table('likes')->truncate();
		\DB::table('users')->truncate();

		$this->call(DishesTableSeeder::class);
		$this->call(KitchensTableSeeder::class);
		$this->call(UsersTableSeeder::class);
		$this->call(LikesTableSeeder::class);

		Schema::enableForeignKeyConstraints();

		Model::reguard();
    }
}
