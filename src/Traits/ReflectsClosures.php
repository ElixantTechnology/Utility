<?php
namespace Elixant\Components\Utility\Traits;

/**
 * OneSaaS Platform
 *
 * Copyright (c) 2021 Elixant Technology Ltd.
 * All Rights are Reserved.
 *
 * @package     elixant/onesaas
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <corporate@elixan-technology.com>
 * @license     proprietary
 */
use Closure;
use ReflectionException;
use ReflectionFunction;
use RuntimeException;
use Elixant\Components\Utility\Reflector;

/**
 * RelectsClosures Class
 *
 * @package     elixant/platform
 * @subpackage  ReflectsClosures
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <corporate@elixant-technology.com>
 */
trait ReflectsClosures
{
    /**
     * Get the class names / types of the parameters of the given Closure.
     *
     * @param  Closure  $closure
     *
     * @return array
     *
     * @throws ReflectionException
     */
    protected function closureParameterTypes(Closure $closure)
    {
        $reflection = new ReflectionFunction($closure);

        return collect($reflection->getParameters())->mapWithKeys(function ($parameter) {
            if ($parameter->isVariadic()) {
                return [$parameter->getName() => null];
            }

            return [$parameter->getName() => Reflector::getParameterClassName($parameter)];
        })->all();
    }

    /**
     * Get the class name of the first parameter of the given Closure.
     *
     * @param  Closure  $closure
     *
     * @return string
     *
     * @throws ReflectionException|RuntimeException
     */
    protected function firstClosureParameterType(Closure $closure)
    {
        $types = array_values($this->closureParameterTypes($closure));

        if (! $types) {
            throw new RuntimeException('The given Closure has no parameters.');
        }

        if ($types[0] === null) {
            throw new RuntimeException('The first parameter of the given Closure is missing a type hint.');
        }

        return $types[0];
    }
}