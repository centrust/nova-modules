<?php

namespace Centrust\NovaModules;

use Centrust\NovaModules\Commands\CreateNovaModuleAction;
use Spatie\LaravelPackageTools\Package;
use Spatie\LaravelPackageTools\PackageServiceProvider;
use Centrust\NovaModules\Commands\NovaModulesCommand;

class NovaModulesServiceProvider extends PackageServiceProvider
{
    public function configurePackage(Package $package): void
    {
        /*
         * This class is a Package Service Provider
         *
         * More info: https://github.com/spatie/laravel-package-tools
         */
        $package
            ->name('nova-modules')
            ->hasConfigFile()
            ->hasCommands([NovaModulesCommand::class,CreateNovaModuleAction::class]);
    }
}
