<?php
namespace Elixant\Components\Utility\Collections;

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
use Elixant\Components\Utility\Interfaces\Enumerable;

/**
 * ${CARET}
 *
 * @author      Alexander M. Schmautz <corporate@elixant-technology.com>\
 * @copyright   2021 (c) Elixant Technology Ltd. All Rights Reserved.
 * @since       1.0.0-dev
 * @package     elixant/framework
 *
 * @mixin Enumerable
 */
class HigherOrderWhenProxy
{
	/**
	 * The collection being operated on.
	 *
	 * @var Enumerable
	 */
	protected $collection;
	
	/**
	 * The condition for proxying.
	 *
	 * @var bool
	 */
	protected $condition;
	
	/**
	 * Create a new proxy instance.
	 *
	 * @param  Enumerable  $collection
	 * @param  bool  $condition
	 *
	 * @return void
	 */
	public function __construct(Enumerable $collection, $condition)
	{
		$this->condition = $condition;
		$this->collection = $collection;
	}
	
	/**
	 * Proxy accessing an attribute onto the collection.
	 *
	 * @param  string  $key
	 * @return mixed
	 */
	public function __get($key)
	{
		return $this->condition
			? $this->collection->{$key}
			: $this->collection;
	}
	
	/**
	 * Proxy a method call onto the collection.
	 *
	 * @param  string  $method
	 * @param  array  $parameters
	 * @return mixed
	 */
	public function __call($method, $parameters)
	{
		return $this->condition
			? $this->collection->{$method}(...$parameters)
			: $this->collection;
	}
}