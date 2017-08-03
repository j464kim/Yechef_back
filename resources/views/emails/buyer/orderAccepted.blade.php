<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="UTF-8">
	<title>
		{{ __('orderToBuyer.order_accepted_title') }}
	</title>
</head>
<body>
<h1>
	{{ __('orderToBuyer.order_accepted_intro') }}
</h1>
<p>
	{{ __('orderToBuyer.order_accepted_body') }}
	<a href="{{ url(config('app.url_front')) . 'user/profile/myOrder'}}">
		{{ __('orderToBuyer.order_accepted_link') }}
	</a>
</p>
</body>
</html>