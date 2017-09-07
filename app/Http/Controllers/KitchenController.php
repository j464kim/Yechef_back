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

class KitchenController extends Controller
{
	private $payoutCtrl;
	protected $kitchen, $user;

	function __construct(Application $app, PayoutController $payoutCtrl, Kitchen $kitchen, User $user)
	{
		parent::__construct($app);

		$this->payoutCtrl = $payoutCtrl;
		$this->kitchen = $kitchen;
		$this->user = $user;
	}

	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		$kitchens = $this->kitchen->with('medias')->get();
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
		$validationRule = $this->kitchen->getValidationRule();
		$this->validateInput($request, $validationRule);
		$user = $this->getUser($request);

		// create payout account if owner of kitchen doesn't have a payout method yet
		$this->payoutCtrl->store($request);

		$kitchen = $this->kitchen->create([
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
		$kitchen = $this->kitchen->findById($id, true);
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

		$validationRule = $this->kitchen->getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$kitchen = $this->kitchen->findById($id, true);

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
		$kitchen = $this->kitchen->findById($id);
		$kitchen->delete();

		event(new ReactionableDeleted($kitchen));

		return response()->success(12002);
	}

	public function getAdmins($id)
	{
		$admins = $this->kitchen->findById($id, false)->users->load('medias');
		return response()->success($admins);
	}

	public function addAdmin(Request $request, $id)
	{
		$request->user()->isVerifiedKitchenOwner($id);

		$userId = $this->getUserId($request);
		$kitchen = $this->kitchen->findById($id);
		$user = $this->user->findById($userId);
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
		$kitchen = $this->kitchen->findById($id);
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
		$kitchen = $this->kitchen->findById($id);
		$dishes = $kitchen->dishes()->with('medias')->get();
		foreach ($dishes as $dish) {
			$dish->addRatingAttributes();
		}
		return response()->success($dishes);
	}

	public function getSubscribers($id)
	{
		$kitchen = $this->kitchen->findById($id);
		$subscribers = $kitchen->reactions()->where('kind', 3)->pluck('user_id')->toArray();
		$subscribers = $this->user->findMany($subscribers);
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
		$kitchen = $this->kitchen->findById($kitchenId);

		$orderInfo = $kitchen->orders()->with('items.dish', 'items.dishRating', 'kitchen', 'transaction')->get();
		$orderInfo->map(function ($eachOrder) {
			$eachOrder->user_name = $this->user->findById($eachOrder->user_id)->first_name;
		});

		return response()->success($orderInfo);
	}

	public function toggleBusinessHour(Request $request, $kitchenId)
	{
		$kitchen = $this->kitchen->findById($kitchenId);
		$businessHour = $kitchen->getBusinessHourByDay($request->input('day'));

		$businessHour->update(
			[
				'active' => $request->input('active'),
			]
		);

		return response()->success($businessHour);
	}

	public function updateBusinessHour(Request $request, $kitchenId)
	{
		$kitchen = $this->kitchen->findById($kitchenId);
		$businessHour = $kitchen->getBusinessHourByDay($request->input('day'));

		$businessHour->update(
			[
				'open_time'  => $request->input('open'),
				'close_time' => $request->input('close')
			]
		);

		return response()->success($businessHour);
	}

	public function getBusinessHour($kitchenId)
	{
		$kitchen = $this->kitchen->findById($kitchenId);
		$businessHours = $kitchen->businessHours()->get();

		return response()->success($businessHours);
	}

}
