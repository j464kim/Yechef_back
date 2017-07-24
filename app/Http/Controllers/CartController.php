<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\Log;
use App\Models\Cart;
use App\Models\CartItem;
use App\Models\Dish;
use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Foundation\Application;
use App\Exceptions\YechefException;

class CartController extends Controller
{
	private $cart;

	public function retrieveCart(Request $request, $kitchenId = null)
	{
		$user = $this->getUser($request);
		$this->cart = $user->getCart($kitchenId);
	}

	/**
	 * Retrieve signed-in user's shopping cart
	 */
	public function index(Request $request)
	{
		$this->retrieveCart($request);

		if (!$this->cart) {
			return response()->success(18003);
		}

		foreach($this->cart as $cart) {
			$items = $cart->items;
			foreach ($items as $item) {
				$dish = Dish::findById($item->dish_id);
				$item->id = $dish->id;
				$item->name = $dish->name;
				$item->eachPrice = $dish->price;
				$item->kitchenId = $dish->kitchen_id;
			}
		}

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
		$validationRule = CartItem::getValidationRule(false);
		$this->validateInput($request, $validationRule);

		$dish = Dish::findById($request->input('dish_id'));
		$this->retrieveCart($request, $dish->kitchen_id);

		// create a cart item
		$cartItem = CartItem::create(
			array(
				"cart_id" => $this->cart->id,
				"dish_id" => $request->input('dish_id'),
				"quantity" => $request->input('quantity'),
			)
		);

		$this->cart->kitchen_id = $dish->kitchen_id;
		$this->cart->save();

		return response()->success($cartItem, 18000);
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
		$validationRule = CartItem::getValidationRule(isset($dishId));
		$this->validateInput($request, $validationRule);

		$dish = Dish::findById($dishId);
		$this->retrieveCart($request, $dish->kitchen_id);

		$item = $this->cart->findItemByDish($dishId);
		$item->quantity = $request->input('quantity');
		$item->save();

		return response()->success($item, 18001);
	}

	/**
	 * Remove the item from the cart
	 *
	 * @param  int $dishId
	 * @return \Illuminate\Http\Response
	 */
	public function destroy(Request $request, $dishId)
	{
		$dish = Dish::findById($dishId);
		$this->retrieveCart($request, $dish->kitchen_id);

		$item = $this->cart->findItemByDish($dishId);
		$item->delete();

		// if there is no more item in cart, remove it
		if ($this->cart->items->isEmpty()) {
			$this->cart->delete();
		}

		return response()->success($item, 18002);
	}

}