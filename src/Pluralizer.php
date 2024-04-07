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
 * @copyright    2023 (C) Elixant Corporation.
 * @license      MIT License
 * @author       Alexander Schmautz <a.schmautz@outlook.com>
 */
declare(strict_types = 1);
namespace Elixant\Utility;

use Countable;
use Doctrine\Inflector\Inflector;
use Doctrine\Inflector\InflectorFactory;

/**
 * Pluralizer Class
 * Created Apr 07, 2024.
 *
 * The Pluralizer class is a utility class that provides a simple way to
 * create and manage pluralized values. The class provides a number of
 * convenience methods for creating and managing pluralized values.
 *
 * @package         Elixant\Utility::Pluralizer
 * @class           Pluralizer
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class Pluralizer
{
    /**
     * Uncountable non-nouns word forms.
     *
     * Contains words supported by
     * Doctrine/Inflector/Rules/English/Uninflected.php
     *
     * @var string[]
     */
    public static array $uncountable
        = [
            'recommended',
            'related',
        ];
    /**
     * The cached inflector instance.
     *
     * @var \Doctrine\Inflector\Inflector|null
     */
    protected static Inflector|null $inflector;
    /**
     * The language that should be used by the inflector.
     *
     * @var string
     */
    protected static string $language = 'english';
    
    /**
     * Get the plural form of an English word.
     *
     * @param string               $value
     * @param \Countable|array|int $count
     *
     * @return string
     */
    public static function plural(string $value, Countable|array|int $count = 2
    ): string {
        if (is_countable($count)) {
            $count = count($count);
        }
        if ((int)abs($count) === 1 || static::uncountable($value)
            || preg_match(
                '/^(.*)[A-Za-z0-9\x{0080}-\x{FFFF}]$/u', $value
            ) == 0
        ) {
            return $value;
        }
        $plural = static::inflector()->pluralize($value);
        
        return static::matchCase($plural, $value);
    }
    
    /**
     * Determine if the given value is uncountable.
     *
     * @param string $value
     *
     * @return bool
     */
    protected static function uncountable(string $value): bool
    {
        return in_array(strtolower($value), static::$uncountable);
    }
    
    /**
     * Get the inflector instance.
     *
     * @return \Doctrine\Inflector\InflectorFactory|\Elixant\Utility\Pluralizer|\Doctrine\Inflector\Inflector
     */
    public static function inflector(): InflectorFactory|Pluralizer|Inflector
    {
        if (is_null(static::$inflector)) {
            static::$inflector = InflectorFactory::createForLanguage(
                static::$language
            )->build();
        }
        
        return static::$inflector;
    }
    
    /**
     * Attempt to match the case on two strings.
     *
     * @param string $value
     * @param string $comparison
     *
     * @return string
     */
    protected static function matchCase(string $value, string $comparison
    ): string {
        $functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];
        foreach ($functions as $function) {
            if ($function($comparison) === $comparison) {
                return $function($value);
            }
        }
        
        return $value;
    }
    
    /**
     * Get the singular form of an English word.
     *
     * @param string $value
     *
     * @return string
     */
    public static function singular(string $value): string
    {
        $singular = static::inflector()->singularize($value);
        
        return static::matchCase($singular, $value);
    }
    
    /**
     * Specify the language that should be used by the inflector.
     *
     * @param string $language
     *
     * @return void
     */
    public static function useLanguage(string $language): void
    {
        static::$language  = $language;
        static::$inflector = null;
    }
}
