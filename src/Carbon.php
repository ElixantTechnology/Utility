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
namespace Elixant\Utility;

use DateTime;
use Ramsey\Uuid\Uuid;
use Carbon\Carbon as BaseCarbon;
use Symfony\Component\Uid\Ulid;
use Elixant\Utility\Traits\DumpableTrait;
use Elixant\Utility\Traits\ConditionableTrait;
use Carbon\CarbonImmutable as BaseCarbonImmutable;

/**
 * Carbon Class
 * Created Apr 07, 2024.
 *
 * The Carbon class extends the Carbon\Carbon class and adds the
 * ConditionableTrait and DumpableTrait traits to the class. This class is used
 * to create Carbon instances from ordered UUIDs and ULIDs. The class also
 * provides a static method for setting the test now date and time for the
 * Carbon and CarbonImmutable classes. The class is used by the Elixant
 * Platform Framework to create Carbon instances from ordered UUIDs and ULIDs.
 *
 * @package         Elixant\Utility::Carbon
 * @class           Carbon
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class Carbon extends BaseCarbon
{
    use ConditionableTrait,
        DumpableTrait;
    
    /**
     * Set the test now date and time for the Carbon and CarbonImmutable
     * classes.
     *
     * @param mixed|null $testNow
     *
     * @return void
     */
    public static function setTestNow(mixed $testNow = null): void
    {
        BaseCarbon::setTestNow($testNow);
        BaseCarbonImmutable::setTestNow($testNow);
    }
    
    /**
     * Create a new Carbon instance from a UUID or ULID.
     *
     * @param \Ramsey\Uuid\Uuid|\Symfony\Component\Uid\Ulid|string $id
     *
     * @return \DateTime
     */
    public static function createFromId(Uuid|Ulid|string $id): DateTime
    {
        if (is_string($id)) {
            $id = Ulid::isValid($id)
                ? Ulid::fromString($id)
                : Uuid::fromString(
                    $id
                );
        }
        
        return static::createFromInterface($id->getDateTime());
    }
}
