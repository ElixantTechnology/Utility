<?php
/**
 * Elixant Platform Framework Component
 *
 * Elixant Platform
 * Copyright (c) 2023 Elixant Corporation.
 *
 * Permission is hereby granted, free of charge, to any
 * person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @package      elixant/service-container
 * @copyright    2023 (C) Elixant Corporation.
 * @license      MIT License
 * @author       Aexander Schmautz <a.schmautz@outlook.com>
 */
declare(strict_types = 1);
namespace Elixant\Utility;

use Carbon\Factory;
use InvalidArgumentException;
use Symfony\Contracts\Translation\TranslatorInterface;

/**
 * DateFactory Class
 * Created Apr 07, 2024.
 *
 * The DateFactory class is a utility class that provides a simple way to
 * create dates using the Carbon library. The class provides a number of
 * convenience methods for creating dates.
 *
 * @package         Elixant\Utility::DateFactory
 * @class           DateFactory
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 *
 * @see             https://carbon.nesbot.com/docs/
 * @see             https://github.com/briannesbitt/Carbon/blob/master/src/Carbon/Factory.php
 *
 * @method Carbon create($year = 0, $month = 1, $day = 1, $hour = 0, $minute =
 *         0, $second = 0, $tz = null)
 * @method Carbon createFromDate($year = null, $month = null, $day = null, $tz
 *         = null)
 * @method Carbon|false createFromFormat($format, $time, $tz = null)
 * @method Carbon createFromTime($hour = 0, $minute = 0, $second = 0, $tz =
 *         null)
 * @method Carbon createFromTimeString($time, $tz = null)
 * @method Carbon createFromTimestamp($timestamp, $tz = null)
 * @method Carbon createFromTimestampMs($timestamp, $tz = null)
 * @method Carbon createFromTimestampUTC($timestamp)
 * @method Carbon createMidnightDate($year = null, $month = null, $day = null,
 *         $tz = null)
 * @method Carbon|false createSafe($year = null, $month = null, $day = null,
 *         $hour = null, $minute = null, $second = null, $tz = null)
 * @method void disableHumanDiffOption($humanDiffOption)
 * @method void enableHumanDiffOption($humanDiffOption)
 * @method mixed executeWithLocale($locale, $func)
 * @method Carbon fromSerialized($value)
 * @method array getAvailableLocales()
 * @method array getDays()
 * @method int getHumanDiffOptions()
 * @method array getIsoUnits()
 * @method array getLastErrors()
 * @method string getLocale()
 * @method int getMidDayAt()
 * @method Carbon|null getTestNow()
 * @method TranslatorInterface getTranslator()
 * @method int getWeekEndsAt()
 * @method int getWeekStartsAt()
 * @method array getWeekendDays()
 * @method bool hasFormat($date, $format)
 * @method bool hasMacro($name)
 * @method bool hasRelativeKeywords($time)
 * @method bool hasTestNow()
 * @method Carbon instance($date)
 * @method bool isImmutable()
 * @method bool isModifiableUnit($unit)
 * @method bool isMutable()
 * @method bool isStrictModeEnabled()
 * @method bool localeHasDiffOneDayWords($locale)
 * @method bool localeHasDiffSyntax($locale)
 * @method bool localeHasDiffTwoDayWords($locale)
 * @method bool localeHasPeriodSyntax($locale)
 * @method bool localeHasShortUnits($locale)
 * @method void macro($name, $macro)
 * @method Carbon|null make($var)
 * @method Carbon maxValue()
 * @method Carbon minValue()
 * @method void mixin($mixin)
 * @method Carbon now($tz = null)
 * @method Carbon parse($time = null, $tz = null)
 * @method string pluralUnit(string $unit)
 * @method void resetMonthsOverflow()
 * @method void resetToStringFormat()
 * @method void resetYearsOverflow()
 * @method void serializeUsing($callback)
 * @method void setHumanDiffOptions($humanDiffOptions)
 * @method bool setLocale($locale)
 * @method void setMidDayAt($hour)
 * @method void setTestNow($testNow = null)
 * @method void setToStringFormat($format)
 * @method void setTranslator(TranslatorInterface $translator)
 * @method void setUtf8($utf8)
 * @method void setWeekEndsAt($day)
 * @method void setWeekStartsAt($day)
 * @method void setWeekendDays($days)
 * @method bool shouldOverflowMonths()
 * @method bool shouldOverflowYears()
 * @method string singularUnit(string $unit)
 * @method Carbon today($tz = null)
 * @method Carbon tomorrow($tz = null)
 * @method void useMonthsOverflow($monthsOverflow = true)
 * @method void useStrictMode($strictModeEnabled = true)
 * @method void useYearsOverflow($yearsOverflow = true)
 * @method Carbon yesterday($tz = null)
 */
class DateFactory
{
    /**
     * The default class that will be used for all created dates.
     *
     * @var string
     */
    const DEFAULT_CLASS_NAME = Carbon::class;
    /**
     * The type (class) of dates that should be created.
     *
     * @var ?string
     */
    protected static ?string $dateClass;
    /**
     * This callable may be used to intercept date creation.
     *
     * @var callable
     */
    protected static $callable;
    /**
     * The Carbon factory that should be used when creating dates.
     *
     * @var ?object
     */
    protected static ?object $factory;
    
    /**
     * Use the given handler when generating dates (class name, callable, or
     * factory).
     *
     * @param mixed $handler
     *
     * @return mixed
     *
     * @throws \InvalidArgumentException
     */
    public static function use(mixed $handler): mixed
    {
        if (is_callable($handler) && is_object($handler)) {
            static::useCallable($handler);
        } elseif (is_string($handler)) {
            static::useClass($handler);
        } elseif ($handler instanceof Factory) {
            static::useFactory($handler);
        }
        throw new InvalidArgumentException(
            'Invalid date creation handler. Please provide a class name, callable, or Carbon factory.'
        );
    }
    
    /**
     * Execute the given callable on each date creation.
     *
     * @param callable $callable
     *
     * @return void
     */
    public static function useCallable(callable $callable): void
    {
        static::$callable  = $callable;
        static::$dateClass = null;
        static::$factory   = null;
    }
    
    /**
     * Use the given date type (class) when generating dates.
     *
     * @param string $dateClass
     *
     * @return void
     */
    public static function useClass(string $dateClass): void
    {
        static::$dateClass = $dateClass;
        static::$factory   = null;
        static::$callable  = null;
    }
    
    /**
     * Use the given Carbon factory when generating dates.
     *
     * @param object $factory
     *
     * @return void
     */
    public static function useFactory(object $factory): void
    {
        static::$factory   = $factory;
        static::$dateClass = null;
        static::$callable  = null;
    }
    
    /**
     * Use the default date class when generating dates.
     *
     * @return void
     */
    public static function useDefault(): void
    {
        static::$dateClass = null;
        static::$callable  = null;
        static::$factory   = null;
    }
    
    /**
     * Handle dynamic calls to generate dates.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     *
     * @throws \RuntimeException
     */
    public function __call(string $method, array $parameters)
    {
        $defaultClassName = static::DEFAULT_CLASS_NAME;
        // Using callable to generate dates...
        if (static::$callable) {
            return call_user_func(
                static::$callable, $defaultClassName::$method(...$parameters)
            );
        }
        // Using Carbon factory to generate dates...
        if (static::$factory) {
            return static::$factory->$method(...$parameters);
        }
        $dateClass = static::$dateClass ? : $defaultClassName;
        // Check if the date can be created using the public class method...
        if (method_exists($dateClass, $method)
            || method_exists($dateClass, 'hasMacro')
            && $dateClass::hasMacro(
                $method
            )
        ) {
            return $dateClass::$method(...$parameters);
        }
        // If that fails, create the date with the default class...
        $date = $defaultClassName::$method(...$parameters);
        // If the configured class has an "instance" method, we'll try to pass our date into there...
        if (method_exists($dateClass, 'instance')) {
            return $dateClass::instance($date);
        }
        
        // Otherwise, assume the configured class has a DateTime compatible constructor...
        return new $dateClass(
            $date->format('Y-m-d H:i:s.u'), $date->getTimezone()
        );
    }
}
