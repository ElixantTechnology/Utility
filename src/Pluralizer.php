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
use Doctrine\Common\Inflector\Inflector;

/**
 * Class: Pluralizer
 *
 * @package     elixant-platform/utility
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <president@elixant-technology.com>
 * @license     http://www.opensource.org/licenses/mit-license.html  MIT License
 */
class Pluralizer
{
	/**
	 * Uncountable word forms.
	 *
	 * @var array
	 */
	public static $uncountable
		= [
			'audio',
			'bison',
			'cattle',
			'chassis',
			'compensation',
			'coreopsis',
			'data',
			'deer',
			'education',
			'emoji',
			'equipment',
			'evidence',
			'feedback',
			'firmware',
			'fish',
			'furniture',
			'gold',
			'hardware',
			'information',
			'jedi',
			'kin',
			'knowledge',
			'love',
			'metadata',
			'money',
			'moose',
			'news',
			'nutrition',
			'offspring',
			'plankton',
			'pokemon',
			'police',
			'rain',
			'recommended',
			'related',
			'rice',
			'series',
			'sheep',
			'software',
			'species',
			'swine',
			'traffic',
			'wheat',
		];

	/**
	 * Get the plural form of an English word.
	 *
	 * @param string $value
	 * @param int    $count
	 *
	 * @return string
	 */
	public static function plural($value, $count = 2)
	{
		if ((int)abs($count) === 1 || static::uncountable($value))
		{
			return $value;
		}

		$plural = Inflector::pluralize($value);

		return static::matchCase($plural, $value);
	}

	/**
	 * Get the singular form of an English word.
	 *
	 * @param string $value
	 *
	 * @return string
	 */
	public static function singular($value)
	{
		$singular = Inflector::singularize($value);

		return static::matchCase($singular, $value);
	}

	/**
	 * Determine if the given value is uncountable.
	 *
	 * @param string $value
	 *
	 * @return bool
	 */
	protected static function uncountable($value)
	{
		return in_array(strtolower($value), static::$uncountable);
	}

	/**
	 * Attempt to match the case on two strings.
	 *
	 * @param string $value
	 * @param string $comparison
	 *
	 * @return string
	 */
	protected static function matchCase($value, $comparison)
	{
		$functions = ['mb_strtolower', 'mb_strtoupper', 'ucfirst', 'ucwords'];

		foreach ($functions as $function)
		{
			if (call_user_func($function, $comparison) === $comparison)
			{
				return call_user_func($function, $value);
			}
		}

		return $value;
	}
}
