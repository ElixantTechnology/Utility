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
use Symfony\Component\VarDumper\VarDumper;

trait Dumpable
{
	/**
	 * Dump the Class contents for visual inspection, this should
	 * really only be used for debugging purposes.
	 *
	 * @return mixed
	 */
	public function dump()
	{
		return VarDumper::dump($this);
	}
}
