<?php

namespace Neon\Attributables\Models\Traits;

use Neon\Attributables\Models\Attribute;
use Neon\Attributables\Models\AttributeValue;
use Illuminate\Database\Eloquent\Relations\MorphTo;

/** 
 
 * 
 * @author: Balázs Ercsey <balazs.ercsey@elementary-interactive.com>
 */
trait Attributable
{
  protected $attributables = [];

  protected $attributable_records = [];

  /** Extending the boot, to ...
   */
  protected static function boot()
  {
    /** We MUST call the parent boot...
     */
    parent::boot();

    static::saving(function ($model) {
      // $model->attributeValues()->delete();

      // foreach ($model->attributables as $key => $attribute)
      // {

      //   $value = new AttributeValue([
      //     'value'     => $model->attributes[$key],
      //   ]);
        
      //   $model->attributable_records[] = Attribute::find($attribute['id'])->values()->save($value);
        
      //   unset($model->attributes[$key]);
      // }
    });

    static::retrieved(function ($model) {
      foreach ($model->attributeValues as $attributeValue)
      {
        $model->setAttribute($attributeValue->attribute->slug, $attributeValue->value);
      }
    });
  }

  protected function initializeAttributable()
  {
    $attributables = Attribute::where('class', '=', self::class)->get();

    /**
     * @todo Caching. Cache can store the result, and very easily could be
     * re-generated if the attributes created or updated related to the 
     * given class.
     */

    foreach ($attributables as $attribute)
    {
      $this->attributables[$attribute->slug] = [
        'cast_as' => $attribute->cast_as,
        'rules'   => $attribute->rules,
        'field'   => $attribute->field,
        'name'    => $attribute->name,
        'id'      => $attribute->id,
      ];

      /** Set attribute casting.
       */
      $this->casts[$attribute->slug]      = $attribute->cast_as;

      /** Fill attributes with empty value.
       */
      $this->setAttribute($attribute->slug, null);

    };
  }

  /** Get connected variable values.
   * 
   * @return Illuminate\Database\Eloquent\Relations\MorphMany
   */
  public function attributeValues()
  {
    return $this->belongsToMany(Attribute::class, AttributeValue::class)
      ->withPivotValue('attributable_type', static::class)
      ->withPivot('attributable_id')
      ->withPivot('value')
      // ->withSoftDeletes()
      ->withTimestamps();
  }
}
