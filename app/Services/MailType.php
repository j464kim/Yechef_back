<?php

namespace App\Services;

class MailType {

	const REGISTRATION_VERIFY= 'emails.verifyEmail';
	const ORDER_REQUEST = 'emails.requestOrder';
	const ORDER_ACCEPTED = 'emails.orderAccepted';
	const ORDER_DECLINED = 'emails.orderDeclined';
	const ORDER_CANCELLED = 'emails.orderCancelled';
}