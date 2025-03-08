[![Latest Version on Packagist](https://img.shields.io/packagist/v/davealex/laravel-time-period-reference.svg?style=flat-square)](https://packagist.org/packages/davealex/laravel-time-period-reference)
[![Total Downloads](https://img.shields.io/packagist/dt/davealex/laravel-time-period-reference.svg?style=flat-square)](https://packagist.org/packages/davealex/laravel-time-period-reference)
![GitHub Actions](https://github.com/davealex/laravel-time-period-reference/actions/workflows/main.yml/badge.svg)

# Laravel Time Period Reference

This Laravel package provides a simple way to convert time period references (e.g., "2 days", "1 week") into `Carbon` instances.

## Installation

You can install the package via composer:

```bash
composer require davealex/laravel-time-period-reference
```

## Configuration

Publish the configuration file to customize the time units and their corresponding time references:

```bash
php artisan vendor:publish --provider="Davealex\LaravelTimePeriodReference\LaravelTimePeriodReferenceServiceProvider"
```

### Example configuration

```php
<?php

return [
    'units' => [
        'day' => ['day', 'days'],
        'week' => ['week', 'weeks'],
        'month' => ['month', 'months'],
        'year' => ['year', 'years'],
    ],
];
```

## Usage
Use the LaravelTimePeriodReference class to convert time period references into Carbon instances.

```php

// 1. Using facade
use Davealex\LaravelTimePeriodReference\LaravelTimePeriodReferenceFacade;

$carbonInstance = LaravelTimePeriodReferenceFacade::toCarbonInstance('2 days ago');

dd($carbonInstance->toString())

// 2. Manually instantiating the service class
use Davealex\LaravelTimePeriodReference\LaravelTimePeriodReference;
use Illuminate\Config\Repository;
use Illuminate\Support\Carbon;

// Create a configuration array or use the config repository.
$config = new Repository(config('time-period-reference'));

$timePeriodReference = new LaravelTimePeriodReference($config);

// Convert a string time reference to a Carbon instance.
$carbonInstance = $timePeriodReference->toCarbonInstance('2 days ago');

if ($carbonInstance instanceof Carbon) {
    echo $carbonInstance->toDateString(); // Output: e.g., 2024-10-26 (if today is 2024-10-28)
}

// Convert a BackedEnum time reference to a Carbon instance.
enum TimeReferenceEnum: string
{
    case TWO_WEEKS = '2 weeks ago';
}

$enumInstance = TimeReferenceEnum::TWO_WEEKS;

$carbonInstanceFromEnum = $timePeriodReference->toCarbonInstance($enumInstance);

if ($carbonInstanceFromEnum instanceof Carbon) {
    echo $carbonInstanceFromEnum->toDateString();
}
```
## Exceptions

The package throws `Davealex\LaravelTimePeriodReference\Exceptions\InvalidTimeReferenceCarbonInstance` in the following cases:

* Invalid time reference format (e.g., non-numeric value before unit).
* Unknown time unit.
* Error during Carbon instance creation.

## Testing

To run the tests, use PHPUnit:

```bash
./vendor/bin/phpunit 
```
or 

```bash
composer test
```

### Changelog

Please see [CHANGELOG](CHANGELOG.md) for more information what has changed recently.

## Contributing

Please feel free to contribute by submitting issues or pull requests.

### Security

If you discover any security related issues, please email daveabiola@gmail.com instead of using the issue tracker.

## Credits

-   [David Olaleye](https://github.com/davealex)

## License

This package is open-source and available under the MIT license.

