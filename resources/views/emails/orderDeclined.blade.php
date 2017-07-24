<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
		{{ __('orderToSeller.order_declined_title') }}
	</title>
</head>
<body>
<h1>
	{{ __('orderToSeller.order_declined_intro') }}
</h1>
<p>
	{{ __('orderToSeller.order_declined_body') }}
	<a href="{{ url(config('app.url_front')) . 'user/kitchen/' . $order->kitchen_id . '/order'}}">
		{{ __('orderToSeller.order_declined_link') }}
	</a>
</p>
</body>
</html>