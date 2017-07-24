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

		\DB::table('dish_ratings')->truncate();
		\DB::table('media')->truncate();
		\DB::table('dishes')->truncate();
		\DB::table('kitchens')->truncate();
		\DB::table('reactions')->truncate();
		\DB::table('users')->truncate();
		\DB::table('kitchen_user')->truncate();
		\DB::table('user_settings')->truncate();
		\DB::table('cart_items')->truncate();
		\DB::table('carts')->truncate();
		\DB::table('payments')->truncate();
		\DB::table('transactions')->truncate();
		\DB::table('order_items')->truncate();
		\DB::table('orders')->truncate();

		$this->call(UsersTableSeeder::class);
		$this->call(UserSettingsTableSeeder::class);
		$this->call(KitchensTableSeeder::class);
		$this->call(DishesTableSeeder::class);
		$this->call(RatingsTableSeeder::class);
		$this->call(MediasTableSeeder::class);
		$this->call(ReactionsTableSeeder::class);

		Schema::enableForeignKeyConstraints();

		Model::reguard();
	}
}
