<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
		{{ __('orderToBuyer.order_cancelled_title') }}
	</title>
</head>
<body>
<h1>
	{{ __('orderToBuyer.order_cancelled_intro') }}
</h1>
<p>
	{{ __('orderToBuyer.order_cancelled_body') }}
	<a href="{{ url(config('app.url_front')) . 'user/profile/myOrder'}}">
		{{ __('orderToBuyer.order_cancelled_link') }}
	</a>
</p>
</body>
</html>