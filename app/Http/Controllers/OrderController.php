<?php

namespace App\Http\Controllers;

use App\Exceptions\YechefException;
use App\Http\Controllers\Auth\TransactionController;
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
use Stripe\Charge;
use Stripe\Stripe;

class OrderController extends Controller
{
	private $transactionCtrl;
	protected $stripe, $secretKey;

	public function __construct(Application $app, TransactionController $transactionCtrl, Stripe $stripe)
	{
		parent::__construct($app);

		$this->transactionCtrl = $transactionCtrl;
		$this->stripe = $stripe;
		$this->secretKey = config('services.stripe.secret_key');
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

	public function acceptOrder(Request $request, $kitchenId, $orderId)
	{
		$user = $this->getUser($request);
		$user->isVerifiedKitchenOwner($kitchenId);

		$order = Order::findById($orderId);
		$order->acceptInFull();

		$order->save();

		// capture amount
		$this->stripe->setApiKey($this->secretKey);

		$transaction = $order->transaction;
		$charge = Charge::retrieve($transaction->charge_id);

		$transaction->captureAmount($charge);
	}

	public function declineOrder(Request $request, $kitchenId, $orderId)
	{
		$user = $this->getUser($request);
		$order = Order::findById($orderId);

		// necessary check before cancelling order
		$user->isVerifiedKitchenOwner($kitchenId);
		$order->isCancellable();

		$order->declineInFull();

		// refund amount
		$this->stripe->setApiKey($this->secretKey);

		$transaction = $order->transaction;
		$charge = Charge::retrieve($transaction->charge_id);

		$transaction->refundAmount($charge);
	}

	public function cancelOrder(Request $request, $kitchenId, $orderId)
	{
		$user = $this->getUser($request);
		$order = Order::findById($orderId);

		$user->isOrderMaker($orderId);
		$order->isCancellable();

		$order->declineInFull();

		// refund amount
		$this->stripe->setApiKey($this->secretKey);

		$transaction = $order->transaction;
		$charge = Charge::retrieve($transaction->charge_id);

		$transaction->refundAmount($charge);
	}

}