<?php

namespace App\Providers;

use Laravel\Passport\Passport;
use Carbon\Carbon;
use Illuminate\Support\Facades\Gate;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;

class AuthServiceProvider extends ServiceProvider
{
    /**
     * The policy mappings for the application.
     *
     * @var array
     */
    protected $policies = [
        'App\Model' => 'App\Policies\ModelPolicy',
    ];

    /**
     * Register any authentication / authorization services.
     *
     * @return void
     */
    public function boot()
    {
        $this->registerPolicies();

		Passport::routes();

		Passport::tokensExpireIn(Carbon::now()->addMinute(env('OAUTH_TOKEN_EXPIRE_IN_MINUTES', 10)));

		Passport::refreshTokensExpireIn(Carbon::now()->addDays(env('OAUTH_REFRESH_TOKEN_EXPIRE_IN_DAYS', 10)));
    }
}
