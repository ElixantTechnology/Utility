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
 * Get the evaluated contents of the object.
 *
 * @package     elixant/platform
 * @subpackage  Renderable
 * @copyright   2021 (c) Elixant Technology Ltd.
 * @author      Alexander M. Schmautz <corporate@elixant-technology.com>
 */
interface Renderable
{
    /**
     * Get the evaluated contents of the object.
     *
     * @return string
     */
    public function render();
}