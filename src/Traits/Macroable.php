<?php
namespace Elixant\Components\Utility\Traits;

/**
 * Elixant Platform & Development Framework
 *
 * Copyright (c) 2021 Elixant Technology Ltd.
 * All Rights are Reserved.
 *
 * This package is a proprietary development framework built
 * by Elixant Technology for use in creating software that is
 * intended to be licensed out; when distributed this package
 * should be encrypted and not able to be modified by the license
 * holder, however, in an un-encrypted open-source state you are not
 * authorized to posess it, without authorization in writing from
 * Elixant Technology.
 *
 * @package     elixant/platform
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <corporate@elixan-technology.com>
 * @license     proprietary
 */
use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;

trait Macroable
{
	/**
	 * The registered string macros.
	 *
	 * @var array
	 */
	protected static $macros = [];

	/**
	 * Register a custom macro.
	 *
	 * @param string $name
	 * @param object|callable $macro
	 *
	 * @return void
	 */
	public static function macro($name, $macro)
	{
		static::$macros[$name] = $macro;
	}

	/**
	 * Mix another object into the class.
	 *
	 * @param object $mixin
	 * @param bool   $replace
	 *
	 * @return void
	 *
	 * @throws \ReflectionException
	 */
	public static function mixin($mixin, $replace = true)
	{
		$methods = (new ReflectionClass($mixin))->getMethods(
			ReflectionMethod::IS_PUBLIC | ReflectionMethod::IS_PROTECTED
		);

		foreach ($methods as $method)
		{
			if ($replace || !static::hasMacro($method->name))
			{
				$method->setAccessible(true);
				static::macro($method->name, $method->invoke($mixin));
			}
		}
	}

	/**
	 * Checks if macro is registered.
	 *
	 * @param string $name
	 *
	 * @return bool
	 */
	public static function hasMacro($name)
	{
		return isset(static::$macros[$name]);
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public static function __callStatic($method, $parameters)
	{
		if (!static::hasMacro($method))
		{
			throw new BadMethodCallException(
				sprintf(
					'Method %s::%s does not exist.', static::class, $method
				)
			);
		}

		if (static::$macros[$method] instanceof Closure)
		{
			return call_user_func_array(
				Closure::bind(static::$macros[$method], null, static::class),
				$parameters
			);
		}

		return call_user_func_array(static::$macros[$method], $parameters);
	}

	/**
	 * Dynamically handle calls to the class.
	 *
	 * @param string $method
	 * @param array  $parameters
	 *
	 * @return mixed
	 *
	 * @throws \BadMethodCallException
	 */
	public function __call($method, $parameters)
	{
		if (!static::hasMacro($method))
		{
			throw new BadMethodCallException(
				sprintf(
					'Method %s::%s does not exist.', static::class, $method
				)
			);
		}

		$macro = static::$macros[$method];

		if ($macro instanceof Closure)
		{
			return call_user_func_array(
				$macro->bindTo($this, static::class), $parameters
			);
		}

		return call_user_func_array($macro, $parameters);
	}
}
