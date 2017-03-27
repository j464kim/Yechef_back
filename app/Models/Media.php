<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Media extends Model
{
	/**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'media';
    
	/**
     * The attributes that are mass assignable.
     *
     * @var array
     */
	protected $fillable = [
        'slug', 'url', 'type',
    ];
}
