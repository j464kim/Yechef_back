<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use App\Models\Reaction;
use Illuminate\Support\Facades\Log;

class ReactionController extends Controller
{
	private $validator;

	/**
	 * ReactionController constructor.
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
		$reactionableId = $request->input('reactionableId');
		$reactionableType = $request->input('reactionableType');
		$userId = $request->input('userId');

		try {
			$reactionable = $reactionableType::findOrFail($reactionableId);
		} catch (\Exception $e) {
			throw new YechefException(14503);
		}

		$reactions = $reactionable->getReactions();

		$userReaction = null;
		if ($userId){
			$userReactions = $reactionable->getReactions($userId);
			$userReaction = $userReactions->first();
		}

		$userReactionId = $userReaction ? $userReaction->id : null;
		$userReactionKind = $userReaction ? $userReaction->kind : null;

		$numLikes = $reactions->where('kind', Reaction::LIKE)->count();
		$numDislikes = $reactions->where('kind', Reaction::DISLIKE)->count();
		$numForks = $reactions->where('kind', Reaction::FORK)->count();
		$numSubscibes = $reactions->where('kind', Reaction::SUBSCRIBE)->count();

		$reactionResponse = (object)array(
			'userReactionId'   => $userReactionId,
			'userReactionKind' => $userReactionKind,
			'numLikes'         => $numLikes,
			'numDislikes'      => $numDislikes,
			'numForks'         => $numForks,
			'numSubscribes'    => $numSubscibes,
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
		$reactionableId = $request->input('reactionableId');
		$reactionableType = $request->input('reactionableType');
		$userId = $request->user()->id;

		try {
			$reactionable = $reactionableType::findOrFail($reactionableId);
		} catch (\Exception $e) {
			throw new YechefException(14503);
		}

		// get existing reactions to be deleted
		$oldReactions = $reactionable->getReactions($userId);

		//$oldReactions must be singular. double check it
		if (count($oldReactions) > 1) {
			throw new YechefException($oldReactions, 14502);
		}

		// add new reaction
		$newReaction = new Reaction;
		$newReaction->kind = $request->input('kind');
		$newReaction->user_id = $userId;

		// associate polymorphic relationship
		$reactionable->reactions()->save($newReaction);
		$newReaction->save();

		if (count($oldReactions)) {
			$oldReaction = $oldReactions->first();
			$oldReaction->delete();
			$oldReactionKind = $oldReaction->kind;

			// send deleted reaction to frontend to reflect the change on view
			$newReaction->oldReactionKind = $oldReactionKind;
		}

		return response()->success($newReaction, 14000);
	}

	/**
	 * TODO: we don't use $reactionId for now until registration branch is in
	 * @param $reactionId
	 * @return mixed
	 */
	public function destroy(Request $request, $reactionId)
	{
		$reactionableId = $request->input('reactionableId');
		$reactionableType = $request->input('reactionableType');
		$userId = $request->user()->id;

		try {
			$reactionable = $reactionableType::findOrFail($reactionableId);
		} catch (\Exception $e) {
			throw new YechefException(14503);
		}

		$userReaction = $reactionable->getReactions($userId);
		$userReaction->first()->delete();

		return response()->success($userReaction, 14001);
	}

	/**
	 * @param Request $request
	 * @throws YechefException
	 */
	private function validateInput(Request $request)
	{
		$validationRule = Reaction::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(14500);
		}
	}
}
