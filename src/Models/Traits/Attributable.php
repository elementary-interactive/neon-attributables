<?php

namespace Neon\Attributable\Models\Traits;

use Neon\Attributable\Models\Attribute;
use Neon\Attributable\Models\AttributeValue;
use Illuminate\Support\Facades\Cache;
use Illuminate\Support\Str;


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
  public static function bootAttributable()
  {

    static::saving(function ($model) {
      $model->attributeValues()->delete();

      foreach ($model->attributable as $key => $attribute) {
        if (array_key_exists($key, $model->attributes)) {
          $value = new AttributeValue([
            'value'     => $model->attributes[$key],
          ]);

          $model->attributable_records[] = Attribute::find($attribute['id'])->values()->save($value);

          unset($model->attributes[$key]);
        }
      }
    });

    static::saved(function ($model) {

      /** Save all variables.
       * 
       */
      foreach ($model->attributable_records as $key => $record) {
        $model->attributeValues()->save($record);

        unset($model->attributable_records[$key]);
      }
      
      if (config('attributable.cache')) {
        Cache::forget('neon-attributable-value-' . $model->id);
      }
    });

    static::retrieved(function ($model) {
      if (config('attributable.cache') && !Cache::has('neon-attributable-value-' . $model->id)) {
        Cache::put(
            'neon-attributable-value-' . $model->id,
            $model->attributeValues()->get(),
            now()->addMinutes(5)
          );
      }

      $attributeValues = (config('attributable.cache') && Cache::has('neon-attributable-value-' . $model->id)) ? Cache::get('neon-attributable-value-' . $model->id) : $model->attributeValues()->get();

      foreach ($attributeValues as $attributeValue) {
        $model->setAttribute($attributeValue->attribute->slug, $attributeValue->value);
      }
    });
  }

  protected function initializeAttributable()
  {
    /** If cache is enabled, but is does not contain attiributable values, we
     * shall put it into cache.
     */
    if (config('attributable.cache') && !Cache::has('neon-attributable-' . Str::slug(self::class))) {
      //-- Store cache ---------------------------------------------------------
      Cache::put(
        'neon-attributable-' . Str::slug(self::class),
        Attribute::where('class', self::getMorphClass())->get(),
        now()->addMinutes(5)
      );
    }

    /** If cache is enabled, and has the key, we read content. This is not an 
     * if-else, to spare the queries, so if it's stored then just read from there
     * and then go...
    */
    if (config('attributable.cache') && Cache::has('neon-attributable-' . Str::slug(self::class))) {
      $attributable = Cache::get('neon-attributable-' . Str::slug(self::class));
    } else {
      $attributable = Attribute::where('class', self::getMorphClass())->get();
    }

    /**
     * @todo Caching. Cache can store the result, and very easily could be
     * re-generated if the attributes created or updated related to the 
     * given class.
     */
    foreach ($attributable as $attribute) {
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
