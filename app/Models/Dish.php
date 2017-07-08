<?php

namespace App\Models;

use App\Traits\ModelService;
use App\Traits\Reactionable;
use App\Yechef\DishRatingable as Ratingable;
use GeometryLibrary\SphericalUtil;
use Iatstuti\Database\Support\CascadeSoftDeletes;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Http\Request;
use Laravel\Scout\Searchable;

class Dish extends Model
{
	use SoftDeletes, CascadeSoftDeletes;
	use Searchable;
	use Ratingable, Reactionable, ModelService;

	/**
	 * The attributes that are mass assignable.
	 *
	 * @var array
	 */
	//TODO: Add ingredient
	protected $fillable = [
		'slug',
		'name',
		'nationality',
		'description',
		'price',
		'gluten_free',
		'vegetarian',
		'vegan',
		'kitchen_id'
	];

	/**
	 * Enable softDeletes & cascade soft-deletes
	 */
	protected $dates = ['deleted_at'];

	/**
	 * Cascade soft-deletes related models
	 */
	protected $cascadeDeletes = ['ratings'];

	/**
	 * Get all of the Dish's comments.
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function medias()
	{
		return $this->morphMany('App\Models\Media', 'mediable');
	}

	public function ingredients()
	{
		//TODO
//		return $this->hasMany("App\Models\Ingredient");
	}

	public function kitchen()
	{
		return $this->belongsTo('App\Models\Kitchen');
	}

	public function ratings()
	{
		return $this->hasMany('App\Models\DishRating');
	}

	/**
	 * Get all of the Dish's reactions.
	 * @return \Illuminate\Database\Eloquent\Relations\MorphMany
	 */
	public function reactions()
	{
		return $this->morphMany('App\Models\Reaction', 'reactionable');
	}

	public function addRatingAttributes()
	{
		$totalRatigSum = 0;
		$avgRatings = $this->avgRating();

		$this['taste_rating'] = $avgRatings['taste_rating'];
		$this['visual_rating'] = $avgRatings['visual_rating'];
		$this['quantity_rating'] = $avgRatings['quantity_rating'];

		foreach($avgRatings as $eachAvg) {
			$totalRatigSum += $eachAvg;
		}

		$this['total_rating'] =  $totalRatigSum / sizeof($avgRatings);
	}

	/**
	 * @param null $id
	 * @return array
	 */
	public static function getValidationRule($id = null)
	{
		Return [
			'name'        => 'bail|required',
			'description' => 'bail|required',
			'kitchen_id'  => 'bail|required|integer',
			'price'       => 'bail|required|numeric',
			'nationality' => 'required'
			//TODO: ingredient
//			'ingredient_id' => 'integer',
		];
	}

	public static function filter(Request $request, $dishes)
	{
		if ($request->gluten_free === '1') {
			$dishes = $dishes->where('gluten_free', '1');
		}
		if ($request->vegan === '1') {
			$dishes = $dishes->where('vegan', '1');
		}
		if ($request->vegetarian === '1') {
			$dishes = $dishes->where('vegetarian', '1');
		}

		$dishes = $dishes->get()->load('medias')->load([
			'kitchen' => function ($query) use ($request) {
				$query->where('address', 'like', "%$request->city%");
			}
		])->where('kitchen', '!=', null);

		if ($request->input('nationality') !== 'all') {
			$dishes = $dishes->where('nationality', '=', $request->input('nationality'));
		}
		if ($request->input('min_price')) {
			$dishes = $dishes->where('price', '>', $request->input('min_price'));
		}
		if ($request->input('max_price')) {
			$dishes = $dishes->where('price', '<', $request->input('max_price'));
		}

		return $dishes->filter(function ($item) use ($request) {
			$geoCodedAddress = \GoogleMaps::load('geocoding')->setParamByKey('address', $item->kitchen->address)->get();
			$geoCodedAddress = json_decode($geoCodedAddress);
			$lat = $geoCodedAddress->results[0]->geometry->location->lat;
			$lng = $geoCodedAddress->results[0]->geometry->location->lng;
			$item->lat = $lat;
			$item->lng = $lng;
			$from = ['lat' => $lat, 'lng' => $lng];
			$to = ['lat' => $request->userLat, 'lng' => $request->userLng];
			$item->distance = SphericalUtil::computeDistanceBetween($from, $to);
			if ($request->distance && $request->distance != 0) {
				return ($request->NE_lat >= $lat) && ($request->NE_lng >= $lng) && ($request->SW_lat <= $lat) && ($request->SW_lng <= $lng) && ($request->distance >= $item->distance);
			} else {
				return ($request->NE_lat >= $lat) && ($request->NE_lng >= $lng) && ($request->SW_lat <= $lat) && ($request->SW_lng <= $lng);
			}
		});
	}

}
