<?php
return [
	// Pusher Event/BroadCast Related Constants
	'events' => [
		'message' => [
			'type'   => 'event:message',
			'action' => [
				'sent'    => 'message.sent',
				'deleted' => 'message.deleted'
			]
		],
		'order'   => [
			'type'   => 'event:order',
			'action' => [
				'sent'      => 'order.sent',
				'accepted'  => 'order.accepted',
				'cancelled' => 'order.cancelled'
			]
		]
	]
];