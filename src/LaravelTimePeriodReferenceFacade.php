<?php

namespace Davealex\LaravelTimePeriodReference;

use Illuminate\Support\Facades\Facade;

/**
 * @see \Davealex\LaravelTimePeriodReference\LaravelTimePeriodReference
 */
class LaravelTimePeriodReferenceFacade extends Facade
{
    /**
     * Get the registered name of the component.
     *
     * @return string
     */
    protected static function getFacadeAccessor(): string
    {
        return 'laravel-time-period-reference';
    }
}
