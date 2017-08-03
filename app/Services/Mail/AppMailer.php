<?php

namespace App\Services\Mail;

use Illuminate\Contracts\Mail\Mailer;
use App\Models\User;
use App\Models\Order;

class AppMailer
{
	protected $mailer;
	protected $to;
	protected $view;
	protected $data;

	function __construct(Mailer $mailer)
	{
		$this->mailer = $mailer;
	}

	public function sendConfirmationEmailTo(User $user)
	{
		$this->to = $user->email;
		$this->view = MailType::REGISTRATION_VERIFY;

		// $user variable to be available on template
		$this->data = compact('user');

		$this->deliver();
	}

	public function deliver()
	{
		$this->mailer->send($this->view, $this->data, function ($message) {
			$message->to($this->to);
		});
	}
}