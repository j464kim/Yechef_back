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

	public function initialize(Request $request)
	{
		$user = $this->getUser($request);
		$this->cart = $user->getCart();
	}

	/**
	 * Retrieve signed-in user's shopping cart
	 */
	public function index(Request $request)
	{
		$this->initialize($request);

		$items = $this->cart->items;
		foreach ($items as $item) {
			$dish = Dish::findById($item->dish_id);
			$item->id = $dish->id;
			$item->name = $dish->name;
			$item->eachPrice = $dish->price;
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
		$this->initialize($request);

		$validationRule = CartItem::getValidationRule(false);
		$this->validateInput($request, $validationRule);

		// create a cart item
		$cartItem = new CartItem;
		$cartItem->dish_id = $request->input('dish_id');
		$cartItem->quantity = $request->input('quantity');
		$this->cart->items()->save($cartItem);

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
		$this->initialize($request);

		$validationRule = CartItem::getValidationRule(isset($dishId));
		$this->validateInput($request, $validationRule);

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
		$this->initialize($request);

		$item = $this->cart->findItemByDish($dishId);
		$item->delete();

		return response()->success($item, 18002);
	}

}