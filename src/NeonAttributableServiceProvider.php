<?php

namespace Neon\Attributable;

use Illuminate\Support\ServiceProvider;
use Illuminate\Contracts\Http\Kernel;
use Neon\Attributable\Console\AttributableClearCommand;

class NeonAttributableServiceProvider extends ServiceProvider
{
  /** Bootstrap any application services.
   *
   * @param \Illuminate\Contracts\Http\Kernel  $kernel
   *
   * @return void
   */
  public function boot(Kernel $kernel): void
  {
    if ($this->app->runningInConsole()) {

      /** Export config.
       */
      $this->publishes([
        __DIR__.'/../config/config_attributable.php'   => config_path('attributable.php'),
      ], 'neon-configs');
      
      /** Export migrations.
       */
      if (!class_exists('CreateAttributesTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/create_attributes_table.php.stub'        => database_path('migrations/' . date('Y_m_d_', time()) . '000001_create_attributes_table.php'),
        ], 'neon-migrations');
      }

      if (!class_exists('CreateAttributeValuesTable')) {
        $this->publishes([
          __DIR__ . '/../database/migrations/create_attribute_values_table.php.stub'  => database_path('migrations/' . date('Y_m_d_', time()) . '000002_create_attribute_values_table.php'),
        ], 'neon-migrations');
      }

      /** Add command...
       */
      $this->commands([
        AttributableClearCommand::class
      ]);
    }  
  }
}
