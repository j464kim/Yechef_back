<?php
return [
	// Pusher Event/BroadCast Related Constants
	'events' => [
		'message' => [
			'type'   => 'message',
			'action' => [
				'sent'    => 'message.sent',
				'deleted' => 'message.deleted'
			]
		],
		'order'   => [
			'type'   => 'order',
			'action' => [
				'sent'      => 'order.sent',
				'accepted'  => 'order.accepted',
				'cancelled' => 'order.cancelled'
			]
		]
	]
];