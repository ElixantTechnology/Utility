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
use ArrayAccess;
use JsonSerializable;
use Illuminate\Support\Collection;

/**
 * Fluent Class
 * Created Apr 06, 2024.
 *
 * The Fluent class is a utility class that allows developers to create and
 * manipulate objects in a more readable and expressive way. It is beneficial
 * when working with objects with many properties, enabling them to be
 * set and manipulated concisely. The Fluent class can be created using the
 * make method, an existing array or object, or the fluent API.
 *
 * It can be manipulated by accessing and setting its properties using dot
 * notation. Overall, the Fluent class is an essential tool for working with
 * objects and is widely used to provide a more expressive and readable way to
 * work with data.
 *
 * @package         Elixant\Utility::Fluent
 * @class           Fluent
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 *
 * @template TKey of array-key
 * @template TValue
 *
 * @implements \ArrayAccess<TKey, TValue>
 */
class Fluent implements ArrayAccess, JsonSerializable
{
    /**
     * The attributes that should be cast to native types.
     *
     * @var array
     */
    protected array $attributes = [];
    
    /**
     * Create a new fluent instance.
     *
     * @param array|null $attributes
     */
    public function __construct(?array $attributes = null)
    {
        if (null === $attributes) {
            $attributes = [];
        }
        foreach ($attributes as $key => $value) {
            $this->attributes[$key] = $value;
        }
    }
    
    /**
     * Get the value of the given key as a new Fluent instance.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return static
     */
    public function scope(string $key, mixed $default = null): static
    {
        return new static(
            (array)$this->get($key, $default)
        );
    }
    
    /**
     * Get an attribute from the fluent instance using "dot" notation.
     *
     * @template TGetDefault
     *
     * @param TKey                                                 $key
     * @param array|string|callable|(\Closure(): TGetDefault)|null $default
     *
     * @return TValue|TGetDefault
     */
    public function get($key, callable|array|string|Closure $default = null)
    {
        return data_get($this->attributes, $key, $default);
    }
    
    /**
     * Get the attributes from the fluent instance.
     *
     * @return array<TKey, TValue>
     */
    public function getAttributes(): array
    {
        return $this->attributes;
    }
    
    /**
     * Convert the fluent instance to a Collection.
     *
     * @param string|null $key
     *
     * @return \Illuminate\Support\Collection
     */
    public function collect(string $key = null): Collection
    {
        return new Collection($this->get($key));
    }
    
    /**
     * Convert the fluent instance to JSON.
     *
     * @param int $options
     *
     * @return string
     */
    public function toJson(int $options = 0): string
    {
        return json_encode($this->jsonSerialize(), $options);
    }
    
    /**
     * Convert the object into something JSON serializable.
     *
     * @return array<TKey, TValue>
     */
    public function jsonSerialize(): array
    {
        return $this->toArray();
    }
    
    /**
     * Convert the fluent instance to an array.
     *
     * @return array<TKey, TValue>
     */
    public function toArray(): array
    {
        return $this->attributes;
    }
    
    /**
     * Get the value for a given offset.
     *
     * @param TKey $offset
     *
     * @return TValue|null
     */
    public function offsetGet($offset): mixed
    {
        return $this->value($offset);
    }
    
    /**
     * Get an attribute from the fluent instance.
     *
     * @param string     $key
     * @param mixed|null $default
     *
     * @return mixed
     */
    public function value(string $key, mixed $default = null): mixed
    {
        if (array_key_exists($key, $this->attributes)) {
            return $this->attributes[$key];
        }
        
        return value($default);
    }
    
    /**
     * Handle dynamic calls to the fluent instance to set attributes.
     *
     * @param TKey              $method
     * @param array{0: ?TValue} $parameters
     *
     * @return $this
     */
    public function __call($method, array $parameters)
    {
        $this->attributes[$method] = count($parameters) > 0 ? reset($parameters)
            : true;
        
        return $this;
    }
    
    /**
     * Dynamically retrieve the value of an attribute.
     *
     * @param TKey $key
     *
     * @return TValue|null
     */
    public function __get($key)
    {
        return $this->value($key);
    }
    
    /**
     * Dynamically set the value of an attribute.
     *
     * @param TKey   $key
     * @param TValue $value
     *
     * @return void
     */
    public function __set($key, $value)
    {
        $this->offsetSet($key, $value);
    }
    
    /**
     * Set the value at the given offset.
     *
     * @param TKey   $offset
     * @param TValue $value
     *
     * @return void
     */
    public function offsetSet($offset, $value): void
    {
        $this->attributes[$offset] = $value;
    }
    
    /**
     * Dynamically check if an attribute is set.
     *
     * @param TKey $key
     *
     * @return bool
     */
    public function __isset($key)
    {
        return $this->offsetExists($key);
    }
    
    /**
     * Determine if the given offset exists.
     *
     * @param TKey $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return isset($this->attributes[$offset]);
    }
    
    /**
     * Dynamically unset an attribute.
     *
     * @param TKey $key
     *
     * @return void
     */
    public function __unset($key)
    {
        $this->offsetUnset($key);
    }
    
    /**
     * Unset the value at the given offset.
     *
     * @param TKey $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        unset($this->attributes[$offset]);
    }
}
