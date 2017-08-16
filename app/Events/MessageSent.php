<?php

namespace App\Events;

use App\Models\Message;
use App\Models\User;
use Illuminate\Broadcasting\Channel;
use Illuminate\Broadcasting\InteractsWithSockets;
use Illuminate\Broadcasting\PrivateChannel;
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
	public $message, $user;

//	public $broadcastQueue = 'your-queue-name';

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, Message $message)
	{
		$this->user = $user;
		$this->message = $message;
	}

	/**
	 * Get the channels the event should broadcast on.
	 *
	 * @return Channel|array
	 */
	public function broadcastOn()
	{
		return new PrivateChannel('message.' . $this->message->message_room_id);
	}

}
