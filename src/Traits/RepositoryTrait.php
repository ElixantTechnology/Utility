<?php declare(strict_types = 1);
namespace Elixant\Utility\Traits;

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
trait RepositoryTrait
{
    /**
     * All the configuration items.
     *
     * @var array $items
     */
    protected array $items = [];
    
    public function __construct(array $items = [])
    {
        $this->items = $items;
    }
    
    public function has(string $key): bool
    {
        return Arr::has($this->items, $key);
    }
    
    public function get(string|array $key, string|array $default = null)
    {
        if (is_array($key)) {
            return $this->getMany($key);
        }
        
        return Arr::get($this->items, $key, $default);
    }
    
    public function getMany(array $keys): array
    {
        $config = [];
        foreach ($keys as $key => $default) {
            if (is_numeric($key)) {
                [$key, $default] = [$default, null];
            }
            $config[$key] = Arr::get($this->items, $key, $default);
        }
        
        return $config;
    }
    
    public function string(string $key, $default = null): string
    {
        $value = $this->get($key, $default);
        if ( ! is_string($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration value for key [%s] must be a string, %s given.',
                    $key, gettype($value)
                )
            );
        }
        
        return $value;
    }
    
    public function integer(string $key, $default = null): int
    {
        $value = $this->get($key, $default);
        if ( ! is_int($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration value for key [%s] must be an integer, %s given.',
                    $key, gettype($value)
                )
            );
        }
        
        return $value;
    }
    
    public function float(string $key, $default = null): float
    {
        $value = $this->get($key, $default);
        if ( ! is_float($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration value for key [%s] must be a float, %s given.',
                    $key, gettype($value)
                )
            );
        }
        
        return $value;
    }
    
    public function boolean(string $key, $default = null): bool
    {
        $value = $this->get($key, $default);
        if ( ! is_bool($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration value for key [%s] must be a boolean, %s given.',
                    $key, gettype($value)
                )
            );
        }
        
        return $value;
    }
    
    public function array(string $key, $default = null): array
    {
        $value = $this->get($key, $default);
        if ( ! is_array($value)) {
            throw new InvalidArgumentException(
                sprintf(
                    'Configuration value for key [%s] must be an array, %s given.',
                    $key, gettype($value)
                )
            );
        }
        
        return $value;
    }
    
    public function set($key, $value = null): void
    {
        $keys = is_array($key) ? $key : [$key => $value];
        foreach ($keys as $key => $value) {
            Arr::set($this->items, $key, $value);
        }
    }
    
    public function prepend($key, $value): void
    {
        $array = $this->get($key, []);
        array_unshift($array, $value);
        $this->set($key, $array);
    }
    
    public function push($key, $value): void
    {
        $array = $this->get($key, []);
        $array[] = $value;
        $this->set($key, $array);
    }
    
    public function all(): array
    {
        return $this->items;
    }
    
    public function offsetExists($offset): bool
    {
        return $this->has($offset);
    }
    
    public function offsetGet($offset): mixed
    {
        return $this->get($offset);
    }
    
    public function offsetSet($offset, $value): void
    {
        $this->set($offset, $value);
    }
    
    public function offsetUnset($offset): void
    {
        $this->set($offset);
    }
}
