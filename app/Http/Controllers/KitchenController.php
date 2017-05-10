<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Yechef\Helper;
use App\Models\Kitchen;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Redirect;
use Illuminate\Support\Facades\Validator;

class KitchenController extends Controller
{
	/**
	 * Display a listing of the resource.
	 *
	 * @return \Illuminate\Http\Response
	 */
	public function index(Request $request)
	{
		Log::info('index');
		$kitchen = Kitchen::with('media')->get();
		// apply pagination
		$result = Helper::paginate($request, $kitchen);
		return response()->success($result);
	}

	/**
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		// validation
		$rules = array(
			'name'    => 'required',
			'email'   => 'required',
			'phone'   => 'required',
			'address' => 'required',
		);
		$validator = Validator::make($request->all(), $rules);

		// process the login
		if ($validator->fails()) {
			return Redirect::to('/kitchens/list')
				->withErrors($validator);
		} else {
			// store
			$kitchen = new Kitchen;
			$kitchen->name = $request->input('name');
			$kitchen->email = $request->input('email');
			$kitchen->phone = $request->input('phone');
			$kitchen->address = $request->input('address');
			$kitchen->description = $request->input('description');
			$kitchen->save();

			return response()->success($kitchen);
		}
	}

	/**
	 * Display the specified resource.
	 *
	 * @param  int $id
	 * @return \Illuminate\Http\Response
	 */
	public function show($id)
	{
		Log::info('show');
		$kitchen = Kitchen::with('media')->find($id);
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
		$input = $request->all();
		$kitchen = Kitchen::find($id);
		$kitchen->update($input);
		$kitchen = Kitchen::find($id);
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
		return Kitchen::where('id', $id)->delete();
	}
}
