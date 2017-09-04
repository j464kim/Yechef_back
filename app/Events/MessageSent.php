<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Contracts\Broadcasting\ShouldBroadcastNow;
use Illuminate\Foundation\Events\Dispatchable;
use Illuminate\Queue\SerializesModels;

/**
 * Class MessageSent
 * @package App\Events
 */
class MessageSent implements ShouldBroadcastNow
{
	use Dispatchable, InteractsWithSockets, SerializesModels;

	/**
	 * @var Message
	 */
	/**
	 * @var Message|User
	 */
	public $message, $user, $message_to;

//	public $broadcastQueue = 'your-queue-name';

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, User $message_to, Message $message)
	{
		$this->user = $user;
		$this->message_to = $message_to;
		$this->message = $message;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new Channel('message.' . $this->message_to->id);
	}

}
