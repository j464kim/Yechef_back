<?php

namespace App\Providers;

use Adaojunior\Passport\SocialUserResolverInterface;
use App\Yechef\SocialLogin;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\ServiceProvider;


class AppServiceProvider extends ServiceProvider
{
	/**
	 * Bootstrap any application services.
	 *
	 * @return void
	 */
	public function boot()
	{
		Schema::defaultStringLength(191);

		//Phone Validation. i.g. +1-123-456-7890, 11234567890, +11234567890, +1-(123)-456-7890, +1-123-456-7890
		Validator::extend('phone', function ($attribute, $value, $parameters, $validator) {
			return preg_match('%^(?:(?:\(?(?:00|\+)([1-4]\d\d|[1-9]\d?)\)?)?[\-\.\ \\\/]?)?((?:\(?\d{1,}\)?[\-\.\ \\\/]?){0,})(?:[\-\.\ \\\/]?(?:#|ext\.?|extension|x)[\-\.\ \\\/]?(\d+))?$%i',
					$value) && strlen($value) >= 10;
		});
		Validator::replacer('phone', function ($message, $attribute, $rule, $parameters) {
			return str_replace(':attribute', $attribute, ':attribute is invalid phone number');
		});
	}

	/**
	 * Register any application services.
	 *
	 * @return void
	 */
	public function register()
	{
		$this->app->singleton(SocialUserResolverInterface::class, SocialLogin::class);
	}
}
