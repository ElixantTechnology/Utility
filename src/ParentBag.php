<?php
namespace Elixant\Components\Utility;

use Elixant\Components\Utility\Exceptions\BagParameterCircularReferenceException;
use RuntimeException;

/**
 * OneSaaS Platform
 *
 * Copyright (c) 2021 Elixant Technology Ltd.
 * All Rights are Reserved.
 *
 * @package     elixant/framework
 * @copyright   2021 (c) Elixant Technology Ltd. All Rights Reserved.
 * @author      Alexander M. Schmautz <corporate@elixant-technology.com>
 * @license     Elixant EULA https://elixant-technology.com/legal/eula
 * @version     1.0.0-dev
 */

/**
 * A Bag (or ParameterBag) is a datatype used to help store data, similar
 * to a collection. A bag specifically can access values via. key/value
 * accessors and includes other operations that are useful.
 *
 * This class can act independently or as a Parent to any bag definition
 * classes used throughout your project(s).
 *
 * @package     elixant/framework
 * @copyright   2021 (c) Elixant Technology Ltd. All Rights Reserved.
 * @author      Alexander M. Schmautz <corporate@elixant-technology.com>\
 * @since       1.0.0-dev
 */
class ParentBag
{
    protected $parameters = [];
    protected $resolved   = false;

    public function __construct(array $parameters = [])
    {
        //
    }

    public function add(array $parameters)
    {
        foreach ($parameters as $key => $value)
        {
            $this->set($key, $value);
        }
    }

    public function get(string $name)
    {
        if (!$this->has($name))
        {
            return null;
        }

        return Arr::get($this->parameters, $name);
    }

    public function set(string $name, $value)
    {
        Arr::set($this->parameters, $name, $value);
    }

    public function has(string $name)
    {
        return Arr::has($this->parameters, $name);
    }

    public function all(string $base = null)
    {
        if (null === $base)
        {
            return $this->parameters;
        }

        if (Arr::get($this->parameters, $base))
        {
            return is_array($this->get($base)) ? $this->get($base) : null;
        }

        return null;
    }

    public function remove(string $name)
    {
        Arr::forget($this->parameters, $name);
    }

    public function resolve()
    {
        if ($this->resolved)
        {
            return;
        }

        $parameters = [];

        foreach ($this->parameters as $key => $value)
        {
            $value            = $this->resolveValue($value);
            $parameters[$key] = $this->unescapeValue($value);
        }

        $this->add($parameters);
        $this->resolved = true;
    }

    public function resolveValue($value, array $resolving = [])
    {
        if (true === is_array($value)) {
            $args = [];

            foreach ($value as $k => $v) {
                $args[is_string($value) ? $this->resolveValue($k, $resolving) : $k] = $this->resolveValue($v, $resolving);
            }

            return $args;
        }

        if (false === is_string($value) || 2 > strlen($value)) {
            return $value;
        }

        return $this->resolveString($value, $resolving);
    }

    /**
     * Prepend a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function prepend($key, $value)
    {
        $array = $this->get($key);

        array_unshift($array, $value);

        $this->set($key, $array);
    }

    /**
     * Push a value onto an array configuration value.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function push($key, $value)
    {
        $array = $this->get($key);

        $array[] = $value;

        $this->set($key, $array);
    }

    /**
     * Determine if the given configuration option exists.
     *
     * @param  string  $key
     * @return bool
     */
    public function offsetExists($key)
    {
        return $this->has($key);
    }

    /**
     * Get a configuration option.
     *
     * @param  string  $key
     * @return mixed
     */
    public function offsetGet($key)
    {
        return $this->get($key);
    }

    /**
     * Set a configuration option.
     *
     * @param  string  $key
     * @param  mixed  $value
     * @return void
     */
    public function offsetSet($key, $value)
    {
        $this->set($key, $value);
    }

    /**
     * Unset a configuration option.
     *
     * @param  string  $key
     * @return void
     */
    public function offsetUnset($key)
    {
        $this->set($key, null);
    }

    public function resolveString(string $value, array $resolving = [])
    {
        if (preg_match('/^%([^%\s]+)%$/', $value, $match)) {
            $key = $match[1];

            if (isset($resolving[$key])) {
                throw new BagParameterCircularReferenceException(array_keys($resolving));
            }

            $resolving[$key] = true;

            return $this->resolved ? $this->get($key) : $this->resolveValue($this->get($key), $resolving);
        }

        return preg_replace_callback('/%%|%([^%\s]+)%/', function ($match) use ($resolving, $value) {
            // skip %%
            if (!isset($match[1])) {
                return '%%';
            }

            $key = $match[1];
            if (isset($resolving[$key])) {
                throw new BagParameterCircularReferenceException(array_keys($resolving));
            }

            $resolved = $this->get($key);

            if (! is_string($resolved) && !is_numeric($resolved)) {
                throw new RuntimeException(sprintf('A string value must be composed of strings and/or numbers, but found parameter "%s" of type "%s" inside string value "%s".', $key, get_debug_type($resolved), $value));
            }

            $resolved = (string) $resolved;
            $resolving[$key] = true;

            return $this->isResolved() ? $resolved : $this->resolveString($resolved, $resolving);
        }, $value);
    }

    public function isResolved()
    {
        return $this->resolved;
    }

    public function escapeValue($value)
    {
        if (is_string($value)) {
            return str_replace('%', '%%', $value);
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->escapeValue($v);
            }

            return $result;
        }

        return $value;
    }

    public function unescapeValue($value)
    {
        if (is_string($value)) {
            return str_replace('%%', '%', $value);
        }

        if (is_array($value)) {
            $result = [];
            foreach ($value as $k => $v) {
                $result[$k] = $this->unescapeValue($v);
            }

            return $result;
        }

        return $value;
    }
}