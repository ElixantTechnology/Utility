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

use Closure;
use Exception;
use TypeError;
use BackedEnum;
use ValueError;
use ArrayIterator;
use UnexpectedValueException;
use InvalidArgumentException;
use Elixant\Utility\Traits\DumpableTrait;
use Elixant\Utility\Interfaces\ParameterBagInterface;

use function count;
use function is_array;
use function is_scalar;
use function is_object;

use const FILTER_DEFAULT;
use const FILTER_CALLBACK;
use const FILTER_VALIDATE_INT;
use const FILTER_VALIDATE_BOOL;
use const FILTER_REQUIRE_ARRAY;
use const FILTER_REQUIRE_SCALAR;
use const FILTER_NULL_ON_FAILURE;

/**
 * ParameterBag Class
 * Created Apr 06, 2024.
 *
 * ParameterBag is a container for key/value pairs. It is similar to the PHP
 * $_GET and $_POST superglobals, but provides additional functionality for
 * validating and filtering the data. The ParameterBag class implements the
 * ParameterBagInterface interface, which extends the IteratorAggregate,
 * Countable, JsonSerializable, and ArrayAccess interfaces.
 *
 * The ParameterBag class provides methods for retrieving, setting, and
 * removing items from the bag. It also provides methods for filtering and
 * validating the items in the bag. The ParameterBag class is used by the
 * Request class to store request parameters.
 *
 * Items in the ParameterBag are stored as key/value pairs in an associative
 * array. The keys are strings, and the values can be of any type. The items
 * are also accessible using array syntax, e.g. $bag['key'] and dot notation,
 * e.g. $bag->get('key.sub-key'). The ParameterBag class also provides methods
 * for converting the items to strings, integers, booleans, and enums.
 *
 * @package         Elixant\Utility::ParameterBag
 * @class           ParameterBag
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class ParameterBag implements ParameterBagInterface
{
    use DumpableTrait;
    
    /**
     * The items contained within the bag.
     *
     * @var array
     */
    protected array $items = [];
    
    /**
     * ParameterBag constructor.
     *
     * @param array $items
     */
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
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
    public function all(?string $key = null): array
    {
        if (null === $key) {
            return $this->items;
        }
        
        if (! is_array($value = Arr::get($this->items, $key) ?? [])) {
            throw new Exception(
                sprintf(
                    'Unexpected value for parameter "%s": expecting "array", got "%s".',
                    $key,
                    get_debug_type($value)
                )
            );
        }
        
        return $value;
    }
    
    /**
     * Returns an array containing all the keys in the bag.
     *
     * @return array
     */
    public function keys(): array
    {
        return array_keys($this->items);
    }
    
    /**
     * Replaces the current items in the bag with a new set of items.
     *
     * @param array $items
     */
    public function replace(array $items = []): void
    {
        $this->items = $items;
    }
    
    /**
     * Adds items to the bag.
     *
     * @param array $items
     */
    public function add(array $items = []): void
    {
        foreach ($items as $key => $value) {
            Arr::add($this->items, $key, $value);
        }
    }
    
    /**
     * Returns the value of the specified key, or the default value if the key
     * does not exist.
     *
     * @param string $key
     * @param mixed $default
     *
     * @return mixed
     */
    public function get(string $key, mixed $default = null): mixed
    {
        return Arr::get($this->items, $key, $default);
    }
    
    /**
     * Sets the value of the specified key.
     *
     * @param string $key
     * @param mixed $value
     */
    public function set(string $key, mixed $value): void
    {
        Arr::set($this->items, $key, $value);
    }
    
    /**
     * Returns true if the specified key is defined.
     *
     * @param string $key
     *
     * @return bool
     */
    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }
    
    /**
     * Removes the specified key from the bag.
     *
     * @param string $key
     */
    public function remove(string $key): void
    {
        Arr::forget($this->items, $key);
    }
    
    /**
     * Returns an iterator for items.
     *
     * @return \ArrayIterator<string, mixed>
     */
    public function getIterator(): ArrayIterator
    {
        return new ArrayIterator($this->items);
    }
    
    /**
     * Returns the number of items in the bag.
     *
     * @return int
     */
    public function count(): int
    {
        return count($this->items);
    }
    
    /**
     * Returns the alphabetic characters of the parameter value.
     *
     * @param string $key The key to retrieve the value from.
     * @param string $default The default value to return if the key does not exist.
     *
     * @return string
     */
    public function getAlpha(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alpha:]]/', '', $this->getString($key, $default));
    }
    
    /**
     * Returns the alphabetic characters and digits of the parameter value.
     *
     * @param string $key The key to retrieve the value from.
     * @param string $default The default value to return if the key does not exist.
     *
     * @return string
     */
    public function getAlnum(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:alnum:]]/', '', $this->getString($key, $default));
    }
    
    /**
     * Returns the digits of the parameter value.
     *
     * @param string $key The key to retrieve the value from.
     * @param string $default The default value to return if the key does not exist.
     *
     * @return string
     */
    public function getDigits(string $key, string $default = ''): string
    {
        return preg_replace('/[^[:digit:]]/', '', $this->getString($key, $default));
    }
    
    /**
     * Returns the parameter as string.
     */
    public function getString(string $key, string $default = ''): string
    {
        $value = $this->get($key, $default);
        if (! is_scalar($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException(sprintf('Parameter value "%s" cannot be converted to "string".', $key));
        }
        
        return (string) $value;
    }
    
    /**
     * Returns the parameter value converted to integer.
     */
    public function getInt(string $key, int $default = 0): int
    {
        return $this->filter($key, $default, FILTER_VALIDATE_INT, ['flags' => FILTER_REQUIRE_SCALAR]);
    }
    
    /**
     * Returns the parameter value converted to boolean.
     */
    public function getBoolean(string $key, bool $default = false): bool
    {
        return $this->filter($key, $default, FILTER_VALIDATE_BOOL, ['flags' => FILTER_REQUIRE_SCALAR]);
    }
    
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
    public function getEnum(string $key, string $class, ?BackedEnum $default = null): ?BackedEnum
    {
        $value = $this->get($key);
        
        if (null === $value) {
            return $default;
        }
        
        try {
            return $class::from($value);
        } catch (ValueError|TypeError $e) {
            throw new UnexpectedValueException(sprintf('Parameter "%s" cannot be converted to enum: %s.', $key, $e->getMessage()), $e->getCode(), $e);
        }
    }
    
    /**
     * Filter key.
     *
     * @param int                                     $filter  FILTER_* constant
     * @param int|array{flags?: int, options?: array} $options Flags from FILTER_* constants
     *
     * @see https://php.net/filter-var
     */
    public function filter(string $key, mixed $default = null, int $filter = FILTER_DEFAULT, mixed $options = []): mixed
    {
        $value = $this->get($key, $default);
        
        // Always turn $options into an array - this allows filter_var option shortcuts.
        if (! is_array($options) && $options) {
            $options = ['flags' => $options];
        }
        
        // Add a convenience check for arrays.
        if (is_array($value) && !isset($options['flags'])) {
            $options['flags'] = FILTER_REQUIRE_ARRAY;
        }
        
        if (is_object($value) && !$value instanceof \Stringable) {
            throw new UnexpectedValueException(sprintf('Parameter value "%s" cannot be filtered.', $key));
        }
        
        if ((FILTER_CALLBACK & $filter) && !(($options['options'] ?? null) instanceof Closure)) {
            throw new InvalidArgumentException(sprintf('A Closure must be passed to "%s()" when FILTER_CALLBACK is used, "%s" given.', __METHOD__, get_debug_type($options['options'] ?? null)));
        }
        
        $options['flags'] ??= 0;
        $nullOnFailure = $options['flags'] & FILTER_NULL_ON_FAILURE;
        $options['flags'] |= FILTER_NULL_ON_FAILURE;
        
        $value = filter_var($value, $filter, $options);
        
        if (null !== $value || $nullOnFailure) {
            return $value;
        }
        
        throw new UnexpectedValueException(sprintf('Parameter value "%s" is invalid and flag "FILTER_NULL_ON_FAILURE" was not set.', $key));
    }
    
    /**
     * @inheritDoc
     */
    public function offsetGet(mixed $offset): mixed
    {
        return $this->get($offset);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->set($offset, $value);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetExists(mixed $offset): bool
    {
        return $this->has($offset);
    }
    
    /**
     * @inheritDoc
     */
    public function offsetUnset(mixed $offset): void
    {
        $this->remove($offset);
    }
    
    /**
     * Returns the JSON representation of the items.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return json_encode($this->items);
    }
}
