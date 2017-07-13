<?php

/*
|--------------------------------------------------------------------------
| Model Factories
|--------------------------------------------------------------------------
|
| Here you may define all of your model factories. Model factories give
| you a convenient way to create models for testing and seeding your
| database. Just tell the factory how a default model should look.
|
*/

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Media::class, function (Faker\Generator $faker) {
	$randomNumber = $faker->numberBetween(0,10);
	return [
		'slug' => str_random(10),
		'url'  => "http://lorempixel.com/400/200/food/$randomNumber/YeChef/"
	];
});


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Dish::class, function (Faker\Generator $faker) {
	return [
		'slug'        => $faker->slug,
		'name'        => $faker->word,
		'nationality' => 'fusion',
		'description' => $faker->realText(),
		'price'       => $faker->randomFloat(2, 5, 25),
		'vegetarian'  => $faker->boolean(),
		'vegan'  => $faker->boolean(),
		'gluten_free'  => $faker->boolean(),
	];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
	return [
		'email'      => $faker->email,
		'first_name' => $faker->firstName,
		'password'   => bcrypt('password'),
		'last_name'  => $faker->lastName,
		'phone'      => $faker->phoneNumber,
		'show_phone' => $faker->boolean(),
		'show_subscription' => $faker->boolean(),
		'show_forks' => $faker->boolean(),
	];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Kitchen::class, function (Faker\Generator $faker) {
	return [
		'slug'        => $faker->slug,
		'name'        => $faker->name,
		'address'     => $faker->address,
		'phone'       => $faker->phoneNumber,
		'email'       => $faker->unique()->safeEmail,
		'description' => $faker->realText(),
	];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\DishRating::class, function (Faker\Generator $faker) {
	return [
		'taste_rating'    => $faker->numberBetween(1, 5),
		'visual_rating'   => $faker->numberBetween(1, 5),
		'quantity_rating' => $faker->numberBetween(1, 5),
		'comment'         => $faker->text(),
	];
});