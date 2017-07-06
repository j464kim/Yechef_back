<?php

namespace App\Traits;

use App\Exceptions\YechefException;

trait ModelService
{
	public static function findById($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return self::with('medias')->findOrFail($id);
			} else {
				return self::findOrFail($id);
			}
		} catch (\Exception $e) {
			throw new YechefException(19500);
		}
	}
}