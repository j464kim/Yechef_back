<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Dish;
use App\Models\Media;
use App\Yechef\Helper;

class DishController extends Controller
{
    public function index(Request $request) {
    	$dish = Dish::with('media')->get();
    	// apply pagination
		$result = Helper::paginate($request, $dish);
    	return response()->success($result);
    }
}
