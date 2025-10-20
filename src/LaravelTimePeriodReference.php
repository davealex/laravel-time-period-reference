<?php

namespace Davealex\LaravelTimePeriodReference;

use Davealex\LaravelTimePeriodReference\Exceptions\InvalidTimeReferenceCarbonInstance;
use Illuminate\Config\Repository as ConfigRepository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use BackedEnum;

readonly class LaravelTimePeriodReference
{
    private array $units;

    public function __construct(
        private ConfigRepository $config
    ) {

        if (!$this->config->has('laravel-time-period-reference.units')) {
            $this->config->set('laravel-time-period-reference.units', self::DEFAULT_UNITS);
        }

        $this->units = $this->config->get('laravel-time-period-reference.units');
    }

    const array DEFAULT_UNITS = [
        'seconds' => ['second', 'seconds', 'sec'],
        'minutes' => ['minutes', 'minute', 'min'],
        'hours' => ['hours', 'hour', 'hrs'],
        'days' => ['days', 'day'],
        'weeks' => ['weeks', 'week'],
        'months' => ['months', 'month'],
        'years' => ['years', 'year'],
    ];

    /**
     * @param BackedEnum|string $timeReference
     * @return Carbon
     * @throws InvalidTimeReferenceCarbonInstance
     */
    public function toCarbonInstance(BackedEnum|string $timeReference): Carbon
    {
        $timeReferenceString = match (true) {
            $timeReference instanceof BackedEnum => $timeReference->value,
            is_string($timeReference) => $timeReference,
            default => throw new InvalidTimeReferenceCarbonInstance("Time reference must be a string or BackedEnum."),
        };

        $timeReferenceStringValue = strtolower(trim($timeReferenceString));

        foreach ($this->units as $method => $references) {
            foreach ($references as $reference) {
                if (Str::contains($timeReferenceStringValue, $reference)) {
                    $value = $this->getPeriodValue($timeReferenceStringValue);

                    if (is_numeric($value)) {
                        try {
                            return Carbon::now()->{'sub'.ucfirst($method)}($value);
                        } catch (\Throwable $exception) {
                            throw new InvalidTimeReferenceCarbonInstance($exception->getMessage());
                        }
                    } else {
                        throw new InvalidTimeReferenceCarbonInstance("Invalid time reference format: Numeric value expected before unit.");
                    }
                }
            }
        }

        throw new InvalidTimeReferenceCarbonInstance("Invalid time reference format: Unknown unit.");
    }

    /**
     * Extract the first sequence of digits/numbers from the beginning of the string
     * e.g., "5 years ago" -> "5"; "years 5" -> null
     *
     * @param string $timeReference
     * @return int|null
     */
    private function getPeriodValue(string $timeReference): ?int
    {
        if (preg_match('/^\s*(\d+)/', $timeReference, $matches)) {
            return (int) $matches[1];
        }

        return null;
    }
}
