<?php
namespace App\Exceptions;


class YechefException extends \Exception
{

	/**
	 * Create a new authentication exception.
	 *
	 * @param  string  $message
	 */
	public function __construct($errorCode = 0, $message = '')
	{
		if(!is_numeric($errorCode)) {
			$errorCode = 0;
		}
		parent::__construct($message, $errorCode);
	}

}