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
      
      /** Export migrations.
       */
      $this->publishes([
        __DIR__ . '/../database/migrations/create_attributes_table.php.stub'        => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_attributes_table.php'),
        __DIR__ . '/../database/migrations/create_attribute_values_table.php.stub'  => database_path('migrations/' . date('Y_m_d_His', time()) . '_create_attribute_values_table.php'),
      ], 'migrations');

      $this->commands([
        AttributableClearCommand::class
      ]);
    }  
  }
}
