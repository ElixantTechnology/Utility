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
declare(strict_types=1);
namespace Elixant\Utility;

use Closure;
use Countable;
use ArrayAccess;
use JsonSerializable;
use Stringable as BaseStringable;
use Illuminate\Support\Collection;
use Elixant\Utility\Traits\DumpableTrait;
use Elixant\Utility\Traits\TappableTrait;
use Elixant\Utility\Traits\ConditionableTrait;

/**
 * Stringable Class
 * Created Apr 07, 2024.
 *
 * The Stringable class is a utility class that provides a simple way to
 * create and manage string values. The class provides a number of
 * convenience methods for creating and managing string values.
 *
 * @package         Elixant\Utility::Stringable
 * @class           Stringable
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class Stringable implements JsonSerializable, ArrayAccess, BaseStringable
{
    use ConditionableTrait,
        DumpableTrait,
        TappableTrait;
    
    /**
     * The underlying string value.
     *
     * @var string|null
     */
    protected ?string $value;
    
    /**
     * Create a new instance of the class.
     *
     * @param string $value
     *
     * @return void
     */
    public function __construct(string $value = '')
    {
        $this->value = $value;
    }
    
    /**
     * Return the remainder of a string after the first occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function after(string $search): static
    {
        return new static(Str::after($this->value, $search));
    }
    
    /**
     * Return the remainder of a string after the last occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function afterLast(string $search): static
    {
        return new static(Str::afterLast($this->value, $search));
    }
    
    /**
     * Append the given values to the string.
     *
     * @param  array|string  ...$values
     * @return static
     */
    public function append(...$values): static
    {
        return new static($this->value.implode('', $values));
    }
    
    /**
     * Append a new line to the string.
     *
     * @param int $count
     *
     * @return $this
     */
    public function newLine(int $count = 1): static
    {
        return $this->append(str_repeat(PHP_EOL, $count));
    }
    
    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $language
     *
     * @return static
     */
    public function ascii(string $language = 'en'): static
    {
        return new static(Str::ascii($this->value, $language));
    }
    
    /**
     * Get the trailing name component of the path.
     *
     * @param string $suffix
     *
     * @return static
     */
    public function basename(string $suffix = ''): static
    {
        return new static(basename($this->value, $suffix));
    }
    
    /**
     * Get the character at the specified index.
     *
     * @param int $index
     *
     * @return string|false
     */
    public function charAt(int $index): false|string
    {
        return Str::charAt($this->value, $index);
    }
    
    /**
     * Get the basename of the class path.
     *
     * @return static
     */
    public function classBasename(): static
    {
        return new static(class_basename($this->value));
    }
    
    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function before(string $search): static
    {
        return new static(Str::before($this->value, $search));
    }
    
    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string $search
     *
     * @return static
     */
    public function beforeLast(string $search): static
    {
        return new static(Str::beforeLast($this->value, $search));
    }
    
    /**
     * Get the portion of a string between two given values.
     *
     * @param string $from
     * @param string $to
     *
     * @return static
     */
    public function between(string $from, string $to): static
    {
        return new static(Str::between($this->value, $from, $to));
    }
    
    /**
     * Get the smallest possible portion of a string between two given values.
     *
     * @param string $from
     * @param string $to
     *
     * @return static
     */
    public function betweenFirst(string $from, string $to): static
    {
        return new static(Str::betweenFirst($this->value, $from, $to));
    }
    
    /**
     * Convert a value to camel case.
     *
     * @return static
     */
    public function camel(): static
    {
        return new static(Str::camel($this->value));
    }
    
    /**
     * Determine if a given string contains a given substring.
     *
     * @param string|iterable<string> $needles
     * @param bool                    $ignoreCase
     *
     * @return bool
     */
    public function contains(
        array|string $needles, bool $ignoreCase = false): bool
    {
        return Str::contains($this->value, $needles, $ignoreCase);
    }
    
    /**
     * Determine if a given string contains all array values.
     *
     * @param iterable<string> $needles
     * @param bool             $ignoreCase
     *
     * @return bool
     */
    public function containsAll(array $needles, bool $ignoreCase = false): bool
    {
        return Str::containsAll($this->value, $needles, $ignoreCase);
    }
    
    /**
     * Convert the case of a string.
     *
     * @param  int  $mode
     * @param  string|null  $encoding
     * @return static
     */
    public function convertCase(int $mode = MB_CASE_FOLD, ?string $encoding = 'UTF-8'): static
    {
        return new static(Str::convertCase($this->value, $mode, $encoding));
    }
    
    /**
     * Get the parent directory's path.
     *
     * @param int $levels
     *
     * @return static
     */
    public function dirname(int $levels = 1): static
    {
        return new static(dirname($this->value, $levels));
    }
    
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string|iterable<string> $needles
     *
     * @return bool
     */
    public function endsWith(array|string $needles): bool
    {
        return Str::endsWith($this->value, $needles);
    }
    
    /**
     * Determine if the string is an exact match with the given value.
     *
     * @param  \Elixant\Utility\Stringable|string  $value
     * @return bool
     */
    public function exactly(Stringable|string $value): bool
    {
        if ($value instanceof Stringable) {
            $value = $value->toString();
        }
        
        return $this->value === $value;
    }
    
    /**
     * Extracts an excerpt from text that matches the first instance of a phrase.
     *
     * @param string $phrase
     * @param array  $options
     *
     * @return string|null
     */
    public function excerpt(string $phrase = '', array $options = []): ?string
    {
        return Str::excerpt($this->value, $phrase, $options);
    }
    
    /**
     * Explode the string into an array.
     *
     * @param string $delimiter
     * @param int    $limit
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function explode(string $delimiter, int $limit = PHP_INT_MAX): Collection
    {
        return collect(explode($delimiter, $this->value, $limit));
    }
    
    /**
     * Split a string using a regular expression or by length.
     *
     * @param int|string $pattern
     * @param int        $limit
     * @param int        $flags
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function split(int|string $pattern, int $limit = -1, int $flags = 0): Collection
    {
        if (filter_var($pattern, FILTER_VALIDATE_INT) !== false) {
            return collect(mb_str_split($this->value, $pattern));
        }
        
        $segments = preg_split($pattern, $this->value, $limit, $flags);
        
        return ! empty($segments) ? collect($segments) : collect();
    }
    
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param string $cap
     *
     * @return static
     */
    public function finish(string $cap): static
    {
        return new static(Str::finish($this->value, $cap));
    }
    
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|iterable<string> $pattern
     *
     * @return bool
     */
    public function is(array|string $pattern): bool
    {
        return Str::is($pattern, $this->value);
    }
    
    /**
     * Determine if a given string is 7-bit ASCII.
     *
     * @return bool
     */
    public function isAscii(): bool
    {
        return Str::isAscii($this->value);
    }
    
    /**
     * Determine if a given string is valid JSON.
     *
     * @return bool
     */
    public function isJson(): bool
    {
        return Str::isJson($this->value);
    }
    
    /**
     * Determine if a given value is a valid URL.
     *
     * @return bool
     */
    public function isUrl(): bool
    {
        return Str::isUrl($this->value);
    }
    
    /**
     * Determine if a given string is a valid UUID.
     *
     * @return bool
     */
    public function isUuid(): bool
    {
        return Str::isUuid($this->value);
    }
    
    /**
     * Determine if a given string is a valid ULID.
     *
     * @return bool
     */
    public function isUlid(): bool
    {
        return Str::isUlid($this->value);
    }
    
    /**
     * Determine if the given string is empty.
     *
     * @return bool
     */
    public function isEmpty(): bool
    {
        return $this->value === '';
    }
    
    /**
     * Determine if the given string is not empty.
     *
     * @return bool
     */
    public function isNotEmpty(): bool
    {
        return ! $this->isEmpty();
    }
    
    /**
     * Convert a string to kebab case.
     *
     * @return static
     */
    public function kebab(): static
    {
        return new static(Str::kebab($this->value));
    }
    
    /**
     * Return the length of the given string.
     *
     * @param string|null $encoding
     *
     * @return int
     */
    public function length(string $encoding = null): int
    {
        return Str::length($this->value, $encoding);
    }
    
    /**
     * Limit the number of characters in a string.
     *
     * @param int    $limit
     * @param string $end
     *
     * @return static
     */
    public function limit(int $limit = 100, string $end = '...'): static
    {
        return new static(Str::limit($this->value, $limit, $end));
    }
    
    /**
     * Convert the given string to lower-case.
     *
     * @return static
     */
    public function lower(): static
    {
        return new static(Str::lower($this->value));
    }
    
    /**
     * Convert GitHub flavored Markdown into HTML.
     *
     * @param array $options
     *
     * @return static
     * @throws \League\CommonMark\Exception\CommonMarkException
     */
    public function markdown(array $options = []): static
    {
        return new static(Str::markdown($this->value, $options));
    }
    
    /**
     * Convert inline Markdown into HTML.
     *
     * @param array $options
     *
     * @return static
     * @throws \League\CommonMark\Exception\CommonMarkException
     */
    public function inlineMarkdown(array $options = []): static
    {
        return new static(Str::inlineMarkdown($this->value, $options));
    }
    
    /**
     * Masks a portion of a string with a repeated character.
     *
     * @param string   $character
     * @param int      $index
     * @param int|null $length
     * @param string   $encoding
     *
     * @return static
     */
    public function mask(string $character, int $index, int $length = null, string $encoding = 'UTF-8'): static
    {
        return new static(Str::mask($this->value, $character, $index, $length, $encoding));
    }
    
    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     *
     * @return static
     */
    public function match(string $pattern): static
    {
        return new static(Str::match($pattern, $this->value));
    }
    
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|iterable<string> $pattern
     *
     * @return bool
     */
    public function isMatch(array|string $pattern): bool
    {
        return Str::isMatch($pattern, $this->value);
    }
    
    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     *
     * @return \Illuminate\Support\Collection
     */
    public function matchAll(string $pattern): Collection
    {
        return Str::matchAll($pattern, $this->value);
    }
    
    /**
     * Determine if the string matches the given pattern.
     *
     * @param string $pattern
     *
     * @return bool
     */
    public function test(string $pattern): bool
    {
        return $this->isMatch($pattern);
    }
    
    /**
     * Remove all non-numeric characters from a string.
     *
     * @return static
     */
    public function numbers(): static
    {
        return new static(Str::numbers($this->value));
    }
    
    /**
     * Pad both sides of the string with another.
     *
     * @param int    $length
     * @param string $pad
     *
     * @return static
     */
    public function padBoth(int $length, string $pad = ' '): static
    {
        return new static(Str::padBoth($this->value, $length, $pad));
    }
    
    /**
     * Pad the left side of the string with another.
     *
     * @param int    $length
     * @param string $pad
     *
     * @return static
     */
    public function padLeft(int $length, string $pad = ' '): static
    {
        return new static(Str::padLeft($this->value, $length, $pad));
    }
    
    /**
     * Pad the right side of the string with another.
     *
     * @param int    $length
     * @param string $pad
     *
     * @return static
     */
    public function padRight(int $length, string $pad = ' '): static
    {
        return new static(Str::padRight($this->value, $length, $pad));
    }
    
    /**
     * Parse a Class callback into class and method.
     *
     * @param string|null $default
     *
     * @return array<int, string|null>
     */
    public function parseCallback(string $default = null): array
    {
        return Str::parseCallback($this->value, $default);
    }
    
    /**
     * Call the given callback and return a new string.
     *
     * @param  callable  $callback
     * @return static
     */
    public function pipe(callable $callback): static
    {
        return new static($callback($this));
    }
    
    /**
     * Get the plural form of an English word.
     *
     * @param \Countable|array|int $count
     *
     * @return static
     */
    public function plural(Countable|array|int $count = 2): static
    {
        return new static(Str::plural($this->value, $count));
    }
    
    /**
     * Pluralize the last word of an English, studly caps case string.
     *
     * @param \Countable|array|int $count
     *
     * @return static
     */
    public function pluralStudly(Countable|array|int $count = 2): static
    {
        return new static(Str::pluralStudly($this->value, $count));
    }
    
    /**
     * Find the multibyte safe position of the first occurrence of the given substring.
     *
     * @param string      $needle
     * @param int         $offset
     * @param string|null $encoding
     *
     * @return int|false
     */
    public function position(string $needle, int $offset = 0, string $encoding = null): false|int
    {
        return Str::position($this->value, $needle, $offset, $encoding);
    }
    
    /**
     * Prepend the given values to the string.
     *
     * @param  string  ...$values
     * @return static
     */
    public function prepend(...$values): static
    {
        return new static(implode('', $values).$this->value);
    }
    
    /**
     * Remove any occurrence of the given string in the subject.
     *
     * @param string|iterable<string> $search
     * @param bool                    $caseSensitive
     *
     * @return static
     */
    public function remove(array|string $search, bool $caseSensitive = true): static
    {
        return new static(Str::remove($search, $this->value, $caseSensitive));
    }
    
    /**
     * Reverse the string.
     *
     * @return static
     */
    public function reverse(): static
    {
        return new static(Str::reverse($this->value));
    }
    
    /**
     * Repeat the string.
     *
     * @param  int  $times
     * @return static
     */
    public function repeat(int $times): static
    {
        return new static(str_repeat($this->value, $times));
    }
    
    /**
     * Replace the given value in the given string.
     *
     * @param string|iterable<string> $search
     * @param string|iterable<string> $replace
     * @param bool                    $caseSensitive
     *
     * @return static
     */
    public function replace(array|string $search, array|string $replace, bool $caseSensitive = true): static
    {
        return new static(Str::replace($search, $replace, $this->value, $caseSensitive));
    }
    
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param string           $search
     * @param iterable<string> $replace
     *
     * @return static
     */
    public function replaceArray(string $search, array $replace): static
    {
        return new static(Str::replaceArray($search, $replace, $this->value));
    }
    
    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     *
     * @return static
     */
    public function replaceFirst(string $search, string $replace): static
    {
        return new static(Str::replaceFirst($search, $replace, $this->value));
    }
    
    /**
     * Replace the first occurrence of the given value if it appears at the start of the string.
     *
     * @param string $search
     * @param string $replace
     *
     * @return static
     */
    public function replaceStart(string $search, string $replace): static
    {
        return new static(Str::replaceStart($search, $replace, $this->value));
    }
    
    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     *
     * @return static
     */
    public function replaceLast(string $search, string $replace): static
    {
        return new static(Str::replaceLast($search, $replace, $this->value));
    }
    
    /**
     * Replace the last occurrence of a given value if it appears at the end of the string.
     *
     * @param string $search
     * @param string $replace
     *
     * @return static
     */
    public function replaceEnd(string $search, string $replace): static
    {
        return new static(Str::replaceEnd($search, $replace, $this->value));
    }
    
    /**
     * Replace the patterns matching the given regular expression.
     *
     * @param array|string    $pattern
     * @param string|\Closure $replace
     * @param int             $limit
     *
     * @return static
     */
    public function replaceMatches(array|string $pattern, string|Closure $replace, int $limit = -1): static
    {
        if ($replace instanceof Closure) {
            return new static(preg_replace_callback($pattern, $replace, $this->value, $limit));
        }
        
        return new static(preg_replace($pattern, $replace, $this->value, $limit));
    }
    
    /**
     * Parse input from a string to a collection, according to a format.
     *
     * @param string $format
     *
     * @return \Illuminate\Support\Collection
     */
    public function scan(string $format): Collection
    {
        return collect(sscanf($this->value, $format));
    }
    
    /**
     * Remove all "extra" blank space from the given string.
     *
     * @return static
     */
    public function squish(): static
    {
        return new static(Str::squish($this->value));
    }
    
    /**
     * Begin a string with a single instance of a given value.
     *
     * @param string $prefix
     *
     * @return static
     */
    public function start(string $prefix): static
    {
        return new static(Str::start($this->value, $prefix));
    }
    
    /**
     * Strip HTML and PHP tags from the given string.
     *
     * @param string|string[]|null $allowedTags
     *
     * @return static
     */
    public function stripTags(array|string $allowedTags = null): static
    {
        return new static(strip_tags($this->value, $allowedTags));
    }
    
    /**
     * Convert the given string to upper-case.
     *
     * @return static
     */
    public function upper(): static
    {
        return new static(Str::upper($this->value));
    }
    
    /**
     * Convert the given string to proper case.
     *
     * @return static
     */
    public function title(): static
    {
        return new static(Str::title($this->value));
    }
    
    /**
     * Convert the given string to proper case for each word.
     *
     * @return static
     */
    public function headline(): static
    {
        return new static(Str::headline($this->value));
    }
    
    /**
     * Convert the given string to APA-style title case.
     *
     * @return static
     */
    public function apa(): static
    {
        return new static(Str::apa($this->value));
    }
    
    /**
     * Transliterate a string to its closest ASCII representation.
     *
     * @param string|null $unknown
     * @param bool|null   $strict
     *
     * @return static
     */
    public function transliterate(?string $unknown = '?', ?bool $strict = false): static
    {
        return new static(Str::transliterate($this->value, $unknown, $strict));
    }
    
    /**
     * Get the singular form of an English word.
     *
     * @return static
     */
    public function singular(): static
    {
        return new static(Str::singular($this->value));
    }
    
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string                $separator
     * @param string|null           $language
     * @param array<string, string> $dictionary
     *
     * @return static
     */
    public function slug(string $separator = '-', ?string $language = 'en', array $dictionary = ['@' => 'at']): static
    {
        return new static(Str::slug($this->value, $separator, $language, $dictionary));
    }
    
    /**
     * Convert a string to snake case.
     *
     * @param string $delimiter
     *
     * @return static
     */
    public function snake(string $delimiter = '_'): static
    {
        return new static(Str::snake($this->value, $delimiter));
    }
    
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string|iterable<string> $needles
     *
     * @return bool
     */
    public function startsWith(array|string $needles): bool
    {
        return Str::startsWith($this->value, $needles);
    }
    
    /**
     * Convert a value to studly caps case.
     *
     * @return static
     */
    public function studly(): static
    {
        return new static(Str::studly($this->value));
    }
    
    /**
     * Returns the portion of the string specified by the start and length parameters.
     *
     * @param int      $start
     * @param int|null $length
     * @param string   $encoding
     *
     * @return static
     */
    public function substr(int $start, int $length = null, string $encoding = 'UTF-8'): static
    {
        return new static(Str::substr($this->value, $start, $length, $encoding));
    }
    
    /**
     * Returns the number of substring occurrences.
     *
     * @param string   $needle
     * @param int      $offset
     * @param int|null $length
     *
     * @return int
     */
    public function substrCount(string $needle, int $offset = 0, int $length = null): int
    {
        return Str::substrCount($this->value, $needle, $offset, $length);
    }
    
    /**
     * Replace text within a portion of a string.
     *
     * @param string|string[] $replace
     * @param int|int[]       $offset
     * @param int|int[]|null  $length
     *
     * @return static
     */
    public function substrReplace(array|string $replace, array|int $offset = 0, array|int $length = null): static
    {
        return new static(Str::substrReplace($this->value, $replace, $offset, $length));
    }
    
    /**
     * Swap multiple keywords in a string with other keywords.
     *
     * @param  array  $map
     * @return static
     */
    public function swap(array $map): static
    {
        return new static(strtr($this->value, $map));
    }
    
    /**
     * Take the first or last {$limit} characters.
     *
     * @param  int  $limit
     * @return static
     */
    public function take(int $limit): static
    {
        if ($limit < 0) {
            return $this->substr($limit);
        }
        
        return $this->substr(0, $limit);
    }
    
    /**
     * Trim the string of the given characters.
     *
     * @param string|null $characters
     *
     * @return static
     */
    public function trim(string $characters = null): static
    {
        return new static(trim(...array_merge([$this->value], func_get_args())));
    }
    
    /**
     * Left trim the string of the given characters.
     *
     * @param string|null $characters
     *
     * @return static
     */
    public function ltrim(string $characters = null): static
    {
        return new static(ltrim(...array_merge([$this->value], func_get_args())));
    }
    
    /**
     * Right trim the string of the given characters.
     *
     * @param string|null $characters
     *
     * @return static
     */
    public function rtrim(string $characters = null): static
    {
        return new static(rtrim(...array_merge([$this->value], func_get_args())));
    }
    
    /**
     * Make a string's first character lowercase.
     *
     * @return static
     */
    public function lcfirst(): static
    {
        return new static(Str::lcfirst($this->value));
    }
    
    /**
     * Make a string's first character uppercase.
     *
     * @return static
     */
    public function ucfirst(): static
    {
        return new static(Str::ucfirst($this->value));
    }
    
    /**
     * Split a string by uppercase characters.
     *
     * @return \Illuminate\Support\Collection<int, string>
     */
    public function ucsplit(): Collection
    {
        return collect(Str::ucsplit($this->value));
    }
    
    /**
     * Execute the given callback if the string contains a given substring.
     *
     * @param string|iterable<string> $needles
     * @param callable                $callback
     * @param callable|null           $default
     *
     * @return static
     */
    public function whenContains(array|string $needles, callable $callback, callable $default = null): static
    {
        return $this->when($this->contains($needles), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is empty.
     *
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenEmpty(callable $callback, callable $default = null): static
    {
        return $this->when($this->isEmpty(), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is not empty.
     *
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenNotEmpty(callable $callback, callable $default = null): static
    {
        return $this->when($this->isNotEmpty(), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string ends with a given substring.
     *
     * @param string|iterable<string> $needles
     * @param callable                $callback
     * @param callable|null           $default
     *
     * @return static
     */
    public function whenEndsWith(array|string $needles, callable $callback, callable $default = null): static
    {
        return $this->when($this->endsWith($needles), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is an exact match with the given value.
     *
     * @param string        $value
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenExactly(string $value, callable $callback, callable $default = null): static
    {
        return $this->when($this->exactly($value), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is not an exact match with the given value.
     *
     * @param string        $value
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenNotExactly(string $value, callable $callback, callable $default = null): static
    {
        return $this->when(! $this->exactly($value), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string matches a given pattern.
     *
     * @param string|iterable<string> $pattern
     * @param callable                $callback
     * @param callable|null           $default
     *
     * @return static
     */
    public function whenIs(array|string $pattern, callable $callback, callable $default = null): static
    {
        return $this->when($this->is($pattern), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is 7-bit ASCII.
     *
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenIsAscii(callable $callback, callable $default = null): static
    {
        return $this->when($this->isAscii(), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is a valid UUID.
     *
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenIsUuid(callable $callback, callable $default = null): static
    {
        return $this->when($this->isUuid(), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string is a valid ULID.
     *
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenIsUlid(callable $callback, callable $default = null): static
    {
        return $this->when($this->isUlid(), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string starts with a given substring.
     *
     * @param string|iterable<string> $needles
     * @param callable                $callback
     * @param callable|null           $default
     *
     * @return static
     */
    public function whenStartsWith(array|string $needles, callable $callback, callable $default = null): static
    {
        return $this->when($this->startsWith($needles), $callback, $default);
    }
    
    /**
     * Execute the given callback if the string matches the given pattern.
     *
     * @param string        $pattern
     * @param callable      $callback
     * @param callable|null $default
     *
     * @return static
     */
    public function whenTest(string $pattern, callable $callback, callable $default = null): static
    {
        return $this->when($this->test($pattern), $callback, $default);
    }
    
    /**
     * Limit the number of words in a string.
     *
     * @param int    $words
     * @param string $end
     *
     * @return static
     */
    public function words(int $words = 100, string $end = '...'): static
    {
        return new static(Str::words($this->value, $words, $end));
    }
    
    /**
     * Get the number of words a string contains.
     *
     * @param string|null $characters
     *
     * @return int
     */
    public function wordCount(string $characters = null): int
    {
        return Str::wordCount($this->value, $characters);
    }
    
    /**
     * Wrap a string to a given number of characters.
     *
     * @param int    $characters
     * @param string $break
     * @param bool   $cutLongWords
     *
     * @return static
     */
    public function wordWrap(int    $characters = 75,
                             string $break = "\n", bool $cutLongWords = false): static
    {
        return new static(Str::wordWrap($this->value, $characters, $break, $cutLongWords));
    }
    
    /**
     * Wrap the string with the given strings.
     *
     * @param string      $before
     * @param string|null $after
     *
     * @return static
     */
    public function wrap(string $before, string $after = null): static
    {
        return new static(Str::wrap($this->value, $before, $after));
    }
    
    /**
     * Unwrap the string with the given strings.
     *
     * @param string      $before
     * @param string|null $after
     *
     * @return static
     */
    public function unwrap(string $before, string $after = null): static
    {
        return new static(Str::unwrap($this->value, $before, $after));
    }
    
    /**
     * Convert the string into a `HtmlString` instance.
     *
     * @return HtmlString
     */
    public function toHtmlString(): HtmlString
    {
        return new HtmlString($this->value);
    }
    
    /**
     * Convert the string to Base64 encoding.
     *
     * @return static
     */
    public function toBase64(): static
    {
        return new static(base64_encode($this->value));
    }
    
    /**
     * Decode the Base64 encoded string.
     *
     * @param bool $strict
     *
     * @return static
     */
    public function fromBase64(bool $strict = false): static
    {
        return new static(base64_decode($this->value, $strict));
    }
    
    /**
     * Dump the string.
     *
     * @param  mixed  ...$args
     * @return $this
     */
    public function dump(...$args): static
    {
        dump($this->value, ...$args);
        
        return $this;
    }
    
    /**
     * Get the underlying string value.
     *
     * @return string|null
     */
    public function value(): ?string
    {
        return $this->toString();
    }
    
    /**
     * Get the underlying string value.
     *
     * @return string|null
     */
    public function toString(): ?string
    {
        return $this->value;
    }
    
    /**
     * Get the underlying string value as an integer.
     *
     * @param int $base
     *
     * @return int
     */
    public function toInteger(int $base = 10): int
    {
        return intval($this->value, $base);
    }
    
    /**
     * Get the underlying string value as a float.
     *
     * @return float
     */
    public function toFloat(): float
    {
        return floatval($this->value);
    }
    
    /**
     * Get the underlying string value as a boolean.
     *
     * Returns true when value is "1", "true", "on", and "yes". Otherwise, returns false.
     *
     * @return bool
     */
    public function toBoolean(): bool
    {
        return filter_var($this->value, FILTER_VALIDATE_BOOLEAN);
    }
    
    /**
     * Get the underlying string value as a Carbon instance.
     *
     * @param string|null $format
     * @param string|null $tz
     *
     * @return \Elixant\Utility\Carbon
     */
    public function toDate(string $format = null,
                           string $tz = null): Carbon
    {
        if (is_null($format)) {
            return
                (new DateFactory)->parse($this->value, $tz);
        }
        
        return Carbon::createFromFormat($format, $this->value, $tz);
    }
    
    /**
     * Convert the object to a string when JSON encoded.
     *
     * @return string
     */
    public function jsonSerialize(): string
    {
        return $this->__toString();
    }
    
    /**
     * Determine if the given offset exists.
     *
     * @param  mixed  $offset
     * @return bool
     */
    public function offsetExists(mixed $offset): bool
    {
        return isset($this->value[$offset]);
    }
    
    /**
     * Get the value at the given offset.
     *
     * @param  mixed  $offset
     * @return string
     */
    public function offsetGet(mixed $offset): string
    {
        return $this->value[$offset];
    }
    
    /**
     * Set the value at the given offset.
     *
     * @param mixed $offset
     * @param mixed $value
     *
     * @return void
     */
    public function offsetSet(mixed $offset, mixed $value): void
    {
        $this->value[$offset] = $value;
    }
    
    /**
     * Unset the value at the given offset.
     *
     * @param  mixed  $offset
     * @return void
     */
    public function offsetUnset(mixed $offset): void
    {
        unset($this->value[$offset]);
    }
    
    /**
     * Proxy dynamic properties onto methods.
     *
     * @param string $key
     *
     * @return mixed
     */
    public function __get(string $key)
    {
        return $this->{$key}();
    }
    
    /**
     * Get the raw string value.
     *
     * @return string
     */
    public function __toString()
    {
        return (string) $this->value;
    }
}
