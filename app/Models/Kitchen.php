<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Support\Facades\Log;

class Kitchen extends Model
{
	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	protected $fillable = ['name', 'address', 'phone', 'email', 'description'];

	/**
	 * Many to many relationship to media
	 */
	public function media()
	{
		return $this->belongsToMany('App\Models\Media');
	}

	/**
	 * @return array
	 */
	public static function getValidationRule()
	{
		$rule = array(
			'name'        => 'bail|required|unique:kitchens',
			'email'       => 'unique:kitchens',
			'phone'       => 'required',
			'address'     => 'required',
			'description' => 'nullable',
		);

		return $rule;
	}

	public static function findKitchen($id, $withMedia = false)
	{
		try {
			if ($withMedia) {
				return Kitchen::with('media')->findOrFail($id);
			} else {
				return Kitchen::findOrFail($id);
			}
		} catch (ModelNotFoundException $e) {
			Log::error('Could not find the kitchen with id: ' . $id);

			$message = ($e->getMessage() ?: $message = 'the requested resource was not found');
			return Response::json(array(
				'code'    => 422,
				'message' => $message
			), 422);
		}
	}
}
