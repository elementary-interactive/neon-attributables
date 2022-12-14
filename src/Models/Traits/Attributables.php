<?php

namespace Neon\Attributables\Models\Traits;

use Neon\Attributables\Models\Attribute;
use Neon\Attributables\Models\AttributeValue;
use Illuminate\Database\Eloquent\Relations\MorphTo;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Str;


/** 
 
 * 
 * @author: Balázs Ercsey <balazs.ercsey@elementary-interactive.com>
 */
trait Attributables
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
      $model->attributeValues()->delete();

      foreach ($model->attributables as $key => $attribute)
      {

        $value = new AttributeValue([
          'value'     => $model->attributes[$key],
        ]);
        
        $model->attributable_records[] = Attribute::find($attribute['id'])->values()->save($value);
        
        unset($model->attributes[$key]);
      }
    });

    static::saved(function ($model) {
      
      /** Save all variables.
       * 
       */
      foreach ($model->attributable_records as $key => $record)
      {
        $model->attributeValues()->save($record);
        
        unset($model->attributable_records[$key]);
      }
    });

    static::retrieved(function ($model) {
      if (!Cache::has('neon-aval-'.$model->id)) {
        $attributeValues = Cache::tags(['neon-attributes'])
          ->remember('neon-aval-'.$model->id, now()->addMinutes(5), function() use ($model) {
            return $model->attributeValues;
          });
      }
      foreach ($attributeValues as $attributeValue)
      {
        $model->setAttribute($attributeValue->attribute->slug, $attributeValue->value);
      }
    });
  }

  protected function initializeAttributable()
  {
    if (Cache::has('neon-attr-'.Str::slug(self::class)))
    {
      $attributables = Cache::get('neon-attr-'.Str::slug(self::class));
    }
    else
    {
      $attributables = Attribute::where('class', '=', self::class)->get();

      Cache::tags('neon-attributes')
        ->put('neon-attr-'.Str::slug(self::class), $attributables);
    }

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
    return $this->morphMany(AttributeValue::class, 'attributable')
      ->with('attribute');
  }
}
