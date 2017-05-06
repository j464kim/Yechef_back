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

    public function store(Request $request) {
        //TODO: Dish hasMany(media) VS Media belongsToMany(dish)?
        $dish = new Dish();
        $dish->slug = $request->slug;
        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->save();
    }

    public function update(Request $request, $id) {
        $dish = Dish::find($id);
        $dish->slug = $request->slug;
        $dish->name = $request->name;
        $dish->description = $request->description;
        $dish->save();
    }

    public function destroy(Request $request, $id) {
        $dish = Dish::find($id);
        $dish->delete();
    }
}
