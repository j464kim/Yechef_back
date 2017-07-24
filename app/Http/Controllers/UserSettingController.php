<?php

namespace App\Http\Controllers;

use App\Models\UserSetting;
use Illuminate\Http\Request;

class UserSettingController extends Controller
{

	/**
	 * Display the specified resource.
	 *
	 * @param  \App\Models\UserSetting $userSetting
	 * @return \Illuminate\Http\Response
	 */
	public function show(Request $request)
	{
		$user = $request->user();
		$result = $user->setting;
		return response()->success($result);
	}

	/**
	 * Update the specified resource in storage.
	 *
	 * @param  \Illuminate\Http\Request $request
	 * @param  \App\Models\UserSetting $userSetting
	 * @return \Illuminate\Http\Response
	 */
	public function update(Request $request)
	{
		//
		$validationRule = UserSetting::getValidationRule($id);
		$this->validateInput($request, $validationRule);
		$user = $request->user();
		$setting = $user->setting;
		$result = $setting->update([
			'show_phone'        => $request->input('show_phone'),
			'show_forks'        => $request->input('show_forks'),
			'show_subscription' => $request->input('show_subscription'),
		]);
		return response()->success($result);
	}
}
