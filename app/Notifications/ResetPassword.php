<?php

namespace App\Notifications;

use Illuminate\Notifications\Notification;
use Illuminate\Notifications\Messages\MailMessage;

class ResetPassword extends Notification
{
	/**
	 * The password reset token.
	 *
	 * @var string
	 */
	public $token;

	/**
	 * Create a notification instance.
	 *
	 * @param  string  $token
	 * @return void
	 */
	public function __construct($token)
	{
		$this->token = $token;
	}

	/**
	 * Get the notification's channels.
	 *
	 * @param  mixed  $notifiable
	 * @return array|string
	 */
	public function via($notifiable)
	{
		return ['mail'];
	}

	/**
	 * Build the mail representation of the notification.
	 *
	 * @param  mixed  $notifiable
	 * @return \Illuminate\Notifications\Messages\MailMessage
	 */
	public function toMail($notifiable)
	{
		return (new MailMessage)
			->line(__('passwords.password_reset_intro'))
			->action('Reset Password', url(config('app.url')) . ':9001/#!/'. 'user/password/reset/' . $this->token)
			->line(__('passwords.password_reset_conclusion'));
	}
}