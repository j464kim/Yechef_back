<?php

namespace App\Http\Controllers;


use App\Events\MessageSent;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{

	/**
	 * MessageController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		parent::__construct($app);
	}

	/**
	 * Get the list of chat rooms the user belongs to
	 * @param Request $request
	 */
	public function getRooms(Request $request)
	{

	}

	/**
	 * Start a chatting room. If the room does not exists, then create one. Otherwise, return the existing one
	 * @param Request $request
	 */
	public function joinRoom(Request $request)
	{

	}

	/**
	 * Send a message in the correct chatting room
	 * @param Request $request
	 */
	public function sendMessage(Request $request)
	{
		$user = $this->getUser($request);
		$message = $request->message;
		event(new MessageSent($user, $message));
	}

	/**
	 * Delete a message sent.
	 * @param Request $request
	 */
	public function deleteMessage(Request $request)
	{

	}
}