<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Models\Dish;
use App\Models\User;
use App\Yechef\Helper;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;


/**
 * Class UserController
 * @package App\Http\Controllers
 */
class UserController extends Controller
{
	protected $user;

	public function __construct(Application $app, User $user)
	{
		parent::__construct($app);
		$this->user = $user;
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getKitchens(Request $request)
	{
		$user = $this->getUser($request);

		try {
			$result = $user->kitchens()->with('medias')->get();
		} catch (\Exception $e) {
			return response()->fail($e->getMessage());
		}
		$result = Helper::paginate($request, $result, $request->perPage);
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 */
	public function getMyKitchensInCompactList(Request $request)
	{
		$user = $this->getUser($request);

		try {
			$result = $user->kitchens()->get(['kitchen_id as id', 'name']);
		} catch (\Exception $e) {
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
		$user = $this->getUser($request)->load('medias');

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

	public function checkOwnership(Request $request)
	{
		$user = $this->getUser($request);

		if ($dishId = $request->input('dish_id')) {
			$dish = Dish::findById($dishId);
			$kitchenId = $dish->kitchen_id;
		} else {
			$kitchenId = $request->input('kitchen_id');
		}

		try {
			$user->isVerifiedKitchenOwner($kitchenId);
		} catch (YechefException $e) {
			return response()->notallow($e->getMessage());
		}

		return response()->success();
	}

	public function checkPayout(Request $request)
	{
		$user = $this->getUser($request);
		return response()->success($user->payoutAccount);
	}

	/**
	 * @param $id
	 * @return mixed
	 */
	public function show($id)
	{
		$user = User::findById($id, true);
		if (!$user->setting->show_phone) {
			$user->phone = '';
		}

		return response()->success($user);
	}

	/**
	 * @param Request $request
	 * @param $id
	 * @return mixed
	 */
	public function update(Request $request, $id)
	{
		$validationRule = User::getValidationRule($id);
		$this->validateInput($request, $validationRule);

		$user = User::findById($id);

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
		$user = $this->getUser($request);

		//Check user's privacy settings
		// userId is required to determine if the request is coming from MyProfile page or User Show Page.
		if ($request->userId && $user->setting->show_subscription == 0) {
			$result = null;
		} else {
			$subscriptionKitchens = $user->getSubscriptions();
			$result = Helper::paginate($request, $subscriptionKitchens, $request->perPage);

		}
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getForkedDishes(Request $request)
	{
		$user = $this->getUser($request);

		//Check user's privacy settings
		// userId is required to determine if the request is coming from MyProfile page or User Show Page.
		if ($request->userId && $user->setting->show_forks == 0) {
			$result = null;
		} else {
			$forkedDishes = $user->getForkedDishes();
			$result = Helper::paginate($request, $forkedDishes, $request->perPage);
		}
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return mixed
	 */
	public function getOrders(Request $request)
	{
		$user = $this->getUser($request);

		$orderInfo = $user->orders()->with([
			'items.dishRating' => function ($query) {
				$query->withTrashed();
			},
			'items.dish',
			'kitchen',
			'transaction'
		])->get();

		return response()->success($orderInfo);
	}
}
