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
	protected $subject;
	protected $data;

	function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendOrderRequested(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_REQUEST;
		$this->subject = __('orderToSeller.order_request_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderAccepted(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_ACCEPTED;
		$this->subject = __('orderToSeller.order_accepted_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderDeclined(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_DECLINED;
		$this->subject = __('orderToSeller.order_declined_subject');

		// $order variable to be available on template
		$this->data = compact('order');

		$this->deliver();
	}

	public function sendOrderCancelled(User $seller, Order $order)
	{
		$this->to = $seller->email;
		$this->view = MailType::SELLER_ORDER_CANCELLED;
		$this->subject = __('orderToSeller.order_cancelled_subject');

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