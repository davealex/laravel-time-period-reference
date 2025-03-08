<?php

namespace Davealex\LaravelTimePeriodReference;

use Davealex\LaravelTimePeriodReference\Exceptions\InvalidTimeReferenceCarbonInstance;
use Illuminate\Contracts\Config\Repository;
use Illuminate\Support\Carbon;
use Illuminate\Support\Str;
use BackedEnum;

readonly class LaravelTimePeriodReference
{
    public function __construct(
        private Repository $config
    ) {}

    /**
     * @param BackedEnum|string $timeReference
     * @return Carbon
     * @throws InvalidTimeReferenceCarbonInstance
     */
    public function toCarbonInstance(BackedEnum|string $timeReference): Carbon
    {
        if ($timeReference instanceof BackedEnum) {
            $timeReferenceStringValue = strtolower(trim($timeReference->value));
        }

        $timeReferenceStringValue = strtolower(trim($timeReferenceStringValue ?? $timeReference));

        foreach ($this->config['units'] as $method => $references) {
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
     * @param string $timeReference
     * @return mixed
     */
    private function getPeriodValue(string $timeReference): mixed
    {
        $parts = explode(" ", $timeReference);

        return $parts[0];
    }
}
