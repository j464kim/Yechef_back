<?php

namespace App\Services;


use App\Models\MessageRoom;
use App\Models\User;
use Illuminate\Http\Request;

/**
 * Class MessageService
 * @package App\Services
 */
class MessageService
{

	/**
	 * @param Request $request
	 * @param $from
	 * @return mixed
	 */
	public function createMessageRoom(Request $request, $from)
	{
		$ourMessageRoom = MessageRoom::create();
		$message_to = User::findById($request->input('messageTo'));
		$message_to->messageRooms()->save($ourMessageRoom);
		$from->messageRooms()->save($ourMessageRoom);
		return $ourMessageRoom;
	}

	/**
	 * @param $messageRoomId
	 * @param $from
	 * @return mixed
	 */
	public function findRecipient($messageRoomId, $from)
	{
		return MessageRoom::find($messageRoomId)->users()->where('users.id', '!=', $from->id)->first();
	}
}