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
		\DB::table('kitchens')->delete();

		factory(Kitchen::class, 1)->create(['address' => '10 Noecker St Waterloo, ON N2J 2R2']);
		factory(Kitchen::class, 1)->create(['address' => '158 King St N, Waterloo, ON N2J 2Y1']);
		factory(Kitchen::class, 1)->create(['address' => '5060 Circle Road Apt203 Montreal, QC H3W 2A1']);
		factory(Kitchen::class, 1)->create(['address' => '1345 Croissant Saturne Montreal, QC']);
		factory(Kitchen::class, 1)->create(['address' => '5939 Boulevard Monk, Montréal, QC H4E 3H5']);

	 	Kitchen::all()->each(function ($u) {
			$user = User::first();
			$u->users()->save($user, ['verified' => true, 'role' => 1]);
			$user = User::find(2);
			$u->users()->save($user, ['verified' => false, 'role' => 1]);
		});
	}
}
