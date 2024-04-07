<?php declare(strict_types = 1);
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
 * @package      elixant/utility
 * @copyright    2023 (C) Elixant Corporation.
 * @license      MIT License
 * @author       Alexander Schmautz <a.schmautz@outlook.com>
 */
use Elixant\Utility\Arr;
use Elixant\Utility\Str;
use Elixant\Utility\Interfaces\Htmlable;
use Elixant\Utility\Interfaces\DeferringDisplayableValue;

if ( ! function_exists('append_config')) {
    /**
     * Assign high numeric IDs to a config item to force appending.
     *
     * @param array $array
     *
     * @return array
     */
    function append_config(array $array): array
    {
        $start = 9999;
        foreach ($array as $key => $value) {
            if (is_numeric($key)) {
                $start++;
                $array[$start] = Arr::pull($array, $key);
            }
        }
        
        return $array;
    }
}
if ( ! function_exists('blank')) {
    /**
     * Determine if the given value is "blank".
     *
     * @param mixed $value
     *
     * @return bool
     */
    function blank(mixed $value): bool
    {
        if (is_null($value)) {
            return true;
        }
        if (is_string($value)) {
            return trim($value) === '';
        }
        if (is_numeric($value) || is_bool($value)) {
            return false;
        }
        if ($value instanceof Countable) {
            return count($value) === 0;
        }
        
        return empty($value);
    }
}
if ( ! function_exists('class_basename')) {
    /**
     * Get the class "basename" of the given object / class.
     *
     * @param object|string $class
     *
     * @return string
     */
    function class_basename(object|string $class): string
    {
        $class = is_object($class) ? get_class($class) : $class;
        
        return basename(str_replace('\\', '/', $class));
    }
}
if ( ! function_exists('class_uses_recursive')) {
    /**
     * Returns all traits used by a class, its parent classes and trait of their traits.
     *
     * @param object|string $class
     *
     * @return array
     */
    function class_uses_recursive(object|string $class): array
    {
        if (is_object($class)) {
            $class = get_class($class);
        }
        $results = [];
        foreach (
            array_reverse(class_parents($class) ? : []) + [$class => $class] as
            $class
        ) {
            $results += trait_uses_recursive($class);
        }
        
        return array_unique($results);
    }
}
if ( ! function_exists('e')) {
    /**
     * Encode HTML special characters in a string.
     *
     * @param \BackedEnum|\Elixant\Utility\Interfaces\DeferringDisplayableValue|\Elixant\Utility\Interfaces\Htmlable|string|null $value
     * @param bool                                                                                                               $doubleEncode
     *
     * @return string
     */
    function e(
        BackedEnum|DeferringDisplayableValue|Htmlable|string|null $value,
        bool                                                      $doubleEncode = true
    ): string {
        if ($value instanceof DeferringDisplayableValue) {
            $value = $value->resolveDisplayableValue();
        }
        if ($value instanceof Htmlable) {
            return $value->toHtml();
        }
        if ($value instanceof BackedEnum) {
            $value = $value->value;
        }
        
        return htmlspecialchars(
            $value ?? '', ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8', $doubleEncode
        );
    }
}
if ( ! function_exists('filled')) {
    /**
     * Determine if a value is "filled".
     *
     * @param mixed $value
     *
     * @return bool
     */
    function filled(mixed $value): bool
    {
        return ! blank($value);
    }
}
if ( ! function_exists('literal')) {
    /**
     * Return a new literal or anonymous object using named arguments.
     *
     * @param mixed ...$arguments
     *
     * @return \stdClass
     */
    function literal(...$arguments): stdClass
    {
        if (count($arguments) === 1 && array_is_list($arguments)) {
            return $arguments[0];
        }
        
        return (object)$arguments;
    }
}
if ( ! function_exists('object_get')) {
    /**
     * Get an item from an object using "dot" notation.
     *
     * @param object      $object
     * @param string|null $key
     * @param mixed|null  $default
     *
     * @return mixed
     */
    function object_get(object $object, ?string $key, mixed $default = null
    ): mixed {
        if (is_null($key) || trim($key) === '') {
            return $object;
        }
        foreach (explode('.', $key) as $segment) {
            if ( ! is_object($object) || ! isset($object->{$segment})) {
                return value($default);
            }
            $object = $object->{$segment};
        }
        
        return $object;
    }
}
if ( ! function_exists('str')) {
    /**
     * Get a new stringable object from the given string.
     *
     * @param string|null $string
     *
     * @return \Elixant\Utility\Stringable@6239
     */
    function str(string $string = null): \Elixant\Utility\Stringable
    {
        if (func_num_args() === 0) {
            return new class extends Elixant\Utility\Stringable {
                public function __toString()
                {
                    return '';
                }
                
                public function __call(string $method, array $parameters
                ): Elixant\Utility\Stringable {
                    return Str::$method(...$parameters);
                }
            };
        }
        
        return Str::of($string);
    }
}
if ( ! function_exists('throw_if')) {
    /**
     * Throw the given exception if the given condition is true.
     *
     * @template TException of \Throwable
     *
     * @param mixed             $condition
     * @param \Throwable|string $exception
     * @param mixed             ...$parameters
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    function throw_if(
        mixed $condition, Throwable|string $exception = 'RuntimeException', ...$parameters
    ): mixed {
        if ($condition) {
            if (is_string($exception) && class_exists($exception)) {
                $exception = new $exception(...$parameters);
            }
            throw is_string($exception) ? new RuntimeException($exception)
                : $exception;
        }
        
        return $condition;
    }
}
if ( ! function_exists('throw_unless')) {
    /**
     * Throw the given exception unless the given condition is true.
     *
     * @template TException of \Throwable
     *
     * @param mixed             $condition
     * @param \Throwable|string $exception
     * @param mixed             ...$parameters
     *
     * @return mixed
     *
     * @throws \Throwable
     */
    function throw_unless(
        mixed $condition,   Throwable|string $exception = 'RuntimeException', ...$parameters
    ): mixed {
        throw_if(! $condition, $exception, ...$parameters);
        
        return $condition;
    }
}
if ( ! function_exists('trait_uses_recursive')) {
    /**
     * Returns all traits used by a trait and its traits.
     *
     * @param object|string $trait
     *
     * @return array
     */
    function trait_uses_recursive(object|string $trait): array
    {
        $traits = class_uses($trait) ? : [];
        foreach ($traits as $trait) {
            $traits += trait_uses_recursive($trait);
        }
        
        return $traits;
    }
}
if ( ! function_exists('transform')) {
    /**
     * Transform the given value if it is present.
     *
     * @template TValue of mixed
     * @template TReturn of mixed
     * @template TDefault of mixed
     *
     * @param TValue                    $value
     * @param callable(TValue): TReturn $callback
     * @param TDefault|null             $default
     *
     * @return ($value is empty ? ($default is null ? null : TDefault) : TReturn)
     */
    function transform(mixed $value, callable $callback, mixed $default = null)
    {
        if (filled($value)) {
            return $callback($value);
        }
        if (is_callable($default)) {
            return $default($value);
        }
        
        return $default;
    }
}
if ( ! function_exists('windows_os')) {
    /**
     * Determine whether the current environment is Windows based.
     *
     * @return bool
     */
    function windows_os(): bool
    {
        return PHP_OS_FAMILY === 'Windows';
    }
}
if ( ! function_exists('with')) {
    /**
     * Return the given value, optionally passed through the given callback.
     *
     * @template TValue
     * @template TReturn
     *
     * @param TValue                             $value
     * @param (callable(TValue): (TReturn))|null $callback
     *
     * @return ($callback is null ? TValue : TReturn)
     */
    function with($value, callable $callback = null)
    {
        return is_null($callback) ? $value : $callback($value);
    }
}
