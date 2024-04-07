<?php declare(strict_types = 1);
namespace Elixant\Utility\Proxies;

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
class HigherOrderWhenProxy
{
    /**
     * The target being conditionally operated on.
     *
     * @var mixed
     */
    protected mixed $target;
    /**
     * The condition for proxying.
     *
     * @var bool
     */
    protected bool $condition;
    /**
     * Indicates whether the proxy has a condition.
     *
     * @var bool
     */
    protected bool $hasCondition = false;
    /**
     * Determine whether the condition should be negated.
     *
     * @var bool
     */
    protected bool $negateConditionOnCapture;
    
    /**
     * Create a new proxy instance.
     *
     * @param mixed $target
     *
     * @return void
     */
    public function __construct(mixed $target)
    {
        $this->target = $target;
    }
    
    /**
     * Set the condition on the proxy.
     *
     * @param bool $condition
     *
     * @return $this
     */
    public function condition(bool $condition): static
    {
        [$this->condition, $this->hasCondition] = [$condition, true];
        
        return $this;
    }
    
    /**
     * Indicate that the condition should be negated.
     *
     * @return $this
     */
    public function negateConditionOnCapture(): static
    {
        $this->negateConditionOnCapture = true;
        
        return $this;
    }
    
    /**
     * Proxy accessing an attribute onto the target.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        if ( ! $this->hasCondition) {
            $condition = $this->target->{$key};
            
            return $this->condition(
                $this->negateConditionOnCapture ? ! $condition : $condition
            );
        }
        
        return $this->condition
            ? $this->target->{$key}
            : $this->target;
    }
    
    /**
     * Proxy a method call on the target.
     *
     * @param string $method
     * @param array  $parameters
     *
     * @return mixed
     */
    public function __call(string $method, array $parameters)
    {
        if ( ! $this->hasCondition) {
            $condition = $this->target->{$method}(...$parameters);
            
            return $this->condition(
                $this->negateConditionOnCapture ? ! $condition : $condition
            );
        }
        
        return $this->condition
            ? $this->target->{$method}(...$parameters)
            : $this->target;
    }
}
