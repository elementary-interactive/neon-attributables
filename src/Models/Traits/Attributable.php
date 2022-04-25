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

  // protected $dispatchesEvents = [
  //   'saved'     => \Neon\Attributables\Events\Changed::class,
  //   'deleted'   => \Neon\Attributables\Events\Changed::class,
  // ];


  /** Extending the boot, to ...
   */
  protected static function boot()
  {
    /** We MUST call the parent boot...
     */
    parent::boot();

    static::saving(function ($model) {
      echo 'event::saving';
      
      $model->attributeValues()->detach();

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
      echo 'ecent::saved';
      foreach ($model->attributable_records as $record)
      {
        $record->models()->save($model);
      }
      dd($model);
    });

    static::retrieved(function ($model) {
      echo 'event::retrieved';
      $x = $model->extendedAttributes;
      // dd($x);
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
    return $this->morphTo(AttributeValue::class, 'attributable')
      ->with('attribute');
  }
}
