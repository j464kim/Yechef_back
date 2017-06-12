<?php

use App\Models\Kitchen;
use App\Models\User;
use Illuminate\Database\Seeder;

class KitchensTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$user = User::first();
		factory(Kitchen::class, 5)->create(['user_id' => $user->id]);
	}
}
