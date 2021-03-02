<?php
namespace Elixant\Components\Utility;

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
use Elixant\Components\Container\Container;
use RuntimeException;

abstract class Driver
{
	/**
	 * @var null
	 */
	public $name = null;
	/**
	 * @var null
	 */
	public $description = null;
	/**
	 * @var null
	 */
	public $author = null;
	/**
	 * @var null
	 */
	public $version = null;
	/**
	 * @var null
	 */
	public $copyright = null;

	/**
	 * @var array
	 */
	protected $config_items = [];
	/**
	 * @var array
	 */
	protected $dependencies = [];

	/**
	 * @var
	 */
	protected $config;
	/**
	 * @var Container
	 */
	protected $container;

	/**
	 * Driver constructor.
	 *
	 * @param Container|null $container
	 * @param array          $config
	 */
	public function __construct(Container $container = null, array $config = [])
	{
		if ($this->checkDriverIntegrity($config))
		{
			$this->container = $container ?: get_container();
		}
	}

	/**
	 * @param array $config
	 *
	 * @return bool
	 */
	private function checkDriverIntegrity(array $config = [])
	{
		$valid = true;

		if (empty($this->name) && empty($this->description)
			&& empty($this->author)
			&& empty($this->version)
			&& empty($this->copyright)
		)
		{
			$valid = false;
		} elseif (!empty($this->config_items))
		{
			$valid = $this->validateConfig($config);
		} elseif (!empty($this->dependencies))
		{
			$valid = $this->checkDependencies();
		}

		if (false === $valid)
		{
			$driver_name = $this->name ?? __CLASS__;

			throw new RuntimeException(
				"Integrity check failed for driver [$driver_name]."
			);
		}

		return $valid;
	}

	public function validateConfig(array $config = [])
	{
		if (! empty($this->config_items))
		{
			// @todo: filter items.
		}

		if (! $this->config instanceof ParentBag)
		{
			$this->config = new ParentBag($config);
		}

		return true;
	}
}
