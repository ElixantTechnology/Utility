<?php
/**
 * Elixant Platform Framework Component
 *
 * Elixant Platform
 * Copyright (c) 2023 Elixant Corporation.
 *
 * Permission is hereby granted, free of charge, to any
 * person obtaining a copy of this software and associated
 * documentation files (the "Software"), to deal in the
 * Software without restriction, including without limitation
 * the rights to use, copy, modify, merge, publish, distribute,
 * sublicense, and/or sell copies of the Software, and to permit
 * persons to whom the Software is furnished to do so.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND,
 * EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES
 * OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT.
 *
 * IN NO EVENT SHALL THE AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM,
 * DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
 * OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE
 * USE OR OTHER DEALINGS IN THE SOFTWARE.
 *
 * @copyright    2023 (C) Elixant Corporation.
 * @license      MIT License
 * @author       Alexander Schmautz <a.schmautz@outlook.com>
 */
declare(strict_types = 1);
namespace Elixant\Utility\Traits;

use WeakMap;
use UnitEnum;
use Traversable;
use JsonSerializable;
use InvalidArgumentException;
use Elixant\Utility\Interfaces\Jsonable;
use Elixant\Utility\Interfaces\Arrayable;
use Elixant\Utility\Interfaces\Enumerable;

/**
 * EnumerableTrait Trait
 * Created May 03, 2024.
 *
 * @package         Elixant\Utility\Traits::EnumerableTrait
 * @trait           EnumerableTrait
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
trait EnumerableTrait
{
    /**
     * Get the arrayable items from the given input.
     *
     * @param mixed $items The items to convert to an array.
     * @return array The array representation of the input items.
     */
    protected function getArrayableItems($items)
    {
        // If the input items are already an array, return them directly
        if (is_array($items)) {
            return $items;
        }
        
        // Convert different types of items to arrays based on their instance type
        return match (true) {
            $items instanceof WeakMap => throw new InvalidArgumentException('Collections can not be created using instances of WeakMap.'),
            $items instanceof Enumerable => $items->all(),
            $items instanceof Arrayable => $items->toArray(),
            $items instanceof Traversable => iterator_to_array($items),
            $items instanceof Jsonable => json_decode($items->toJson(), true),
            $items instanceof JsonSerializable => (array) $items->jsonSerialize(),
            $items instanceof UnitEnum => [$items],
            default => (array) $items,
        };
    }
}
