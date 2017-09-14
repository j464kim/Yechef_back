<?php
return [
	// Pusher Event/BroadCast Related Constants
	'events' => [
		'unchecked' => 0,
		'checked'   => 1,
		'deleted'   => 2,
		'message'   => [
			'type'   => 'event:message',
			'action' => [
				'sent'    => 'message.sent',
				'deleted' => 'message.deleted'
			]
		],
		'order'     => [
			'type'   => 'event:order',
			'action' => [
				'sent'      => 'order.sent',
				'accepted'  => 'order.accepted',
				'cancelled' => 'order.cancelled'
			]
		]
	]
];