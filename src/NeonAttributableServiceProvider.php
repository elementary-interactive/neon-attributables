<?php

namespace Neon\Attributable;

use Illuminate\Support\ServiceProvider;
<<<<<<< Updated upstream
use \Illuminate\Contracts\Http\Kernel;
=======
use Illuminate\Contracts\Http\Kernel;
use Neon\Attributable\Console\AttributableClearCommand;
>>>>>>> Stashed changes

class NeonAttributableerviceProvider extends ServiceProvider
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
<<<<<<< Updated upstream
=======

      $this->commands([
        AttributableClearCommand::class
      ]);
>>>>>>> Stashed changes
    }
  }
}
