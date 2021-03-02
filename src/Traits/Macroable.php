<?php
namespace Elixant\Components\Utility\Traits;

/**
 * Copyright (c) 2021 Elixant Technology Ltd.
 *
 * PHP Version 7
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is furnished
 * to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package         elixant-technology/utility
 * @copyright       2021 (c) Elixant Technology Ltd.
 * @author          Alexander M. Schmautz <president@elixant-technology.com>
 * @license         http://www.opensource.org/licenses/mit-license.html  MIT License
 * @version         Release: @package_version@
 */
use BadMethodCallException;
use Closure;
use ReflectionClass;
use ReflectionMethod;

/**
 * Class: Macroable
 *
 * @package     elixant-platform/utility
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <president@elixant-technology.com>
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 */
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
