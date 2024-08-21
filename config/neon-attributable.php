<?php

return [

  /** IMPORTANT!!!
   * 
   * To use Attributable cache, we need cache engine what can work with tagging.
   * 
   * @see https://laravel.com/docs/10.x/cache#cache-tags
   * 
   * If cache is turned on you can use the next command to empty:
   * ```
   *   php artisan attributes:clear
   * ```
   */
  'cache' => env('NEON_ATTRIBUTABLE_CACHE', false),

  /**
   * @todo Remove translated strings! (Together with Filament PHP)
   */
  'scopes' => [
    'site' => 'Weboldal',
    'link' => 'Oldal',
    'menu' => 'Menü'
  ],

  /**
   * @todo Remove translated strings! (Together with Filament PHP)
   */
  'fields' => [
      'Text'  => 'Szöveges beviteli mező'
  ],

  /**
   * @todo Remove translated strings! (Together with Filament PHP)
   */
  'casts' => [
    'string'  => 'Szöveg',
    'int'     => 'Egész szám',
    'boolean' => 'Logikai (I/H)'
  ]

];