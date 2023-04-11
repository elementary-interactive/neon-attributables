<?php

namespace Neon\Attributable\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AttributableClearCommand extends Command
{
  /**
   * @var string The command.
   */
  protected $signature = 'attributes:clear';

  protected $description = 'Clear Neon Attributable\'s attributes cache.';

  public final function handle()
  {
    if (config('attributable.cache'))
    {
      Cache::tags(['neon-attributes'])
        ->flush();
      $this->info('Cache flushed.');
    }
    else
    {
      $this->warn('Cache is turned off by the configuration.');
    }
  }
}
