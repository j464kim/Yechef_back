<?php

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
		factory(App\Models\Kitchen::class, 10)->create()->each(function ($u) {
			for ($i = 0; $i < 3; $i++) {
				$u->medias()->save(factory(App\Models\Media::class)->make());
			}
		});
	}
}
