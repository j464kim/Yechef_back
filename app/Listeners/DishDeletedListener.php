<?php

namespace App\Listeners;

use App\Events\DishDeleted;

class DishDeletedListener
{
	/**
	 * Create the event listener.
	 *
	 * @return void
	 */
	public function __construct()
	{
		//
	}

	/**
	 * Handle the event.
	 *
	 * @param  DishDeleted $event
	 * @return void
	 */
	public function handle(DishDeleted $event)
	{
		// Delete associated media data
	}
}
