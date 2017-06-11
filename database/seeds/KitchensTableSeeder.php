<?php

use Illuminate\Database\Seeder;
use App\Models\Kitchen;

class KitchensTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		factory(Kitchen::class, 5)->create();
	}
}
