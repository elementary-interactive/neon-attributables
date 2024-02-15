<?php

namespace Neon\Attributable;

use Illuminate\Contracts\Http\Kernel;
use Illuminate\Database\Eloquent\Relations\Relation;
use Illuminate\Foundation\Console\AboutCommand;
use Illuminate\Support\ServiceProvider;
use Neon\Attributable\Console\AttributableClearCommand;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Spatie\LaravelPackageTools\Package;

class NeonAttributableServiceProvider extends PackageServiceProvider
{
  const VERSION = '3.0.0-alpha-3';

  public function configurePackage(Package $package): void
  {
    AboutCommand::add('N30N', 'Attributable', self::VERSION);

    $package
      ->name('neon-attributable')
      ->hasConfigFile()
      ->hasMigrations(['create_attributes_table', 'create_attribute_values_table'])
      ->hasCommands([AttributableClearCommand::class]);
  }
}
