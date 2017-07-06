<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sign Up Confirmation</title>
</head>
<body>

    <h1>
        Thanks for signing up!
    </h1>
    <p>
        We just need to you to
        <a href="http://laravel.dev:9001/#!/user/register/{{$user->token}}">confirm your email</a>
        real quick!
    </p>

</body>
</html>