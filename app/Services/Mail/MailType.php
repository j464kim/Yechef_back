<?php

namespace App\Services\Mail;

class MailType
{
	const REGISTRATION_VERIFY = 'emails.verifyEmail';

	// Order
	const SELLER_ORDER_REQUEST = 'emails.seller.orderRequested';
	const SELLER_ORDER_ACCEPTED = 'emails.seller.orderAccepted';
	const SELLER_ORDER_DECLINED = 'emails.seller.orderDeclined';
	const SELLER_ORDER_CANCELLED = 'emails.seller.orderCancelled';

	const BUYER_ORDER_REQUEST = 'emails.buyer.orderRequested';
	const BUYER_ORDER_ACCEPTED = 'emails.buyer.orderAccepted';
	const BUYER_ORDER_DECLINED = 'emails.buyer.orderDeclined';
	const BUYER_ORDER_CANCELLED = 'emails.buyer.orderCancelled';
}