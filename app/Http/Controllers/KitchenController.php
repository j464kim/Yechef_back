<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Yechef\Helper;
use App\Models\Kitchen;
use Illuminate\Support\Facades\Log;

class KitchenController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        $kitchen = Kitchen::with('media')->get();
        // apply pagination
        $result = Helper::paginate($request, $kitchen);
		Log::info('index');
		return response()->success($result);
    }

    /**
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $input = $request->all();
        $newKitchen = Kitchen::create($input);
        return response()->success($newKitchen);
    }

    /**
     * Display the specified resource.
     *
     * @param  int $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $kitchen = Kitchen::with('media')->find($id);
		Log::info('show');
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
