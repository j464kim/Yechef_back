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
	protected $data;

	function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendOrderRequested(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_REQUEST;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderAccepted(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_ACCEPTED;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderDeclined(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_DECLINED;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderCancelled(User $buyer, Order $order)
	{
		$this->to = $buyer->email;
		$this->view = MailType::BUYER_ORDER_CANCELLED;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function deliver()
	{
		$this->mailer->send($this->view, $this->data, function ($message) {
			$message->to($this->to);
		});
	}
}