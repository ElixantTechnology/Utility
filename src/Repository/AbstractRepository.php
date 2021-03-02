<?php
namespace Elixant\Components\Utility\Repository;

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
use Elixant\Components\Utility\Traits\Dumpable;

abstract class AbstractRepository
{
	use Dumpable;

	/**
	 * All of the repository items.
	 *
	 * @var array
	 */
	protected $items = [];

	public function __construct(array $items = [])
	{
		$this->items = $items;
	}

	/**
	 * Determine if the given repository item exists.
	 *
	 * @param string $item
	 *
	 * @return bool
	 */
	abstract public function has($item);

	/**
	 * Get the specified repository item.
	 *
	 * @param array|string $key
	 * @param mixed        $default
	 *
	 * @return mixed
	 */
	abstract public function get($key, $default = null);

	/**
	 * Get many repository items.
	 *
	 * @param array $keys
	 *
	 * @return array
	 */
	abstract public function getMany(array $keys);

	/**
	 * Set a given repository items.
	 *
	 * @param array|string $key
	 * @param mixed        $value
	 *
	 * @return void
	 */
	abstract public function set($key, $value = null);

	/**
	 * Prepend a value onto an array repository item.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	abstract public function prepend($key, $value);

	/**
	 * Push a value onto an array repository item.
	 *
	 * @param string $key
	 * @param mixed  $value
	 *
	 * @return void
	 */
	abstract public function push($key, $value);

	/**
	 * Get all of the repositoru items.
	 *
	 * @return array
	 */
	public function all()
	{
		return $this->items;
	}
}
