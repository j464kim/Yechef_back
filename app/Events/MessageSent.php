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
	 * --- IMPORTANT ---
	 * Both variables must be set from constants and are required in EVERY Event Class.
	 * @var $type
	 */
	/**
	 * @var $action
	 */
	public $type, $action;


	/**
	 * @var Message $message
	 */
	/**
	 * @var User $user
	 */
	/**
	 * @var User $message_to
	 */
	public $message, $user, $message_to;

	// TODO
//	public $broadcastQueue = 'your-queue-name';

	/**
	 * Create a new event instance.
	 *
	 * @return void
	 */
	public function __construct(User $user, User $message_to, Message $message)
	{
		// type and action variables must be defined from constants
		$this->type = config('constants.events.message.type');
		$this->action = config('constants.events.message.action.sent');

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
		return new Channel('pusher.' . $this->message_to->id);
	}

	/**
	 * The event's broadcast name.
	 *
	 * @return string
	 */
	public function broadcastAs()
	{
		return config('constants.events.message.type');
	}

}
