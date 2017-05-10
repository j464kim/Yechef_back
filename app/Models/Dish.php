<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Dish extends Model
{
	use SoftDeletes;

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'slug', 'name', 'description'
    ];
	protected $dates = ['deleted_at'];

    /**
     * Many to many relationship to media
     */
    public function media()
    {
        return $this->belongsToMany('App\Models\Media');
    }

}
