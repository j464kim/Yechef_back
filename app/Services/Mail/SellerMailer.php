<?php

namespace App\Services\Mail;

use App\Models\User;
use Illuminate\Contracts\Mail\Mailer;
use App\Models\Order;

class SellerMailer
{
	protected $mailer;
	protected $to;
	protected $view;
	protected $data;

	function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendOrderRequested(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_REQUEST;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderAccepted(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_ACCEPTED;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderDeclined(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_DECLINED;

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderCancelled(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_CANCELLED;

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