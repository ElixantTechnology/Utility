<?php
namespace Elixant\Components\Utility\Interfaces;

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

/**
 * Determine if the object can produce its JSON representation.
 *
 * @package     elixant/platform
 * @subpackage  Jsonable
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <corporate@elixant-technology.com>
 */
interface Jsonable
{
    /**
     * Convert the object to its JSON representation.
     *
     * @param  int  $options
     * @return string
     */
    public function toJson($options = 0);
}