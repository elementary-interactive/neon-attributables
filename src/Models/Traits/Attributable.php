<?php

namespace Neon\Attributable\Models\Traits;

use Neon\Attributable\Models\Attribute;
use Neon\Attributable\Models\AttributeValue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Facades\Str;


/** 
 
 * 
 * @author: Balázs Ercsey <balazs.ercsey@elementary-interactive.com>
 */
trait Attributable
{
  protected $attributable = [];

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

      foreach ($model->attributable as $key => $attribute)
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
      if (!Cache::tags(['neon-attributes'])->has('neon-aval-'.$model->id)) {
        Cache::tags(['neon-attributes'])
          ->put(
              'neon-aval-'.$model->id,
              $model->attributeValues,
              now()->addMinutes(2)
            );
      }
      
      $attributeValues = Cache::tags(['neon-attributes'])->get('neon-aval-'.$model->id) ?? $model->attributeValues;
      
      foreach ($attributeValues as $attributeValue)
      {
        $model->setAttribute($attributeValue->attribute->slug, $attributeValue->value);
      }
    });
  }

  protected function initializeAttributable()
  {
    $attributable = Attribute::where('class', '=', self::class)->get();

    if (!Cache::tags(['neon-attributes'])->has('neon-attr-'.Str::slug(self::class)))
    {
      Cache::tags(['neon-attributes'])
        ->put(
            'neon-attr-'.Str::slug(self::class),
            Attribute::where('class', '=', self::class)->get(),
            now()->addMinutes(2)
          );
    }

    $attributable = Cache::tags(['neon-attributes'])->get('neon-attr-'.Str::slug(self::class));

    /**
     * @todo Caching. Cache can store the result, and very easily could be
     * re-generated if the attributes created or updated related to the 
     * given class.
     */

    foreach ($attributable as $attribute)
    {
      $this->attributable[$attribute->slug] = [
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
