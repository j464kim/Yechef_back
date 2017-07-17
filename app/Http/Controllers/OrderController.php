<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Models\User;
use App\Services\AppMailer;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Application;
use Illuminate\Foundation\Auth\RegistersUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Illuminate\Contracts\Filesystem\Factory;
use Illuminate\Support\Facades\Log;
use App\Models\Order;
use App\Models\Cart;

class OrderController extends Controller
{

	public function __construct(Application $app)
	{
		parent::__construct($app);
	}

	public function store($transactionId, $userId, $kitchenId)
	{
		$order = Order::create(
			array(
				"transaction_id" => $transactionId,
				"user_id"        => $userId,
				"kitchen_id"     => $kitchenId,
				"status"         => 'pending'
			)
		);

		$cart = $order->cart();

		foreach ($cart->items as $item) {
			OrderItem::create(
				array(
					"order_id"          => $order->id,
					"dish_id"           => $item->dish_id,
					"quantity"          => $item->quantity,
					"captured_quantity" => 0
				)
			);
		}

		return $order;
	}
}