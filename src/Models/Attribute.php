<?php

namespace Neon\Attributable\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Illuminate\Support\Facades\Artisan;

use Neon\Models\Traits\Uuid;

class Attribute extends EloquentModel
{
  use SoftDeletes;
  use Uuid;

  /** The attributes that should be handled as date or datetime.
   *
   * @var array
   */
  protected $dates = [];

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'class', 'name', 'slug', 'field', 'cast_as', 'rules', 'parameters'
  ];

  protected $casts = [
    'parameters'  => 'json',
    'rules'       => 'array',
    'created_at'  => 'datetime',
    'updated_at'  => 'datetime',
    'deleted_at'  => 'datetime'
  ];

  protected static function boot()
  {
    /** We MUST call the parent boot...
     */
    parent::boot();

    static::saved(function ($model) {
      /**
       * Clean up cache.
       */
      if (config('attributable.cache'))
      {
        Artisan::call('attributes:clear');
      }
    });
  }

  public function values()
  {
    /** We getting only the valid variable values, that are published and
     * not yet expired.
     */
    return $this->hasMany(\Neon\Attributable\Models\AttributeValue::class);
  }
}
