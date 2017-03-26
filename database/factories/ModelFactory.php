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
    $exampleImages = [
		'https://lh3.googleusercontent.com/chFm-ptLkRR9S4dbmZXhCLMePLXU-i0GKtWS9j0Htf1dN1vTVJlUIjWpKHA-ljZy5p1dj287=s630-fcrop64=1,201836e8dfa4c8bd',
		'http://www.lepelican-journal.com/journal/illustrations/grandes/2783_c5b8801b7ad939fef1bc7a0bc8a0002f77f66f5c.jpg',
		'http://3.bp.blogspot.com/_Ksh49C97yQc/S9KHVx0zwwI/AAAAAAAADhM/yvC3FCXiQxg/s400/IMG_3689.JPG',
		'https://www.protegez-vous.ca/var/protegez_vous/storage/pages/images/2012/CES/2015-05_crusine_1536x1146.jpg',
		'http://img.over-blog-kiwi.com/1/36/69/69/20151027/ob_87add2_aimg-0607ok.jpg',
		'https://4.bp.blogspot.com/-2pttYvt3NkI/V3c1EUOJn1I/AAAAAAAABTA/eEpv3gmxoUQWyq1yg8w_NBmqKQfek5JdQCLcB/s1600/1%2BVege%2BThali%2Bcover.jpg',
		'https://media-cdn.tripadvisor.com/media/photo-s/04/b2/9e/af/caption.jpg',
		'http://deals.bigsale.com.my/images/deals/522929/hwa-ga-authentic-traditional-deal.jpg',
		'https://s-media-cache-ak0.pinimg.com/736x/d2/99/36/d299369e833036beeed83cf42d92df26.jpg',
		'https://media-cdn.tripadvisor.com/media/photo-s/0b/64/17/4b/very-friendly-resturant.jpg',
		'https://www.lundi-veggie.fr/wp-content/uploads/2016/04/crusine01.jpg',
	];
	
    return [
        'slug' => str_random(10),
        'url' => $exampleImages[array_rand($exampleImages)]
    ];
});


/** @var \Illuminate\Database\Eloquent\Factory $factory */
$factory->define(App\Models\Dish::class, function (Faker\Generator $faker) {
    return [
        'slug' => str_random(10),
        'name' => str_random(10),
        'description' => str_random(10)
    ];
});