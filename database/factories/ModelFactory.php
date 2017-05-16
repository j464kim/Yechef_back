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
	return [
		'slug' => str_random(10),
		'url'  => "http://lorempixel.com/400/200/"
	];
});


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Dish::class, function (Faker\Generator $faker) {
	return [
		'slug'        => $faker->name,
		'name'        => $faker->slug,
		'description' => $faker->text()
	];
});

$factory->define(App\Models\User::class, function (Faker\Generator $faker) {
	return [
		'email'    => $faker->email,
		'name'     => $faker->name,
		'password' => $faker->password
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