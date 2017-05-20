<?php

namespace App\Events;

use App\Models\Dish;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

class DishDeleted
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	public $dish;

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(Dish $dish)
	{
		//
		$this->dish = $dish;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('channel-name');
	}
}
