<?php

namespace App\Http\Controllers;


use App\Events\MessageSent;
use App\Exceptions\YechefException;
use App\Models\Message;
use App\Models\User;
use App\Services\MessageService;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

/**
 * Class MessageController
 * @package App\Http\Controllers
 */
class MessageController extends Controller
{
	protected $messageService;

	/**
	 * MessageController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app, MessageService $messageService)
	{
		parent::__construct($app);
		$this->messageService = $messageService;
	}

	/**
	 * Get the list of chat rooms the user belongs to with the latest message in the message room
	 * @param Request $request
	 */
	public function getRooms(Request $request)
	{
		$user = $this->getUser($request);
		$myMessageRooms = $user->messageRooms;
		foreach ($myMessageRooms as $myMessageRoom) {
			$myMessageRoom->load([
				'messages' => function ($q) {
					$q->orderBy('created_at', 'desc')->first();
				}
			]);
		}

		return response()->success($myMessageRooms);
	}

	/**
	 * Start a chatting room. If the room does not exists, then create one. Otherwise, return the existing one
	 * @param Request $request
	 */
	public function joinRoom(Request $request)
	{
		$from = $this->getUser($request);
		$message_to = User::findById($request->input('messageTo'));
		if ($from->id == $message_to) {
			// User is not supposed to send messages to itself
			throw new YechefException(22500);
		}
		$myMessageRooms = $from->messageRooms()->pluck('message_room_id')->toArray();
		$ourMessageRoom = $message_to->messageRooms()->find($myMessageRooms);
		if ($ourMessageRoom->isEmpty()) {
			// No Message Room exists for the user and the kitchen owner.. create one
			$ourMessageRoom = $this->messageService->createMessageRoom($request, $from);
		}
		return response()->success($ourMessageRoom);
	}

	/**
	 * Send a message in the correct chatting room
	 * @param Request $request
	 */
	public function sendMessage(Request $request)
	{
		$from = $this->getUser($request);
		$validationRule = Message::getValidationRule();
		$this->validateInput($request, $validationRule);
		$messageRoomId = $request->input('messageRoomId');
		$message = Message::create([
			'user_id'         => $from->id,
			'message_body'    => $request->input('messageBody'),
			'message_room_id' => $messageRoomId
		]);
		$message_to = $this->messageService->findRecipient($messageRoomId, $from);
		event(new MessageSent($from, $message_to, $message));
		return response()->success($message, 22000);
	}

	/**
	 * Delete a message sent.
	 * @param Request $request
	 */
	public function destroy(Request $request, $id)
	{
		$user = $this->getUser($request);
		$message = Message::findById($id);
		if ($message->user()->where('users.id', $user->id)->get()->isEmpty()) {
			throw new YechefException(22501);
		}
		$message->delete();
		return response()->success($message, 22001);
	}
}