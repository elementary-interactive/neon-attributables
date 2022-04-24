<?php

namespace Neon\Attributables\Models\Traits;

use Neon\Attributables\Models\Attribute;

/** 
 
 * 
 * @author: Balázs Ercsey <balazs.ercsey@elementary-interactive.com>
 */
trait Attributable
{
  protected $attributables = [];

  /** Extending the boot, to ...
   */
  protected static function boot()
  {
    /** We MUST call the parent boot...
     */
    parent::boot();
  }

  protected function initializeAttributable()
  {
    $this->$attributables = (array) Attribute::where('class', '=', self::class)->get();

    /**
     * @todo Caching. Cache can store the result, and very easily could be
     * re-generated if the attributes created or updated related to the 
     * given class.
     */

    foreach ($this->attributables as $attribute)
    {
      $this->attributes[$attribute->slug] = $attribute;
    }
    dd($this);
  }



  /** Get connected variable values.
   * 
   * @return Illuminate\Database\Eloquent\Relations\MorphMany
   */
  public function extendedAttributes()
  {
    return $this->morphMany(\Neon\Models\AttributeValue::class, 'attributable')
      ->with('attribute');
  }
}
