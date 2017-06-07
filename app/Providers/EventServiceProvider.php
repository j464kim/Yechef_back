<?php

namespace App\Providers;

use Illuminate\Foundation\Support\Providers\EventServiceProvider as ServiceProvider;
use App\Events\ReactionableDeleted;
use App\Listeners\ReactionableDeletedListener;

class EventServiceProvider extends ServiceProvider
{
	/**
	 * The event listener mappings for the application.
	 *
	 * @var array
	 */
	protected $listen = [
		'App\Events\Event'       => [
			'App\Listeners\EventListener',
		],
		'App\Events\DishDeleted' => [
			'App\Listeners\DishDeletedListener',
		],
		ReactionableDeleted::class => [
			ReactionableDeletedListener::class,
		],
	];

	/**
	 * Register any events for your application.
	 *
	 * @return void
	 */
	public function boot()
	{
		parent::boot();

		//
	}
}
