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
 * @package      elixant/utility
 * @copyright    2023 (C) Elixant Corporation.
 * @license      MIT License
 * @author       Alexander Schmautz <a.schmautz@outlook.com>
 */
declare(strict_types = 1);
namespace Elixant\Utility;

use Stringable;

/**
 * HtmlString Class
 * Created Apr 07, 2024.
 *
 * The HtmlString class is a utility class that provides a simple way to\
 * create and manipulate HTML strings. The class provides a number of\
 * convenience methods for creating and manipulating HTML strings.
 *
 * @package         Elixant\Utility::HtmlString
 * @class           HtmlString
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class HtmlString implements Stringable
{
    /**
     * The HTML string.
     *
     * @var string
     */
    protected string $html;
    
    /**
     * Create a new HTML string instance.
     *
     * @param string $html
     *
     * @return void
     */
    public function __construct(string $html = '')
    {
        $this->html = $html;
    }
    
    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function toHtml(): string
    {
        return $this->html;
    }
    
    /**
     * Determine if the given HTML string is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->html === '';
    }
    
    /**
     * Determine if the given HTML string is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
    
    /**
     * Get the HTML string.
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toHtml();
    }
}
