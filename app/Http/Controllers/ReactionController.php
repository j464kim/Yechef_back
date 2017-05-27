<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use Illuminate\Contracts\Foundation\Application;
use App\Models\User;
use App\Models\Reaction;
use App\Models\Kitchen;
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
		Log::info('index called');
		$reactionableId = $request->input('reactionableId');
		$userId = $request->input('userId');

		$reactionable = Kitchen::findKitchen(1);
		$reactionableType = get_class($reactionable);

		$reactions = Reaction::where('reactionable_type', $reactionableType)
			->where('reactionable_id', $reactionableId)
			->get();

		$userReaction = $reactions->where('user_id', $userId)->first();
		$userReactionId = $userReaction ? $userReaction->id : null;
		$userReactionKind = $userReaction ? $userReaction->kind : null;

		$numLikes = $reactions->where('kind', 1)->count();
		$numDislikes = $reactions->where('kind', 0)->count();

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
		$reactionableId = $request->input('reactionableId');
		$reactionable = Kitchen::findKitchen($reactionableId);
		$reactionableType = get_class($reactionable);

		// delete existing reaction
		$oldReactions = Reaction::where('user_id', $userId)
			->where('reactionable_type', $reactionableType)
			->where('reactionable_id', $reactionableId)
			->get();

		//$oldReactions must be singular. double check it
		if (count($oldReactions) > 1) {
			throw new YechefException($oldReactions, 14502);
		}

		$oldReaction = $oldReactions->first();
		$oldReactionKind = $oldReaction ? $oldReaction->kind : null;
		!$oldReaction ?: $oldReaction->delete();

		// add new reaction
		$newReaction = new Reaction;
		$newReaction->kind = $request->input('kind');
		$newReaction->user_id = $userId;

		// associate polymorphic relationship
		$reactionable->reactions()->save($newReaction);
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
		$reactionableId = $request->input('reactionableId');
		$userId = $request->input('userId');
		$reactionable = Kitchen::findKitchen($reactionableId);
		$reactionableType = get_class($reactionable);

		$reaction = Reaction::where('user_id', $userId)
			->where('reactionable_type', $reactionableType)
			->where('reactionable_id', $reactionableId)
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
		$validationRule = Reaction::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(14500);
		}
	}
}
