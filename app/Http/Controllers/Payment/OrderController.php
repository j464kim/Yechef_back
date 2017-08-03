<?php

namespace App\Http\Controllers\Payment;

use App\Http\Controllers\Controller;
use App\Models\OrderItem;
use App\Services\Mail\SellerMailer;
use App\Services\Mail\BuyerMailer;
use Illuminate\Foundation\Application;
use Illuminate\Http\Request;
use App\Models\Order;
use Stripe\Charge;
use Stripe\Stripe;

class OrderController extends Controller
{
	private $transactionCtrl;
	protected $stripe, $secretKey, $sellerMailer, $buyerMailer;
	protected $user, $order, $charge;

	public function __construct(
		Application $app,
		TransactionController $transactionCtrl,
		Stripe $stripe,
		SellerMailer $sellerMailer,
		BuyerMailer $buyerMailer
	) {
		parent::__construct($app);

		$this->transactionCtrl = $transactionCtrl;
		$this->stripe = $stripe;
		$this->secretKey = config('services.stripe.secret_key');
		$this->sellerMailer = $sellerMailer;
		$this->buyerMailer = $buyerMailer;
	}

	public function initialize(Request $request, $orderId)
	{
		$this->user = $this->getUser($request);
		$this->order = Order::findById($orderId);
		$this->stripe->setApiKey($this->secretKey);
		$this->charge = Charge::retrieve($this->order->transaction->charge_id);
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
		$buyer = $this->order->user;
		$seller = $this->user;
		$seller->isVerifiedKitchenOwner($kitchenId);

		// process accepting order
		$this->order->acceptInFull();
		$this->order->transaction->captureAmount($this->charge);
		$this->sellerMailer->sendOrderAccepted($seller, $this->order);
		$this->buyerMailer->sendOrderAccepted($buyer, $this->order);
	}

	public function declineOrder(Request $request, $kitchenId, $orderId)
	{
		// get whats needed
		$this->initialize($request, $orderId);

		// check if authorized
		$buyer = $this->order->user;
		$seller = $this->user;
		$seller->isVerifiedKitchenOwner($kitchenId);
		$this->order->isCancellable();

		// process declining order
		$this->order->declineInFull();
		$this->order->transaction->refundAmount($this->charge);
		$this->sellerMailer->sendOrderDeclined($seller, $this->order);
		$this->buyerMailer->sendOrderDeclined($buyer, $this->order);
	}

	public function cancelOrder(Request $request, $orderId)
	{
		// get whats needed
		$this->initialize($request, $orderId);

		// check if authorized
		$buyer = $this->user;
		$seller = $this->order->kitchen->getBoss();
		$buyer->isOrderMaker($orderId);
		$this->order->isCancellable();

		// process cancelling order
		$this->order->declineInFull();
		$this->order->transaction->refundAmount($this->charge);
		$this->sellerMailer->sendOrderCancelled($seller, $this->order);
		$this->buyerMailer->sendOrderCancelled($buyer, $this->order);
	}


}