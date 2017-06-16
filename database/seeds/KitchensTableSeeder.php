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
		factory(Kitchen::class, 5)->create()->each(function ($u) {
			$user = User::first();
			$u->users()->save($user, ['verified' => true, 'role' => 1]);
			$user = User::find(2);
			$u->users()->save($user, ['verified' => false, 'role' => 1]);
		});
	}
}
