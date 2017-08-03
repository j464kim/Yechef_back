<?php

namespace App\Services\Mail;

use Illuminate\Contracts\Mail\Mailer;
use App\Models\User;
use App\Models\Order;

class BuyerMailer
{
	protected $mailer;
	protected $to;
	protected $view;
	protected $subject;
	protected $data;

	function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendOrderRequested(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_REQUEST;
		$this->subject = __('orderToBuyer.order_request_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderAccepted(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_ACCEPTED;
		$this->subject = __('orderToBuyer.order_accepted_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderDeclined(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_DECLINED;
		$this->subject = __('orderToBuyer.order_declined_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderCancelled(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_CANCELLED;
		$this->subject = __('orderToBuyer.order_cancelled_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function deliver()
	{
		$this->mailer->send($this->view, $this->data, function ($message) {
			$message->subject($this->subject);
			$message->to($this->to);
		});
	}
}