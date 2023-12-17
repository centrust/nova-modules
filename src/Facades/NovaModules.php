<?php

namespace Centrust\NovaModules\Facades;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Centrust\NovaModules\NovaModules
 */
class NovaModules extends Facade
{
    protected static function getFacadeAccessor()
    {
        return \Centrust\NovaModules\NovaModules::class;
    }
}
