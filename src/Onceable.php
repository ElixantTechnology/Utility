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
use Laravel\SerializableClosure\Support\ReflectionClosure;

/**
 * Onceable Class
 * Created Apr 07, 2024.
 *
 * The Onceable class is a utility class that provides a simple way to
 * create and manage singletons. The class provides a number of
 * convenience methods for creating and managing singletons.
 *
 * @package         Elixant\Utility::Onceable
 * @class           Onceable
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 **/
class Onceable
{
    /**
     * Create a new onceable instance.
     *
     * @param string $hash
     * @param object|null $object
     * @param callable $callable
     *
     * @return void
     */
    public function __construct(
        public string $hash,
        public object|null $object,
        public mixed $callable
    ) {
        //
    }
    
    /**
     * Tries to create a new onceable instance from the given trace.
     *
     * @param array<int, array<string, mixed>> $trace
     * @param callable                         $callable
     *
     * @return static|null
     */
    public static function tryFromTrace(array $trace, callable $callable
    ): null|static {
        if ( ! is_null($hash = static::hashFromTrace($trace, $callable))) {
            $object = static::objectFromTrace($trace);
            
            return new static($hash, $object, $callable);
        }
        
        return null;
    }
    
    /**
     * Computes the object of the onceable from the given trace, if any.
     *
     * @param array<int, array<string, mixed>> $trace
     *
     * @return object|null
     */
    protected static function objectFromTrace(array $trace): ?object
    {
        return $trace[1]['object'] ?? null;
    }
    
    /**
     * Computes the hash of the onceable from the given trace.
     *
     * @param array<int, array<string, mixed>> $trace
     * @param callable                         $callable
     *
     * @return string|null
     */
    protected static function hashFromTrace(array $trace, callable $callable
    ): ?string {
        if (str_contains($trace[0]['file'] ?? '', 'eval()\'d code')) {
            return null;
        }
        $uses = array_map(
            fn(mixed $argument) => is_object($argument) ? spl_object_hash(
                $argument
            ) : $argument,
            $callable instanceof Closure ? (new ReflectionClosure(
                $callable
            ))->getClosureUsedVariables() : [],
        );
        
        return md5(
            sprintf(
                '%s@%s%s:%s (%s)',
                $trace[0]['file'],
                isset($trace[1]['class']) ? ($trace[1]['class'] . '@') : '',
                $trace[1]['function'],
                $trace[0]['line'],
                serialize($uses),
            )
        );
    }
}
