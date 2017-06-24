<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use Session;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Dish;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Application;
use App\Exceptions\YechefException;

class CartController
{

	private $validator;
	private $cart;

	/**
	 * KitchenController constructor.
	 * @param Application $app
	 */
	public function __construct(Request $request, Application $app)
	{
		$this->validator = $app->make('validator');

		$user = $request->user();

		$this->cart = $user->getCart();
	}

	/**
	 * Retrieve signed-in user's shopping cart
	 */
	public function index()
	{
		return response()->success($this->cart);
	}

	/**
	 * Add a new item to the cart
	 *
	 * @param Request $request
	 * @return \Illuminate\Http\Response
	 */
	public function store(Request $request)
	{
		$this->validateInput($request);

		// create a cart item
		$this->cart->items()->save(
			CartItem::create([
				'dish_id'  => $request->input('dish_id'),
				'quantity' => $request->input('quantity'),
				'price'    => $request->input('price'),
			])
		);

		return response()->success($this->cart, 18000);
	}

	/**
	 * Update the existing cart item's quantity
	 *
	 * @param Request $request
	 * @param $dishId
	 * @return mixed
	 */
	public function update(Request $request, $dishId)
	{
		$this->validateInput($request);

		$item = $this->cart->findItemByDish($dishId);

		$item->update(
			[
				'quantity' => $request->input('quantity'),
				'price'    => $request->input('price')
			]
		);

		return response()->success($item, 18001);
	}

	/**
	 * Remove the item from the cart
	 *
	 * @param  int $dishId
	 * @return \Illuminate\Http\Response
	 */
	public function destroy($dishId)
	{
		$item = $this->cart->findItemByDish($dishId);
		$item->delete();

		return response()->success($item, 18002);
	}


	/**
	 * Validate Cart Item Inputs
	 *
	 * @param Request $request
	 * @throws YechefException
	 */
	private function validateInput(Request $request)
	{
		$validationRule = CartItem::getValidationRule();
		$validator = $this->validator->make($request->all(), $validationRule);

		if ($validator->fails()) {
			throw new YechefException(18501);
		}
	}

}