<?php

namespace Neon\Attributables\Models\Traits;

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

  /**
   * The "booted" method of the model.
   *
   * @return void
   */
  protected static function booted($model)
  {
    dd($model);
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
