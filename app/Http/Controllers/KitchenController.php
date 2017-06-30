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
		$validationRule = Kitchen::getValidationRule();
		$this->validateInput($request, $validationRule);
		$user = $this->getUser($request);

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
		$validationRule = Kitchen::getValidationRule($id);
		$this->validateInput($request, $validationRule);

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
	public function destroy($id)
	{
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

	private function getUserId(Request $request)
	{
		$user = $this->getUser($request);
		$userId = $request->input('user_id');
		if ($userId === $user->id) {
			throw new YechefException(12500);
		} else {
			return $userId;
		}
	}

}
