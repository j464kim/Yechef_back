<?php

namespace App\Http\Controllers;

use App\Events\ReactionableDeleted;
use App\Exceptions\YechefException;
use App\Models\Kitchen;
use App\Models\User;
use App\Yechef\Helper;
use Illuminate\Contracts\Foundation\Application;
use Illuminate\Http\Request;

class KitchenController extends Controller
{

	private $validator;
	private $auth;
	private $user;

	/**
	 * KitchenController constructor.
	 * @param Application $app
	 */
	public function __construct(Application $app)
	{
		$this->validator = $app->make('validator');
		$this->auth = $app->make('auth');
		//TODO: Get User Dynamically
		$this->user = User::first();
//		$this->user = $this->auth->user();
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
		$this->validateInput($request);

		$kitchen = Kitchen::create([
			'name'        => snake_case($request->input('name')),
			'email'       => $request->input('email'),
			'phone'       => $request->input('phone'),
			'address'     => $request->input('address'),
			'description' => $request->input('description'),
		]);

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
		$this->validateInput($request);

		$kitchen = Kitchen::findKitchen($id, true);

		$kitchen->update(
			[
				'name'        => snake_case($request->input('name')),
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

	private function validateInput(Request $request)
	{
		$validationRule = Kitchen::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(12500);
		}
	}
}
