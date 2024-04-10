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

use ArrayAccess;
use ArrayObject;

/**
 * Optional Class
 * Created Apr 07, 2024.
 *
 * The Optional class is a utility class that provides a simple way to
 * create and manage optional values. The class provides a number of
 * convenience methods for creating and managing optional values.
 *
 * @package         Elixant\Utility::Optional
 * @class           Optional
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class Optional implements ArrayAccess
{
    /**
     * The underlying object.
     *
     * @var mixed
     */
    protected mixed $value;
    
    /**
     * Create a new optional instance.
     *
     * @param  mixed  $value
     * @return void
     */
    public function __construct(mixed $value)
    {
        $this->value = $value;
    }
    
    /**
     * Dynamically access a property on the underlying object.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if (is_object($this->value)) {
            return $this->value->{$key};
        }
        
        return null;
    }
    
    /**
     * Dynamically check a property exists on the underlying object.
     *
     * @param  mixed  $name
     * @return bool
     */
    public function __isset(mixed $name)
    {
        if (is_object($this->value)) {
            return isset($this->value->{$name});
        }
        
        if (is_array($this->value) || $this->value instanceof ArrayObject) {
            return isset($this->value[$name]);
        }
        
        return false;
    }
    
    /**
     * Determine if an item exists at an offset.
     *
     * @param $offset
     *
     * @return bool
     */
    public function offsetExists($offset): bool
    {
        return Arr::accessible($this->value) && Arr::exists($this->value, $offset);
    }
    
    /**
     * Get an item at a given offset.
     *
     * @param  mixed  $offset
     * @return mixed
     */
    public function offsetGet(mixed $offset): mixed
    {
        return Arr::get($this->value, $offset);
    }
    
    /**
     * Set the item at a given offset.
     *
     * @param       $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet($offset, mixed $value): void
    {
        if (Arr::accessible($this->value)) {
            $this->value[$offset] = $value;
        }
    }
    
    /**
     * Unset the item at a given offset.
     *
     * @param $offset
     *
     * @return void
     */
    public function offsetUnset($offset): void
    {
        if (Arr::accessible($this->value)) {
            unset($this->value[$offset]);
        }
    }
    
    /**
     * Dynamically pass a method to the underlying object.
     *
     * @param string $method
     * @param  array $parameters
     *
     * @return mixed
     */
    public function __call(string $method, ...$parameters)
    {
        if (is_object($this->value)) {
            return $this->value->{$method}(...$parameters);
        }
        
        return null;
    }
}
