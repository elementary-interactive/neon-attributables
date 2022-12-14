<?php

namespace Neon\Attributables\Console;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Cache;

class AttributablesClearCommand extends Command
{
  /**
   * @var string The command.
   */
  protected $signature = 'attributes:clear';

  protected $description = 'Clear Neon\' attributes cache.';

  public final function handle()
  {
    Cache::tags(['neon-attributes'])
      ->flush();
  }
}
