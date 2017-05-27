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
		'description' => $faker->realText(),
		'price'       => $faker->randomFloat(2, 5, 25),
	];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
	return [
		'email'      => $faker->email,
		'first_name' => $faker->firstName,
		'password'   => $faker->password,
		'last_name'  => $faker->lastName,
		'phone'      => $faker->phoneNumber,
	];
});

/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Kitchen::class, function (Faker\Generator $faker) {
	return [
		'name'        => $faker->name,
		'address'     => $faker->address,
		'phone'       => $faker->phoneNumber,
		'email'       => $faker->unique()->safeEmail,
		'description' => str_random(10),
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