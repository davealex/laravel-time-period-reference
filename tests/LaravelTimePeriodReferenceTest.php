<?php

namespace Davealex\LaravelTimePeriodReference\Tests;

use Davealex\LaravelTimePeriodReference\Exceptions\InvalidTimeReferenceCarbonInstance;
use Davealex\LaravelTimePeriodReference\LaravelTimePeriodReference;
use Davealex\LaravelTimePeriodReference\Tests\Enums\TimeReferenceTestEnum;
use Illuminate\Config\Repository;
use Illuminate\Support\Carbon;
use PHPUnit\Framework\TestCase;

class LaravelTimePeriodReferenceTest extends TestCase
{
    private LaravelTimePeriodReference $timePeriodReference;

    protected function setUp(): void
    {
        parent::setUp();

        $config = new Repository([
            'units' => [
                'seconds' => ['second', 'seconds', 'sec'],
                'minutes' => ['minutes', 'minute', 'min'],
                'hours' => ['hours', 'hour', 'hrs'],
                'days' => ['days', 'day'],
                'weeks' => ['weeks', 'week'],
                'months' => ['months', 'month'],
                'years' => ['years', 'year'],
            ]
        ]);

        $this->timePeriodReference = new LaravelTimePeriodReference($config);
    }

    /**
     * @throws InvalidTimeReferenceCarbonInstance
     */
    public function testToCarbonInstanceWithValidString(): void
    {
        $result = $this->timePeriodReference->toCarbonInstance('2 days ago');
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals(Carbon::now()->subDays(2)->toDateString(), $result->toDateString());
    }

    /**
     * @throws InvalidTimeReferenceCarbonInstance
     */
    public function testToCarbonInstanceWithValidStringBackedEnum(): void
    {
        $enum = TimeReferenceTestEnum::tryFrom('2 weeks ago');
        $this->assertInstanceOf(\BackedEnum::class, $enum);
        $result = $this->timePeriodReference->toCarbonInstance($enum);
        $this->assertInstanceOf(Carbon::class, $result);
        $this->assertEquals(Carbon::now()->subWeeks(2)->toDateString(), $result->toDateString());
    }

    public function testToCarbonInstanceWithInvalidUnit(): void
    {
        $this->expectException(InvalidTimeReferenceCarbonInstance::class);
        $this->expectExceptionMessage('Invalid time reference format: Unknown unit.');
        $this->timePeriodReference->toCarbonInstance('2 decades ago');
    }

    public function testToCarbonInstanceWithInvalidFormat(): void
    {
        $this->expectException(InvalidTimeReferenceCarbonInstance::class);
        $this->expectExceptionMessage('Invalid time reference format: Numeric value expected before unit.');
        $this->timePeriodReference->toCarbonInstance('days 2');
    }

    public function testToCarbonInstanceWithInvalidNumericalValue(): void
    {
        $this->expectException(InvalidTimeReferenceCarbonInstance::class);
        $this->expectExceptionMessage('Invalid time reference format: Numeric value expected before unit.');
        $this->timePeriodReference->toCarbonInstance('two days ago');
    }

    public function testToCarbonInstanceWithExceptionFromCarbon(): void
    {
        $config = new Repository([
            'units' => [
                'invalid' => ['invalidMethod'],
            ],
        ]);

        $timePeriodReference = new LaravelTimePeriodReference($config);

        $this->expectException(InvalidTimeReferenceCarbonInstance::class);
        $timePeriodReference->toCarbonInstance('1 invalid');
    }

    /**
     * @throws InvalidTimeReferenceCarbonInstance
     */
    public function testToCarbonInstanceIsCaseInsensitive(): void
    {
        Carbon::setTestNow(Carbon::parse('2020-01-01'));
        $result = $this->timePeriodReference->toCarbonInstance('5 YEARS ago');
        $this->assertEquals('2015-01-01', $result->toDateString());
        Carbon::setTestNow();
    }
}
