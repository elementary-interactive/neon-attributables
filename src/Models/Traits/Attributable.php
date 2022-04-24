<?php

namespace Neon\Attributables\Models\Traits;

use Neon\Attributables\Models\Attribute;

/** 
 
 * 
 * @author: Balázs Ercsey <balazs.ercsey@elementary-interactive.com>
 */
trait Attributable
{

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
    $arrtibutes = Attribute::where('class', '=', self::class)->get();

    dd(self::class, $arrtibutes);
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
