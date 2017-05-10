<?php
namespace App\Exceptions;


class OauthException extends \Exception
{

	/**
	 * Create a new authentication exception.
	 *
	 * @param  string  $message
	 */
	public function __construct($message = '')
	{
		$mainMessage = 'Fail to authenticate using Oauth2';
		parent::__construct($mainMessage . $message);
	}

}