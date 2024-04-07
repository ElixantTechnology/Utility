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
 * @package      elixant/service-container
 * @copyright    2023 (C) Elixant Corporation.
 * @license      MIT License
 * @author       Alexander Schmautz <a.schmautz@outlook.com>
 */
declare(strict_types = 1);
namespace Elixant\Utility;

use WeakMap;

/**
 * Once Class
 * Created Apr 07, 2024.
 *
 * The Once class is a utility class that provides a simple way to
 * create and manage singletons. The class provides a number of
 * convenience methods for creating and managing singletons.
 *
 * @package         Elixant\Utility::Once
 * @class           Once
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class Once
{
    /**
     * The current globally used instance.
     *
     * @var static|null
     */
    protected static ?self $instance = null;
    /**
     * Indicates if the once instance is enabled.
     *
     * @var bool
     */
    protected static bool $enabled = true;
    
    /**
     * Create a new once instance.
     *
     * @param \WeakMap<object, array<string, mixed>> $values
     *
     * @return void
     */
    protected function __construct(protected WeakMap $values)
    {
        //
    }
    
    /**
     * Create a new once instance.
     *
     * @return static
     */
    public static function instance(): static
    {
        return static::$instance ??= new static(new WeakMap);
    }
    
    /**
     * Re-enable the once instance if it was disabled.
     *
     * @return void
     */
    public static function enable(): void
    {
        static::$enabled = true;
    }
    
    /**
     * Disable the once instance.
     *
     * @return void
     */
    public static function disable(): void
    {
        static::$enabled = false;
    }
    
    /**
     * Flush the once instance.
     *
     * @return void
     */
    public static function flush(): void
    {
        static::$instance = null;
    }
    
    /**
     * Get the value of the given onceable.
     *
     * @param Onceable $onceable
     *
     * @return mixed
     */
    public function value(Onceable $onceable): mixed
    {
        if ( ! static::$enabled) {
            return call_user_func($onceable->callable);
        }
        $object = $onceable->object ? : $this;
        $hash   = $onceable->hash;
        if (isset($this->values[$object][$hash])) {
            return $this->values[$object][$hash];
        }
        if ( ! isset($this->values[$object])) {
            $this->values[$object] = [];
        }
        
        return $this->values[$object][$hash] = call_user_func(
            $onceable->callable
        );
    }
}
