<?php

namespace App\Yechef;

use App\Exceptions\YechefException;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

class Helper
{
	public static function paginate(Request $request, $items, $perPage = 8)
	{
		//Get current page form url e.g. &page=1
		$currentPage = LengthAwarePaginator::resolveCurrentPage();

		//Slice the collection to get the items to display in current page
		$currentPageItems = $items->slice(($currentPage - 1) * $perPage, $perPage);

		//Create our paginator and pass it to the view
		return new LengthAwarePaginator($currentPageItems, count($items), $perPage, $currentPage, [
			// 'path' => Paginator::resolveCurrentPath()
			'path'  => $request->url(),
			'query' => $request->query(),
		]);
	}

	public static function checkKitchenAccess(Request $request, $kitchenId)
	{
		$currentUser = $request->user();
		$exists = $currentUser->kitchens()->wherePivot('kitchen_id', $kitchenId)->wherePivot('verified', true);
		if (!$exists) {
			throw new YechefException(12505);
		}
	}

}