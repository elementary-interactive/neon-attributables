<?php

namespace Neon\Attributable\Models;

use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Model as EloquentModel;
use Neon\Models\Traits\Publishable;

use Neon\Models\Traits\Uuid;

class AttributeValue extends EloquentModel
{
  use SoftDeletes;
  use Publishable;
  use Uuid;

  /**
   * The attributes that are mass assignable.
   *
   * @var array
   */
  protected $fillable = [
    'value'
  ];

  /** The attributes that should be handled as date or datetime.
   *
   * @var array
   */
  protected $casts = [
    'created_at'    => 'date',
    'updated_at'    => 'date',
    'deleted_at'    => 'date',
    'published_at'  => 'date',
    'expired_at'    => 'date',
  ];

  /** */
  public function attribute()
  {
    return $this->belongsTo(\Neon\Attributable\Models\Attribute::class);
  }

  public function attributable()
  {
    return $this->morphTo('attributable');
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
