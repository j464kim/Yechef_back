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
	protected $stripe, $secretKey, $mailer;
	protected $user, $order, $charge;

	public function __construct(
		Application $app,
		TransactionController $transactionCtrl,
		Stripe $stripe,
		AppMailer $mailer
	) {
		parent::__construct($app);

		$this->transactionCtrl = $transactionCtrl;
		$this->stripe = $stripe;
		$this->secretKey = config('services.stripe.secret_key');
		$this->mailer = $mailer;
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
					"order_id" => $order->id,
					"dish_id" => $item->dish_id,
					"quantity" => $item->quantity,
					"captured_quantity" => 0
				)
			);
		}

		return $order;
	}

	public function acceptOrder(Request $request, $kitchenId, $orderId)
	{
		// get whats needed
		$this->initialize($request, $orderId);

		// check if authorized
		$this->user->isVerifiedKitchenOwner($kitchenId);

		// process accepting order
		$this->order->acceptInFull();
		$this->order->transaction->captureAmount($this->charge);
		$this->mailer->sendOrderAccepted($this->user, $this->order);
	}

	public function declineOrder(Request $request, $kitchenId, $orderId)
	{
		// get whats needed
		$this->initialize($request, $orderId);

		// check if authorized
		$this->user->isVerifiedKitchenOwner($kitchenId);
		$this->order->isCancellable();

		// process declining order
		$this->order->declineInFull();
		$this->order->transaction->refundAmount($this->charge);
		$this->mailer->sendOrderDeclined($this->user, $this->order);
	}

	public function cancelOrder(Request $request, $orderId)
	{
		// get whats needed
		$this->initialize($request, $orderId);

		// check if authorized
		$this->user->isOrderMaker($orderId);
		$this->order->isCancellable();

		// process cancelling order
		$this->order->declineInFull();
		$this->order->transaction->refundAmount($this->charge);
		$this->mailer->sendOrderCancelled($this->user, $this->order);
	}

	public function initialize(Request $request, $orderId)
	{
		$this->user = $this->getUser($request);
		$this->order = Order::findById($orderId);
		$this->stripe->setApiKey($this->secretKey);
		$this->charge = Charge::retrieve($this->order->transaction->charge_id);
	}


}