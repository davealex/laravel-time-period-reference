<?php

namespace Davealex\LaravelTimePeriodReference\Exceptions;

use Throwable;

class InvalidTimeReferenceCarbonInstance extends \Exception
{
    public function __construct(string $message = 'Invalid Carbon instance value for time-reference', int $code = 500, ?Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }
}