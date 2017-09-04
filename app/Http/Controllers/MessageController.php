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

	public function index(Request $request)
	{
		$user = $this->getUser($request);
		$messageRoom = $user->messageRooms()->find($request->messageRoomId);
		if (!$messageRoom) {
			return response()->notallow();
		}
		$messages = $messageRoom->messages()->with([
			'user' => function ($q) {
				$q->with('medias');
			}
		])->get();
		return response()->success($messages);
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
					$q->with('user')->orderBy('created_at', 'desc')->first();
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
		if ($from->id == $message_to->id) {
			// User is not supposed to send messages to itself
			throw new YechefException(22500);
		}
		$myMessageRooms = $from->messageRooms()->pluck('message_room_id')->toArray();
		$ourMessageRoom = $message_to->messageRooms()->find($myMessageRooms);
		if ($ourMessageRoom->isEmpty()) {
			// No Message Room exists for the user and the kitchen owner.. create one
			$ourMessageRoom = $this->messageService->createMessageRoom($request, $from);
		} else {
			$ourMessageRoom = $ourMessageRoom->first();
		}
		return response()->success($ourMessageRoom);
	}
}