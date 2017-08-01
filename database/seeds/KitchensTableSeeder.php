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

		factory(Kitchen::class, 1)->create([
			'address' => '10 Noecker St Waterloo, ON N2J 2R2',
			'lat'     => 43.471626,
			'lng'     => -80.523249
		]);
		factory(Kitchen::class, 1)->create([
			'address' => '158 King St N, Waterloo, ON N2J 2Y1',
			'lat'     => 43.471770,
			'lng'     => -80.523690
		]);
		factory(Kitchen::class, 1)->create([
			'address' => '5060 Circle Road Apt203 Montreal, QC H3W 2A1',
			'lat'     => 45.484840,
			'lng'     => -73.627083
		]);
		factory(Kitchen::class, 1)->create([
			'address' => '1345 Croissant Saturne Montreal, QC',
			'lat'     => 45.451431,
			'lng'     => -73.484417
		]);
		factory(Kitchen::class, 1)->create([
			'address' => '5939 Boulevard Monk, Montréal, QC H4E 3H5',
			'lat'     => 45.459195,
			'lng'     => -73.596454
		]);
		factory(Kitchen::class, 1)->create([
			'address' => '200 University Ave W, Waterloo, ON N2L 3G1',
			'lat'     => 43.467949,
			'lng'     => -80.543231
		]);

		Kitchen::all()->each(function ($u) {
			$user = User::first();
			$u->users()->save($user, ['verified' => true, 'role' => 1]);
			$user = User::find(2);
			$u->users()->save($user, ['verified' => false, 'role' => 1]);
		});
	}
}
