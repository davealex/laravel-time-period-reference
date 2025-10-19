[![Latest Version on Packagist](https://img.shields.io/packagist/v/davealex/laravel-time-period-reference.svg?style=flat-square)](https://packagist.org/packages/davealex/laravel-time-period-reference)
[![Total Downloads](https://img.shields.io/packagist/dt/davealex/laravel-time-period-reference.svg?style=flat-square)](https://packagist.org/packages/davealex/laravel-time-period-reference)
[![run-tests](https://github.com/davealex/laravel-time-period-reference/actions/workflows/main.yml/badge.svg)](https://github.com/davealex/laravel-time-period-reference/actions/workflows/main.yml)

# **Laravel Time Period Reference**

A clean, flexible utility package for Laravel that converts natural-language time period references `(e.g., "3 hours ago", "1 week")` into precise **Carbon instances**.

This is ideal for handling user inputs, API requests, or configuration values that define a time window relative to `now()`. It supports custom units and integrates seamlessly with `PHP 8.1+ Enums`.

## **1. Installation**

You can install the package via Composer:

`composer require davealex/laravel-time-period-reference`

## **2. Configuration**

By default, the package supports common references (seconds, minutes, days, weeks, etc.). To customize or extend these references, publish the configuration file:

`php artisan vendor:publish --provider="Davealex\LaravelTimePeriodReference\LaravelTimePeriodReferenceServiceProvider" --tag="config"`

This creates `config/laravel-time-period-reference.php`.

### **Example Configuration (config/laravel-time-period-reference.php)**

The array keys correspond to the standard **Carbon method name** (e.g., `subDays`, `subWeeks`), and the values are the acceptable string references.

````
<?php

return [  
    'units' => [  
    // Keys map to Carbon's sub[Unit]() methods (e.g., subSeconds(X))  
    'seconds' => ['second', 'seconds', 'sec'],  
    'minutes' => ['minutes', 'minute', 'min'],  
    'hours' => ['hours', 'hour', 'hrs'],  
    'days' => ['days', 'day'],  
    // ... and so on  
    ],
];
````

## **3. Usage**

The core functionality is exposed via the **Facade**, the **Service Container**, or a **helper function** (if you choose to define one).

### **Converting a String Reference**

Use the `toCarbonInstance()` method with a string value like "2 days ago", "5 years", or "10 min". The method returns a fully configured Carbon instance relative to the current time.

````
use Davealex\LaravelTimePeriodReference\Facades\LaravelTimePeriodReference;

// Note: You must import the Facades namespace

// The reference string is parsed for the number and unit.  
$carbonInstance = LaravelTimePeriodReference::toCarbonInstance('2 days ago');
// $carbonInstance is now Carbon::now()->subDays(2)

// Output: e.g., 2024-10-26 (if today is 2024-10-28)  
dd($carbonInstance->toDateString());
````

### **Converting a Backed Enum**

For clean, type-safe code, the package supports PHP 8.1+ BackedEnums whose values are the reference strings.

````
// Define your Enum  
enum TimeReferenceEnum: string  
{  
case TWO_WEEKS_AGO = '2 weeks ago';  
case FIVE_YEARS = '5 years';  
}

// Pass the Enum instance directly to the service  
$carbonInstance = LaravelTimePeriodReference::toCarbonInstance(TimeReferenceEnum::FIVE_YEARS);  
// $carbonInstance is now Carbon::now()->subYears(5)
````

### **Manual Service Injection**

You can also type-hint the service class in your controllers or other services:

````
use Davealex\LaravelTimePeriodReference\LaravelTimePeriodReference;

class ReportController extends Controller  
{  
    public function __construct(  
        private LaravelTimePeriodReference $timeReferenceService  
    ) {}

    public function show(string $period)  
    {  
        // $period might be '3 months' from a route parameter  
        $startTime = $this->timeReferenceService->toCarbonInstance($period);  
        // ... fetch data using $startTime  
    }  
}
````

## **4. Exceptions**

The package throws a single exception, `Davealex\LaravelTimePeriodReference\Exceptions\InvalidTimeReferenceCarbonInstance`, which covers all parsing and creation errors:

* **Invalid Format:** (e.g., "days 2", "two days ago") - Numeric value expected before the unit.
* **Unknown Unit:** (e.g., "2 decades ago") - The unit is not defined in the configuration.
* **Carbon Error:** An internal error occurred during Carbon instance creation.

## **5. Testing**

To run the package tests, use Composer:

`composer test`

or

`./vendor/bin/phpunit`

## **6. Contribution and License**

### **Changelog**

Please see [CHANGELOG.md](http://docs.google.com/CHANGELOG.md) for more information on what has changed recently.

### **Contributing**

Please feel free to contribute by submitting issues or pull requests.

### **Security**

If you discover any security related issues, please email [daveabiola@gmail.com](mailto:daveabiola@gmail.com) instead of using the issue tracker.

### **Credits**

* [David Olaleye](https://github.com/davealex)
* All Contributors

### **License**

This package is open-source and available under the MIT license.
