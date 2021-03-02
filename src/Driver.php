<?php
namespace Elixant\Components\Utility;

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
use Elixant\Components\Utility\Repository\ItemRepository;
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
	 * @var \Elixant\Components\Components\Container\Container
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

		if (! $this->config instanceof ItemRepository)
		{
			$this->config = new ItemRepository($config);
		}

		return true;
	}
}
