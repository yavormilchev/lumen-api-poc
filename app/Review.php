<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

/**
 * Review model
 *
 * @SWG\Definition(
 *   definition="Review",
 *   required={"name","description"},
 *   @SWG\Property(
 *       property="name",
 *       type="string"
 *   ),
 *   @SWG\Property(
 *       property="description",
 *       type="string"
 *   )
 * )
 */
class Review extends Model
{
    /**
     * @var array The attributes that are mass assignable.
     */
    protected $fillable = [
        'name',
        'description',
    ];

    /**
     * @var string Table name
     */
    protected $table = 'reviews';
}
