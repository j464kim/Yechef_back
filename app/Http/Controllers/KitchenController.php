<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Http\Controllers\Payment\PayoutController;
use App\Models\Kitchen;
use App\Models\User;
use App\Yechef\Helper;
use Illuminate\Http\Request;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Log;

class KitchenController extends Controller
{
	private $payoutCtrl;

	function __construct(Application $app, PayoutController $payoutCtrl)
	{
		parent::__construct($app);

		$this->payoutCtrl = $payoutCtrl;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$kitchens = Kitchen::with('medias')->get();
		foreach ($kitchens as $kitchen) {
			$kitchen->addRatingAttributes();
		}
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

		// create payout account if owner of kitchen doesn't have a payout method yet
		$this->payoutCtrl->store($request);

		$kitchen = Kitchen::create([
			'slug'        => snake_case($request->input('name')),
			'name'        => $request->input('name'),
			'country'     => $request->input('country'),
			'email'       => $request->input('email'),
			'phone'       => $request->input('phone'),
			'address'     => $request->input('address'),
			'description' => $request->input('description'),
			'lat'         => $request->input('lat'),
			'lng'         => $request->input('lng')
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
		$kitchen = Kitchen::findById($id, true);
		$kitchen->addRatingAttributes();
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
		$request->user()->isVerifiedKitchenOwner($id);

		$validationRule = Kitchen::getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$kitchen = Kitchen::findById($id, true);

		$kitchen->update(
			[
				'slug'        => snake_case($request->input('name')),
				'name'        => $request->input('name'),
				'email'       => $request->input('email'),
				'phone'       => $request->input('phone'),
				'address'     => $request->input('address'),
				'description' => $request->input('description'),
				'lat'         => $request->input('lat'),
				'lng'         => $request->input('lng')
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
		$request->user()->isVerifiedKitchenOwner($id);
		$kitchen = Kitchen::findById($id);
		$kitchen->delete();

		event(new ReactionableDeleted($kitchen));

		return response()->success(12002);
	}

	public function getAdmins($id)
	{
		$admins = Kitchen::findById($id, false)->users->load('medias');
		return response()->success($admins);
	}

	public function addAdmin(Request $request, $id)
	{
		$request->user()->isVerifiedKitchenOwner($id);

		$userId = $this->getUserId($request);
		$kitchen = Kitchen::findById($id);
		$user = User::findById($userId);
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
		$request->user()->isVerifiedKitchenOwner($id);
		$userId = $this->getUserId($request);
		$kitchen = Kitchen::findById($id);
		$admin = $kitchen->users()->where('user_id', $userId)->first();
		if ($admin) {
			$kitchen->users()->detach($userId);
			return response()->success($admin);
		} else {
			throw new YechefException(12503);
		}
	}

	public function getDishes($id)
	{
		$kitchen = Kitchen::findById($id);
		$dishes = $kitchen->dishes()->with('medias')->get();
		foreach ($dishes as $dish) {
			$dish->addRatingAttributes();
		}
		return response()->success($dishes);
	}

	public function getSubscribers($id)
	{
		$kitchen = Kitchen::findById($id);
		$subscribers = $kitchen->reactions()->where('kind', 3)->pluck('user_id')->toArray();
		$subscribers = User::findMany($subscribers);
		return response()->success($subscribers);
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

	public function getOrders($kitchenId)
	{
		$kitchen = Kitchen::findById($kitchenId);

		$orderInfo = $kitchen->orders()->with('items.dish', 'items.dishRating', 'kitchen', 'transaction')->get();
		$orderInfo->map(function ($eachOrder) {
			$eachOrder->user_name = User::findById($eachOrder->user_id)->first_name;
		});

		return response()->success($orderInfo);
	}

	public function updateBusinessHour(Request $request, $kitchenId)
	{
		$kitchen = Kitchen::findById($kitchenId);
		Log::info('Update Business Hours' . $kitchenId);
	}
}
