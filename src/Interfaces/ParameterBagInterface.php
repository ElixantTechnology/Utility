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
namespace Elixant\Utility\Interfaces;

use Countable;
use BackedEnum;
use ArrayAccess;
use ArrayIterator;
use JsonSerializable;
use IteratorAggregate;

/**
 * ParameterBagInterface Interface
 * Created Apr 06, 2024.
 *
 * ParameterBag is a container for key/value pairs. It is similar to the PHP
 *  $_GET and $_POST superglobals, but provides additional functionality for
 *  validating and filtering the data. The ParameterBag class implements the
 *  ParameterBagInterface interface, which extends the IteratorAggregate,
 *  Countable, JsonSerializable, and ArrayAccess interfaces.
 *
 *  The ParameterBag class provides methods for retrieving, setting, and
 *  removing items from the bag. It also provides methods for filtering and
 *  validating the items in the bag. The ParameterBag class is used by the
 *  Request class to store request parameters.
 *
 *  Items in the ParameterBag are stored as key/value pairs in an associative
 *  array. The keys are strings, and the values can be of any type. The items
 *  are also accessible using array syntax, e.g. $bag['key'] and dot notation,
 *  e.g. $bag->get('key.sub-key'). The ParameterBag class also provides methods
 *  for converting the items to strings, integers, booleans, and enums.
 *
 * @package         Elixant\Utility\Interfaces::ParameterBagInterface
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 *
 * @implements \IteratorAggregate<string, mixed>
 */
interface ParameterBagInterface
    extends IteratorAggregate, Countable, JsonSerializable, ArrayAccess
{
    /**
     * Returns an array containing all the items in the bag, unless a key is
     * specified. If a key is specified, the value of that key will be
     * returned. If the key does not exist, an exception will be thrown. The
     * value of the key must be an array.
     *
     * @param string|null $key
     *
     * @return array
     * @throws \Exception
     */
    public function all(?string $key = null): array;
    
    /**
     * Returns an array containing all the keys in the bag.
     *
     * @return array
     */
    public function keys(): array;
    
    /**
     * Replaces the current items in the bag with a new set of items.
     *
     * @param array $items
     */
    public function replace(array $items = []): void;
    
    /**
     * Adds items to the bag.
     *
     * @param array $items
     */
    public function add(array $items = []): void;
    
    /**
     * Returns the value of the specified key, or the default value if the key
     * does not exist.
     *
     * @param string $key
     * @param mixed  $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed;
    
    /**
     * Sets the value of the specified key.
     *
     * @param string $key
     * @param mixed  $value
     */
    public function set(string $key, mixed $value): void;
    
    /**
     * Returns true if the specified key is defined.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool;
    
    /**
     * Removes the specified key from the bag.
     *
     * @param string $key
     */
    public function remove(string $key): void;
    
    /**
     * Returns an iterator for items.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): ArrayIterator;
    
    /**
     * Returns the alphabetic characters of the parameter value.
     *
     * @param string $key     The key to retrieve the value from.
     * @param string $default The default value to return if the key does not
     *                        exist.
     *
     * @return string
     */
    public function getAlpha(string $key, string $default = ''): string;
    
    /**
     * Returns the alphabetic characters and digits of the parameter value.
     *
     * @param string $key     The key to retrieve the value from.
     * @param string $default The default value to return if the key does not
     *                        exist.
     *
     * @return string
     */
    public function getAlnum(string $key, string $default = ''): string;
    
    /**
     * Returns the digits of the parameter value.
     *
     * @param string $key     The key to retrieve the value from.
     * @param string $default The default value to return if the key does not
     *                        exist.
     *
     * @return string
     */
    public function getDigits(string $key, string $default = ''): string;
    
    /**
     * Returns the parameter as a string.
     *
     * @param string $key     The key to retrieve the value from.
     * @param string $default The default value to return if the key does not
     *                        exist.
     *
     * @return string
     */
    public function getString(string $key, string $default = ''): string;
    
    /**
     * Returns the parameter as an integer.
     *
     * @param string $key     The key to retrieve the value from.
     * @param int    $default The default value to return if the key does not
     *                        exist.
     *
     * @return int
     */
    public function getInt(string $key, int $default = 0): int;
    
    /**
     * Returns the parameter as boolean.
     *
     * @param string $key     The key to retrieve the value from.
     * @param bool   $default The default value to return if the key does not
     *                        exist.
     *
     * @return bool
     */
    public function getBoolean(string $key, bool $default = false): bool;
    
    /**
     * Returns the parameter value converted to an enum.
     *
     * @template T of \BackedEnum
     *
     * @param string           $key
     * @param class-string<T>  $class
     * @param \BackedEnum|null $default
     *
     * @return \BackedEnum|null
     */
    public function getEnum(string $key, string $class,
        ?BackedEnum $default = null
    ): ?BackedEnum;
    
    /**
     * Filter key.
     *
     * @param int                                     $filter  FILTER_*
     *                                                         constant
     * @param int|array{flags?: int, options?: array} $options Flags from
     *                               FILTER_* constants
     *
     * @see https://php.net/filter-var
     */
    public function filter(string $key, mixed $default = null,
        int $filter = FILTER_DEFAULT, mixed $options = []
    ): mixed;
}
