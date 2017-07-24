<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>
        {{ __('registration.confirmation_title') }}
    </title>
</head>
<body>
    <h1>
        {{ __('registration.confirmation_intro') }}
    </h1>
    <p>
        {{ __('registration.confirmation_body') }}
        <a href="{{ url(config('app.url_front')) . 'user/register/' . $user->token}}">
            {{ __('registration.confirmation_link') }}
        </a>
    </p>
</body>
</html>