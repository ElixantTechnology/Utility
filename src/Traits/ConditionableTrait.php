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
use Closure;
use Elixant\Utility\Proxies\HigherOrderWhenProxy;

trait ConditionableTrait
{
    /**
     * Apply the callback if the given "value" is (or resolves to) truthy.
     *
     * @template TWhenParameter
     * @template TWhenReturnType
     *
     * @param \Closure|bool|null                                      $value
     * @param (callable($this, TWhenParameter): TWhenReturnType)|null $callback
     * @param (callable($this, TWhenParameter): TWhenReturnType)|null $default
     *
     * @return \Elixant\Utility\Proxies\HigherOrderWhenProxy|\Elixant\Utility\Traits\ConditionableTrait
     */
    public function when(
        Closure|bool $value = null, callable $callback = null,
        callable $default = null
    ): HigherOrderWhenProxy|static {
        $value = $value instanceof Closure ? $value($this) : $value;
        if (func_num_args() === 0) {
            return new HigherOrderWhenProxy($this);
        }
        if (func_num_args() === 1) {
            return (new HigherOrderWhenProxy($this))->condition($value);
        }
        if ($value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }
        
        return $this;
    }
    
    /**
     * Apply the callback if the given "value" is (or resolves to) falsy.
     *
     * @template TUnlessParameter
     * @template TUnlessReturnType
     *
     * @param (\Closure($this): TUnlessParameter)|null                    $value
     * @param (callable($this, TUnlessParameter): TUnlessReturnType)|null $callback
     * @param (callable($this, TUnlessParameter): TUnlessReturnType)|null $default
     *
     * @return \Elixant\Utility\Proxies\HigherOrderWhenProxy|static
     */
    public function unless(
        Closure $value = null, callable $callback = null,
        callable $default = null
    ): HigherOrderWhenProxy|static {
        $value = $value instanceof Closure ? $value($this) : $value;
        if (func_num_args() === 0) {
            return (new HigherOrderWhenProxy($this))->negateConditionOnCapture(
            );
        }
        if (func_num_args() === 1) {
            return (new HigherOrderWhenProxy($this))->condition(! $value);
        }
        if ( ! $value) {
            return $callback($this, $value) ?? $this;
        } elseif ($default) {
            return $default($this, $value) ?? $this;
        }
        
        return $this;
    }
}
