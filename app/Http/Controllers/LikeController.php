<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use App\Models\User;
use App\Models\Like;
use App\Models\Kitchen;
use Illuminate\Support\Facades\Log;

class LikeController extends Controller
{

	private $validator;

	/**
	 * LikeController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	/**
	 * @param Request $request
	 */
	public function index(Request $request)
	{
		Log::info('index called');
		$likableId = $request->input('likableId');
		$userId = $request->input('userId');

		$likable = Kitchen::findKitchen(1);
		$likableType = get_class($likable);

		$reactions = Like::where('likable_type', $likableType)
			->where('likable_id', $likableId)
			->get();

		$userReaction = $reactions->where('user_id', $userId)->first();
		$userReactionId = $userReaction ? $userReaction->id : null;
		$userReactionKind = $userReaction ? $userReaction->isLike : null;

		$numLikes = $reactions->where('isLike', 1)->count();
		$numDislikes = $reactions->where('isLike', 0)->count();

		$reactionResponse = (object)array(
			'numLikes'         => $numLikes,
			'numDislikes'      => $numDislikes,
			'userReactionId'   => $userReactionId,
			'userReactionKind' => $userReactionKind
		);

		return response()->success($reactionResponse, 14002);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function store(Request $request)
	{
		$this->validateInput($request);

		// TODO: placeholder until registration is implemented
		$userId = $request->input('userId');
		$likableId = $request->input('likableId');
		$likable = Kitchen::findKitchen($likableId);
		$likableType = get_class($likable);

		// delete existing reaction
		$oldReactions = Like::where('user_id', $userId)
			->where('likable_type', $likableType)
			->where('likable_id', $likableId)
			->get();

		//$oldReactions must be singular. double check it
		if (count($oldReactions) > 1) {
			throw new YechefException($oldReactions, 14502);
		}

		$oldReaction = $oldReactions->first();
		$oldReactionKind = $oldReaction ? $oldReaction->isLike: null;
		!$oldReaction ?: $oldReaction->delete();

		// add new reaction
		$newReaction = new Like;
		$newReaction->isLike = $request->input('isLike');
		$newReaction->user_id = $userId;

		// associate polymorphic relationship
		$likable->likes()->save($newReaction);
		$newReaction->save();

		// send deleted reaction to frontend to reflect the change on view
		$newReaction->oldReactionKind = $oldReactionKind;

		return response()->success($newReaction, 14000);
	}

	/**
	 * TODO: we don't use $reactionId for now until registration branch is in
	 * @param $reactionId
	 * @return mixed
	 */
	public function destroy(Request $request, $reactionId)
	{
		$likableId = $request->input('likableId');
		$userId = $request->input('userId');
		$likable = Kitchen::findKitchen($likableId);
		$likableType = get_class($likable);

		$reaction = Like::where('user_id', $userId)
			->where('likable_type', $likableType)
			->where('likable_id', $likableId)
			->first();
		$reaction->delete();

		return response()->success($reaction, 14001);
	}

	/**
	 * @param Request $request
	 * @throws YechefException
	 */
	private function validateInput(Request $request)
	{
		$validationRule = Like::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(14500);
		}
	}
}
