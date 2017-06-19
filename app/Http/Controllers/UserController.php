<?php

namespace App\Http\Controllers;

use App\Models\Dish;
use App\Models\User;
use App\Models\Reaction;
use App\Models\Kitchen;
use Illuminate\Http\Request;
use App\Exceptions\YechefException;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;


class UserController extends Controller
{

	/**
	 * UserController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getMyKitchens(Request $request)
	{
		$user = $request->user();
		try {
			$result = $user->kitchens()->with('medias')->get();
		} catch (Exception $e) {
			return response()->fail($e->getMessage());
		}
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getLoggedInUser(Request $request)
	{
		$user = $request->user();
		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function index()
	{
		$result = User::all();
		return response()->success($result);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function show($id)
	{
		$user = User::findUser($id);

		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return mixed
	 */
	public function update(Request $request, $id)
	{
		$this->validateInput($request, $id);

		$user = User::findUser($id);

		$user->update(
			[
				'first_name' => $request->input('first_name'),
				'last_name'  => $request->input('last_name'),
				'phone'      => $request->input('phone'),
			]
		);

		return response()->success($user, 15001);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getSubscriptions(Request $request)
	{
		$user = $request->user();
		$subscriptionKitchens = Kitchen::with('medias')
			->join('reactions', 'reactions.reactionable_id', '=', 'kitchens.id')
			->where('user_id', $user->id)
			->where('kind', Reaction::SUBSCRIBE)
			->select('kitchens.*')
			->get();

		return response()->success($subscriptionKitchens);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getForks(Request $request)
	{
		$user = $request->user();
		$forkedDishes = Dish::with('medias')
			->join('reactions', 'reactions.reactionable_id', '=', 'dishes.id')
			->where('user_id', $user->id)
			->where('kind', Reaction::FORK)
			->select('dishes.*')
			->get();

		return response()->success($forkedDishes);
	}

	/**
	 * @param Request $request
	 */
	private function validateInput(Request $request, $id)
	{
		$validationRule = User::getValidationRule($id);
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			$message = '';
			foreach ($validator->errors()->all() as $error) {
				$message .= "\r\n" . $error;
			}
			throw new YechefException(15502, $message);
		}
	}
}
