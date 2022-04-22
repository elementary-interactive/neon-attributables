<?php

namespace Neon\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as EloquentModel;

use Neon\Models\Traits\Uuid;

class AttributeValue extends EloquentModel
{
  use SoftDeletes;
  use Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'value',
    'published_at',
  ];

  /** The attributes that should be handled as date or datetime.
   *
   * @var array
   */
  protected $dates = [
    'created_at', 'updated_at', 'deleted_at', 'published_at', 'expired_at',
  ];

  /** */
  public function attribute()
  {
    return $this->belongsTo(\Neon\Models\Attribute::class);
  }

  public function getValueAttribute()
  {
    return unserialize($this->attributes['value']);
  }

  public function setValueAttribute($value)
  {
    $this->attributes['value'] = serialize($value);
  }
}
