<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Models\Kitchen;
use App\Models\User;
use App\Yechef\Helper;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;

class KitchenController extends Controller
{

	private $validator;

	/**
	 * KitchenController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
	}


	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$kitchens = Kitchen::with('medias')->get();
		// apply pagination
		$result = Helper::paginate($request, $kitchens);
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$user = $request->user();
		$this->validateInput($request);

		$kitchen = Kitchen::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'email'       => $request->input('email'),
			'phone'       => $request->input('phone'),
			'address'     => $request->input('address'),
			'description' => $request->input('description'),
		]);
		$kitchen->users()->save($user, ['role' => 1, 'verified' => true]);

		return response()->success($kitchen);
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		$kitchen = Kitchen::findKitchen($id, true);

		return response()->success($kitchen);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */

	public function update(Request $request, $id)
	{
		Helper::checkKitchenAccess($request, $id);
		$this->validateInput($request);

		$kitchen = Kitchen::findKitchen($id, true);

		$kitchen->update(
			[
				'slug'        => snake_case($request->input('name')),
				'name'        => $request->input('name'),
				'email'       => $request->input('email'),
				'phone'       => $request->input('phone'),
				'address'     => $request->input('address'),
				'description' => $request->input('description')
			]
		);

		return response()->success($kitchen);
	}

	/**
	 * Remove the specified resource from storage.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $id)
	{
		Helper::checkKitchenAccess($request, $id);
		$kitchen = Kitchen::findKitchen($id);
		$kitchen->delete();

		event(new ReactionableDeleted($kitchen));

		return response()->success(12002);
	}

	public function getAdmins($id)
	{
		$admins = Kitchen::findKitchen($id, false)->users;
		return response()->success($admins);
	}

	public function addAdmin(Request $request, $id)
	{
		$userId = $this->getUserId($request);
		Helper::checkKitchenAccess($request, $id);
		$kitchen = Kitchen::findKitchen($id);
		$user = User::findUser($userId);
		$admin = $kitchen->users()->where('user_id', $userId)->first();
		if (!$admin) {
			$kitchen->users()->save($user, ['verified' => false, 'role' => 1]);
		} else {
			throw new YechefException(12502);
		}
		return response()->success($user);
	}

	public function removeAdmin(Request $request, $id)
	{
		Helper::checkKitchenAccess($request, $id);
		$userId = $this->getUserId($request);
		$kitchen = Kitchen::findKitchen($id);
		$admin = $kitchen->users()->where('user_id', $userId)->first();
		if ($admin) {
			$kitchen->users()->detach($userId);
			return response()->success($admin);
		} else {
			throw new YechefException(12503);
		}
	}

	public function getDishes(Request $request, $id)
	{
		$kitchen = Kitchen::findKitchen($id);
		$dishes = $kitchen->dishes()->with('medias')->get();
		return response()->success($dishes);
	}

	public function getSubscribers(Request $request, $id)
	{
		$kitchen = Kitchen::findKitchen($id);
		$subscribers = $kitchen->reactions()->where('kind', 3)->pluck('user_id')->toArray();
		$subscribers = User::findMany($subscribers);
		return response()->success($subscribers);
	}

	private function getUserId(Request $request)
	{
		$currentUser = $request->user();
		$userId = $request->input('user_id');
		if ($userId === $currentUser->id) {
			throw new YechefException(12504);
		} else {
			return $userId;
		}
	}

	private function validateInput(Request $request, $id)
	{
		$validationRule = Kitchen::getValidationRule($id);
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(12500);
		}
	}
}
