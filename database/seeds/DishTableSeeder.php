<?php

use Illuminate\Database\Seeder;

class DishTableSeeder extends Seeder
{
	/**
	 * Run the database seeds.
	 *
	 * @return void
	 */
	public function run()
	{
		$kitchen = \App\Models\Kitchen::all()->random(1)->first();
		factory(App\Models\Dish::class, 10)->create(['kitchen_id' => $kitchen->id])->each(function ($u) {
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
		});
		$kitchen = \App\Models\Kitchen::all()->random(1)->first();
		factory(App\Models\Dish::class, 10)->create(['kitchen_id' => $kitchen->id])->each(function ($u) {
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
		});
		$kitchen = \App\Models\Kitchen::all()->random(1)->first();
		factory(App\Models\Dish::class, 10)->create(['kitchen_id' => $kitchen->id])->each(function ($u) {
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
		});
		$kitchen = \App\Models\Kitchen::all()->random(1)->first();
		factory(App\Models\Dish::class, 10)->create(['kitchen_id' => $kitchen->id])->each(function ($u) {
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
		});
		$kitchen = \App\Models\Kitchen::all()->random(1)->first();
		factory(App\Models\Dish::class, 10)->create(['kitchen_id' => $kitchen->id])->each(function ($u) {
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$u->media()->save(factory(App\Models\Media::class)->make());
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
			$user = \App\Models\User::all()->random(1)->first();
			$u->rating(factory(App\Models\Rating\DishRating::class)->raw(), $user)->save();
		});
	}
}
