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

use Closure;
use Countable;
use Throwable;
use Traversable;
use JsonException;
use Ramsey\Uuid\Uuid;
use DateTimeInterface;
use voku\helper\ASCII;
use Ramsey\Uuid\UuidFactory;
use Ramsey\Uuid\UuidInterface;
use Symfony\Component\Uid\Ulid;
use Illuminate\Support\Collection;
use League\CommonMark\MarkdownConverter;
use Ramsey\Uuid\Generator\CombGenerator;
use League\CommonMark\Environment\Environment;
use Ramsey\Uuid\Codec\TimestampFirstCombCodec;
use League\CommonMark\GithubFlavoredMarkdownConverter;
use League\CommonMark\Extension\GithubFlavoredMarkdownExtension;
use League\CommonMark\Extension\InlinesOnly\InlinesOnlyExtension;

/**
 * Str Class
 * Created Apr 07, 2024.
 *
 * The Str class is a utility class that provides a simple way to
 * create and manage string values. The class provides a number of
 * convenience methods for creating and managing string values.
 *
 * @package         Elixant\Utility::Str
 * @class           Str
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
class Str
{
    /**
     * The cache of snake-cased words.
     *
     * @var array
     */
    protected static array $snakeCache = [];
    /**
     * The cache of camel-cased words.
     *
     * @var array
     */
    protected static array $camelCache = [];
    /**
     * The cache of studly-cased words.
     *
     * @var array
     */
    protected static array $studlyCache = [];
    /**
     * The callback that should be used to generate UUIDs.
     *
     * @var callable|null
     */
    protected static $uuidFactory;
    /**
     * The callback that should be used to generate ULIDs.
     *
     * @var callable|null
     */
    protected static $ulidFactory;
    /**
     * The callback that should be used to generate random strings.
     *
     * @var callable|null
     */
    protected static $randomStringFactory;
    
    /**
     * Get a new stringable object from the given string.
     *
     * @param string $string
     *
     * @return \Elixant\Utility\Stringable
     */
    public static function of(string $string): Stringable
    {
        return new Stringable($string);
    }
    
    /**
     * Transliterate a string to its closest ASCII representation.
     *
     * @param string      $string
     * @param string|null $unknown
     * @param bool|null   $strict
     *
     * @return string
     */
    public static function transliterate(string $string, ?string $unknown = '?',
        ?bool $strict = false
    ): string {
        return ASCII::to_transliterate($string, $unknown, $strict);
    }
    
    /**
     * Get the portion of a string between two given values.
     *
     * @param string $subject
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public static function between(string $subject, string $from, string $to
    ): string {
        if ($from === '' || $to === '') {
            return $subject;
        }
        
        return static::beforeLast(static::after($subject, $from), $to);
    }
    
    /**
     * Get the portion of a string before the last occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     *
     * @return string
     */
    public static function beforeLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }
        $pos = mb_strrpos($subject, $search);
        if ($pos === false) {
            return $subject;
        }
        
        return static::substr($subject, 0, $pos);
    }
    
    /**
     * Returns the portion of the string specified by the start and length
     * parameters.
     *
     * @param string   $string
     * @param int      $start
     * @param int|null $length
     * @param string   $encoding
     *
     * @return string
     */
    public static function substr(string $string, int $start,
        int $length = null, string $encoding = 'UTF-8'
    ): string {
        return mb_substr($string, $start, $length, $encoding);
    }
    
    /**
     * Return the remainder of a string after the first occurrence of a given
     * value.
     *
     * @param string $subject
     * @param string $search
     *
     * @return string
     */
    public static function after(string $subject, string $search): string
    {
        return $search === ''
            ? $subject
            : array_reverse(
                  explode($search, $subject, 2)
              )[0];
    }
    
    /**
     * Get the smallest possible portion of a string between two given values.
     *
     * @param string $subject
     * @param string $from
     * @param string $to
     *
     * @return string
     */
    public static function betweenFirst(string $subject, string $from,
        string $to
    ): string {
        if ($from === '' || $to === '') {
            return $subject;
        }
        
        return static::before(static::after($subject, $from), $to);
    }
    
    /**
     * Get the portion of a string before the first occurrence of a given value.
     *
     * @param string $subject
     * @param string $search
     *
     * @return string
     */
    public static function before(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }
        $result = strstr($subject, $search, true);
        
        return $result === false ? $subject : $result;
    }
    
    /**
     * Convert a value to camel case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function camel(string $value): string
    {
        if (isset(static::$camelCache[$value])) {
            return static::$camelCache[$value];
        }
        
        return static::$camelCache[$value] = lcfirst(static::studly($value));
    }
    
    /**
     * Convert a value to studly caps case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function studly(string $value): string
    {
        $key = $value;
        if (isset(static::$studlyCache[$key])) {
            return static::$studlyCache[$key];
        }
        $words       = explode(' ', static::replace(['-', '_'], ' ', $value));
        $studlyWords = array_map(fn($word) => static::ucfirst($word), $words);
        
        return static::$studlyCache[$key] = implode($studlyWords);
    }
    
    /**
     * Replace the given value in the given string.
     *
     * @param iterable|string $search
     * @param iterable|string $replace
     * @param iterable|string $subject
     * @param bool            $caseSensitive
     *
     * @return string|string[]
     */
    public static function replace(iterable|string $search,
        iterable|string $replace, iterable|string $subject,
        bool $caseSensitive = true
    ): array|string {
        if ($search instanceof Traversable) {
            $search = collect($search)->all();
        }
        if ($replace instanceof Traversable) {
            $replace = collect($replace)->all();
        }
        if ($subject instanceof Traversable) {
            $subject = collect($subject)->all();
        }
        
        return $caseSensitive
            ? str_replace($search, $replace, $subject)
            : str_ireplace($search, $replace, $subject);
    }
    
    /**
     * Make a string's first character uppercase.
     *
     * @param string $string
     *
     * @return string
     */
    public static function ucfirst(string $string): string
    {
        return static::upper(static::substr($string, 0, 1)) . static::substr(
                $string, 1
            );
    }
    
    /**
     * Convert the given string to upper-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function upper(string $value): string
    {
        return mb_strtoupper($value, 'UTF-8');
    }
    
    /**
     * Get the character at the specified index.
     *
     * @param string $subject
     * @param int    $index
     *
     * @return string|false
     */
    public static function charAt(string $subject, int $index): false|string
    {
        $length = mb_strlen($subject);
        if ($index < 0 ? $index < -$length : $index > $length - 1) {
            return false;
        }
        
        return mb_substr($subject, $index, 1);
    }
    
    /**
     * Determine if a given string contains all array values.
     *
     * @param string           $haystack
     * @param iterable<string> $needles
     * @param bool             $ignoreCase
     *
     * @return bool
     */
    public static function containsAll(string $haystack, array $needles,
        bool $ignoreCase = false
    ): bool {
        foreach ($needles as $needle) {
            if ( ! static::contains($haystack, $needle, $ignoreCase)) {
                return false;
            }
        }
        
        return true;
    }
    
    /**
     * Determine if a given string contains a given substring.
     *
     * @param string                  $haystack
     * @param string|iterable<string> $needles
     * @param bool                    $ignoreCase
     *
     * @return bool
     */
    public static function contains(string $haystack, array|string $needles,
        bool $ignoreCase = false
    ): bool {
        if ($ignoreCase) {
            $haystack = mb_strtolower($haystack);
        }
        if ( ! is_iterable($needles)) {
            $needles = (array)$needles;
        }
        foreach ($needles as $needle) {
            if ($ignoreCase) {
                $needle = mb_strtolower($needle);
            }
            if ($needle !== '' && str_contains($haystack, $needle)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Convert the case of a string.
     *
     * @param string      $string
     * @param int         $mode
     * @param string|null $encoding
     *
     * @return string
     */
    public static function convertCase(string $string, int $mode = MB_CASE_FOLD,
        ?string $encoding = 'UTF-8'
    ): string {
        return mb_convert_case($string, $mode, $encoding);
    }
    
    /**
     * Extracts an excerpt from text that matches the first instance of a
     * phrase.
     *
     * @param string $text
     * @param string $phrase
     * @param array  $options
     *
     * @return string|null
     */
    public static function excerpt(string $text, string $phrase = '',
        array $options = []
    ): ?string {
        $radius   = $options['radius'] ?? 100;
        $omission = $options['omission'] ?? '...';
        preg_match(
            '/^(.*?)(' . preg_quote($phrase, '/') . ')(.*)$/iu', $text, $matches
        );
        if (empty($matches)) {
            return null;
        }
        $start = ltrim($matches[1]);
        $start = str(
            mb_substr(
                $start, max(mb_strlen($start, 'UTF-8') - $radius, 0), $radius,
                'UTF-8'
            )
        )->ltrim()->unless(
            fn($startWithRadius) => $startWithRadius->exactly($start),
            fn($startWithRadius) => $startWithRadius->prepend($omission),
        );
        $end   = rtrim($matches[3]);
        $end   = str(mb_substr($end, 0, $radius, 'UTF-8'))->rtrim()->unless(
            fn($endWithRadius) => $endWithRadius->exactly($end),
            fn($endWithRadius) => $endWithRadius->append($omission),
        );
        
        return $start->append($matches[2], $end)->toString();
    }
    
    /**
     * Cap a string with a single instance of a given value.
     *
     * @param mixed  $value
     * @param string $cap
     *
     * @return string
     */
    public static function finish(mixed $value, string $cap): string
    {
        $quoted = preg_quote($cap, '/');
        
        return preg_replace('/(?:' . $quoted . ')+$/u', '', $value) . $cap;
    }
    
    /**
     * Wrap the string with the given strings.
     *
     * @param string      $value
     * @param string      $before
     * @param string|null $after
     *
     * @return string
     */
    public static function wrap(string $value, string $before,
        string $after = null
    ): string {
        return sprintf("%s%s%s", $before, $value, $after ? : $before);
    }
    
    /**
     * Unwrap the string with the given strings.
     *
     * @param string      $value
     * @param string      $before
     * @param string|null $after
     *
     * @return string
     */
    public static function unwrap(string $value, string $before,
        string $after = null
    ): string {
        if (static::startsWith($value, $before)) {
            $value = static::substr($value, static::length($before));
        }
        if (static::endsWith($value, $after ??= $before)) {
            $value = static::substr($value, 0, -static::length($after));
        }
        
        return $value;
    }
    
    /**
     * Determine if a given string starts with a given substring.
     *
     * @param string                  $haystack
     * @param string|iterable<string> $needles
     *
     * @return bool
     */
    public static function startsWith(string $haystack, array|string $needles
    ): bool {
        if ( ! is_iterable($needles)) {
            $needles = [$needles];
        }
        foreach ($needles as $needle) {
            if ((string)$needle !== '' && str_starts_with($haystack, $needle)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Return the length of the given string.
     *
     * @param string      $value
     * @param string|null $encoding
     *
     * @return int
     */
    public static function length(string $value, string $encoding = null): int
    {
        return mb_strlen($value, $encoding);
    }
    
    /**
     * Determine if a given string ends with a given substring.
     *
     * @param string                  $haystack
     * @param string|iterable<string> $needles
     *
     * @return bool
     */
    public static function endsWith(string $haystack, array|string $needles
    ): bool {
        if ( ! is_iterable($needles)) {
            $needles = (array)$needles;
        }
        foreach ($needles as $needle) {
            if ((string)$needle !== '' && str_ends_with($haystack, $needle)) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|iterable<string> $pattern
     * @param string                  $value
     *
     * @return bool
     */
    public static function is(array|string $pattern, string $value): bool
    {
        if ( ! is_iterable($pattern)) {
            $pattern = [$pattern];
        }
        foreach ($pattern as $p) {
            $pattern = (string)$p;
            // If the given value is an exact match we can of course return true right
            // from the beginning. Otherwise, we will translate asterisks and do an
            // actual pattern match against the two strings to see if they match.
            if ($pattern === $value) {
                return true;
            }
            $pattern = preg_quote($pattern, '#');
            // Asterisks are translated into zero-or-more regular expression wildcards
            // to make it convenient to check if the strings starts with the given
            // pattern such as "library/*", making any string check convenient.
            $pattern = str_replace('\*', '.*', $pattern);
            if (preg_match('#^' . $pattern . '\z#u', $value) === 1) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Determine if a given string is 7-bit ASCII.
     *
     * @param string $value
     *
     * @return bool
     */
    public static function isAscii(string $value): bool
    {
        return ASCII::is_ascii($value);
    }
    
    /**
     * Determine if a given value is valid JSON.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isJson(mixed $value): bool
    {
        if ( ! is_string($value)) {
            return false;
        }
        if (function_exists('json_validate')) {
            return json_validate($value);
        }
        try {
            json_decode($value, true, 512, JSON_THROW_ON_ERROR);
        } catch (JsonException) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Determine if a given value is a valid URL.
     *
     * @param mixed $value
     * @param array $protocols
     *
     * @return bool
     */
    public static function isUrl(mixed $value, array $protocols = []): bool
    {
        if ( ! is_string($value)) {
            return false;
        }
        $protocolList = empty($protocols)
            ? 'aaa|aas|about|acap|acct|acd|acr|adiumxtra|adt|afp|afs|aim|amss|android|appdata|apt|ark|attachment|aw|barion|\beshare|bitcoin|bitcoincash|blob|'
            .
            'bolo|browserext|calculator|\callto|cap|cast|casts|chrome|chrome-extension|cid|coap|coap\+tcp|coap\+ws|coaps|coaps\+tcp|coaps\+ws|com-eventbrite-attendee|content|conti|crid|cvs|dab|data|dav|diaspora|dict|did|dis|dlna-playcontainer|dlna-playsingle|dns|dntp|dpp|drm|drop|dtn|dvb|ed2k|elsi|example|facetime|fax|feed|feedready|file|filesystem|finger|first-run-pen-experience|fish|fm|ftp|fuchsia-pkg|geo|gg|git|gizmoproject|go|gopher|graph|gtalk|h323|ham|hcap|hcp|http|https|hxxp|hxxps|hydrazone|iax|icap|icon|im|imap|info|iotdisco|ipn|ipp|ipps|irc|irc6|ircs|iris|iris\.beep|iris\.lwz|iris\.xpc|iris\.xpcs|isostore|itms|jabber|jar|jms|keyparc|lastfm|ldap|ldaps|leaptofrogans|lorawan|lvlt|magnet|mailserver|mailto|maps|market|message|mid|mms|modem|mongodb|moz|ms-access|ms-browser-extension|ms-calculator|ms-drive-to|ms-enrollment|ms-excel|ms-eyecontrolspeech|ms-gamebarservices|ms-gamingoverlay|ms-getoffice|ms-help|ms-infopath|ms-inputapp|ms-lockscreencomponent-config|ms-media-stream-id|ms-mixedrealitycapture|ms-mobileplans|ms-officeapp|ms-people|ms-project|ms-powerpoint|ms-publisher|ms-restoretabcompanion|ms-screenclip|ms-screensketch|ms-search|ms-search-repair|ms-secondary-screen-controller|ms-secondary-screen-setup|ms-settings|ms-settings-airplanemode|ms-settings-bluetooth|ms-settings-camera|ms-settings-cellular|ms-settings-cloudstorage|ms-settings-connectabledevices|ms-settings-displays-topology|ms-settings-emailandaccounts|ms-settings-language|ms-settings-location|ms-settings-lock|ms-settings-nfctransactions|ms-settings-notifications|ms-settings-power|ms-settings-privacy|ms-settings-proximity|ms-settings-screenrotation|ms-settings-wifi|ms-settings-workplace|ms-spd|ms-sttoverlay|ms-transit-to|ms-useractivityset|ms-virtualtouchpad|ms-visio|ms-walk-to|ms-whiteboard|ms-whiteboard-cmd|ms-word|msnim|msrp|msrps|mss|mtqp|mumble|mupdate|mvn|news|nfs|ni|nih|nntp|notes|ocf|oid|onenote|onenote-cmd|opaquelocktoken|openpgp4fpr|pack|palm|paparazzi|payto|pkcs11|platform|pop|pres|prospero|proxy|pwid|psyc|pttp|qb|query|redis|rediss|reload|res|resource|rmi|rsync|rtmfp|rtmp|rtsp|rtsps|rtspu|s3|secondlife|service|session|sftp|sgn|shttp|sieve|simpleledger|sip|sips|skype|smb|sms|smtp|snews|snmp|soap\.beep|soap\.beeps|soldat|spiffe|spotify|ssh|steam|stun|stuns|submit|svn|tag|teamspeak|tel|teliaeid|telnet|tftp|tg|things|thismessage|tip|tn3270|tool|ts3server|turn|turns|tv|udp|unreal|urn|ut2004|v-event|vemmi|ventrilo|videotex|vnc|view-source|wais|webcal|wpid|ws|wss|wtai|wyciwyg|xcon|xcon-userid|xfire|xmlrpc\.beep|xmlrpc\.beeps|xmpp|xri|ymsgr|z39\.50|z39\.50r|z39\.50s'
            : implode('|', $protocols);
        /*
         * This pattern is derived from Symfony\Component\Validator\Constraints\UrlValidator (5.0.7).
         *
         * (c) Fabien Potencier <fabien@symfony.com> http://symfony.com
         */
        $pattern = '~^
            (LARAVEL_PROTOCOLS)://                                 # protocol
            (((?:[\_\.\pL\pN-]|%[0-9A-Fa-f]{2})+:)?((?:[\_\.\pL\pN-]|%[0-9A-Fa-f]{2})+)@)?  # basic auth
            (
                ([\pL\pN\pS\-\_\.])+(\.?([\pL\pN]|xn\-\-[\pL\pN-]+)+\.?) # a domain name
                    |                                                 # or
                \d{1,3}\.\d{1,3}\.\d{1,3}\.\d{1,3}                    # an IP address
                    |                                                 # or
                \[
                    (?:(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){6})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:::(?:(?:(?:[0-9a-f]{1,4})):){5})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){4})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,1}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){3})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,2}(?:(?:[0-9a-f]{1,4})))?::(?:(?:(?:[0-9a-f]{1,4})):){2})(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,3}(?:(?:[0-9a-f]{1,4})))?::(?:(?:[0-9a-f]{1,4})):)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,4}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:(?:(?:(?:[0-9a-f]{1,4})):(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9]))\.){3}(?:(?:25[0-5]|(?:[1-9]|1[0-9]|2[0-4])?[0-9])))))))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,5}(?:(?:[0-9a-f]{1,4})))?::)(?:(?:[0-9a-f]{1,4})))|(?:(?:(?:(?:(?:(?:[0-9a-f]{1,4})):){0,6}(?:(?:[0-9a-f]{1,4})))?::))))
                \]  # an IPv6 address
            )
            (:[0-9]+)?                              # a port (optional)
            (?:/ (?:[\pL\pN\-._\~!$&\'()*+,;=:@]|%[0-9A-Fa-f]{2})* )*          # a path
            (?:\? (?:[\pL\pN\-._\~!$&\'\[\]()*+,;=:@/?]|%[0-9A-Fa-f]{2})* )?   # a query (optional)
            (?:\# (?:[\pL\pN\-._\~!$&\'()*+,;=:@/?]|%[0-9A-Fa-f]{2})* )?       # a fragment (optional)
        $~ixu';
        
        return preg_match(
                str_replace('LARAVEL_PROTOCOLS', $protocolList, $pattern),
                $value
            ) > 0;
    }
    
    /**
     * Determine if a given value is a valid UUID.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isUuid(mixed $value): bool
    {
        if ( ! is_string($value)) {
            return false;
        }
        
        return preg_match(
                '/^[\da-fA-F]{8}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{4}-[\da-fA-F]{12}$/D',
                $value
            ) > 0;
    }
    
    /**
     * Determine if a given value is a valid ULID.
     *
     * @param mixed $value
     *
     * @return bool
     */
    public static function isUlid(mixed $value): bool
    {
        if ( ! is_string($value)) {
            return false;
        }
        
        return Ulid::isValid($value);
    }
    
    /**
     * Convert a string to kebab case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function kebab(string $value): string
    {
        return static::snake($value, '-');
    }
    
    /**
     * Convert a string to snake case.
     *
     * @param string $value
     * @param string $delimiter
     *
     * @return string
     */
    public static function snake(string $value, string $delimiter = '_'): string
    {
        $key = $value;
        if (isset(static::$snakeCache[$key][$delimiter])) {
            return static::$snakeCache[$key][$delimiter];
        }
        if ( ! ctype_lower($value)) {
            $value = preg_replace('/\s+/u', '', ucwords($value));
            $value = static::lower(
                preg_replace('/(.)(?=[A-Z])/u', '$1' . $delimiter, $value)
            );
        }
        
        return static::$snakeCache[$key][$delimiter] = $value;
    }
    
    /**
     * Convert the given string to lower-case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function lower(string $value): string
    {
        return mb_strtolower($value, 'UTF-8');
    }
    
    /**
     * Limit the number of characters in a string.
     *
     * @param string $value
     * @param int    $limit
     * @param string $end
     *
     * @return string
     */
    public static function limit(string $value, int $limit = 100,
        string $end = '...'
    ): string {
        if (mb_strwidth($value, 'UTF-8') <= $limit) {
            return $value;
        }
        
        return rtrim(mb_strimwidth($value, 0, $limit, '', 'UTF-8')) . $end;
    }
    
    /**
     * Limit the number of words in a string.
     *
     * @param string $value
     * @param int    $words
     * @param string $end
     *
     * @return string
     */
    public static function words(string $value, int $words = 100,
        string $end = '...'
    ): string {
        preg_match('/^\s*+\S++\s*+{1,' . $words . '}/u', $value, $matches);
        if ( ! isset($matches[0])
            || static::length($value) === static::length(
                $matches[0]
            )
        ) {
            return $value;
        }
        
        return rtrim($matches[0]) . $end;
    }
    
    /**
     * Converts GitHub flavored Markdown into HTML.
     *
     * @param string $string
     * @param array  $options
     *
     * @return string
     * @throws \League\CommonMark\Exception\CommonMarkException
     */
    public static function markdown(string $string, array $options = []): string
    {
        $converter = new GithubFlavoredMarkdownConverter($options);
        
        return (string)$converter->convert($string);
    }
    
    /**
     * Converts inline Markdown into HTML.
     *
     * @param string $string
     * @param array  $options
     *
     * @return string
     * @throws \League\CommonMark\Exception\CommonMarkException
     */
    public static function inlineMarkdown(string $string, array $options = []
    ): string {
        $environment = new Environment($options);
        $environment->addExtension(new GithubFlavoredMarkdownExtension());
        $environment->addExtension(new InlinesOnlyExtension());
        $converter = new MarkdownConverter($environment);
        
        return (string)$converter->convert($string);
    }
    
    /**
     * Masks a portion of a string with a repeated character.
     *
     * @param string   $string
     * @param string   $character
     * @param int      $index
     * @param int|null $length
     * @param string   $encoding
     *
     * @return string
     */
    public static function mask(string $string, string $character, int $index,
        int $length = null, string $encoding = 'UTF-8'
    ): string {
        if ($character === '') {
            return $string;
        }
        $segment = mb_substr($string, $index, $length, $encoding);
        if ($segment === '') {
            return $string;
        }
        $strlen     = mb_strlen($string, $encoding);
        $startIndex = $index;
        if ($index < 0) {
            $startIndex = $index < -$strlen ? 0 : $strlen + $index;
        }
        $start      = mb_substr($string, 0, $startIndex, $encoding);
        $segmentLen = mb_strlen($segment, $encoding);
        $end        = mb_substr($string, $startIndex + $segmentLen);
        
        return $start . str_repeat(
                mb_substr($character, 0, 1, $encoding), $segmentLen
            ) . $end;
    }
    
    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     * @param string $subject
     *
     * @return string
     */
    public static function match(string $pattern, string $subject): string
    {
        preg_match($pattern, $subject, $matches);
        if ( ! $matches) {
            return '';
        }
        
        return $matches[1] ?? $matches[0];
    }
    
    /**
     * Determine if a given string matches a given pattern.
     *
     * @param string|iterable<string> $pattern
     * @param string                  $value
     *
     * @return bool
     */
    public static function isMatch(array|string $pattern, string $value): bool
    {
        
        if ( ! is_iterable($pattern)) {
            $pattern = [$pattern];
        }
        foreach ($pattern as $p) {
            $pattern = (string)$p;
            if (preg_match($pattern, $value) === 1) {
                return true;
            }
        }
        
        return false;
    }
    
    /**
     * Get the string matching the given pattern.
     *
     * @param string $pattern
     * @param string $subject
     *
     * @return \Illuminate\Support\Collection
     */
    public static function matchAll(string $pattern, string $subject
    ): Collection {
        preg_match_all($pattern, $subject, $matches);
        if (empty($matches[0])) {
            return collect();
        }
        
        return collect($matches[1] ?? $matches[0]);
    }
    
    /**
     * Remove all non-numeric characters from a string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function numbers(string $value): string
    {
        return preg_replace('/[^0-9]/', '', $value);
    }
    
    /**
     * Pad both sides of a string with another.
     *
     * @param string $value
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function padBoth(string $value, int $length, string $pad = ' '
    ): string {
        if (function_exists('mb_str_pad')) {
            return mb_str_pad($value, $length, $pad, STR_PAD_BOTH);
        }
        $short      = max(0, $length - mb_strlen($value));
        $shortLeft  = floor($short / 2);
        $shortRight = ceil($short / 2);
        
        return mb_substr(str_repeat($pad, (int)$shortLeft), 0, $shortLeft) .
            $value .
            mb_substr(str_repeat($pad, (int)$shortRight), 0, $shortRight);
    }
    
    /**
     * Pad the left side of a string with another.
     *
     * @param string $value
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function padLeft(string $value, int $length, string $pad = ' '
    ): string {
        if (function_exists('mb_str_pad')) {
            return mb_str_pad($value, $length, $pad, STR_PAD_LEFT);
        }
        $short = max(0, $length - mb_strlen($value));
        
        return mb_substr(str_repeat($pad, $short), 0, $short) . $value;
    }
    
    /**
     * Pad the right side of a string with another.
     *
     * @param string $value
     * @param int    $length
     * @param string $pad
     *
     * @return string
     */
    public static function padRight(string $value, int $length,
        string $pad = ' '
    ): string {
        if (function_exists('mb_str_pad')) {
            return mb_str_pad($value, $length, $pad);
        }
        $short = max(0, $length - mb_strlen($value));
        
        return $value . mb_substr(str_repeat($pad, $short), 0, $short);
    }
    
    /**
     * Parse a Class[@]method style callback into class and method.
     *
     * @param string      $callback
     * @param string|null $default
     *
     * @return array<int, string|null>
     */
    public static function parseCallback(string $callback,
        string $default = null
    ): array {
        if (static::contains($callback, "@anonymous\0")) {
            if (static::substrCount($callback, '@') > 1) {
                return [
                    static::beforeLast($callback, '@'),
                    static::afterLast($callback, '@'),
                ];
            }
            
            return [$callback, $default];
        }
        
        return static::contains($callback, '@') ? explode('@', $callback, 2)
            : [$callback, $default];
    }
    
    /**
     * Returns the number of substring occurrences.
     *
     * @param string   $haystack
     * @param string   $needle
     * @param int      $offset
     * @param int|null $length
     *
     * @return int
     */
    public static function substrCount(string $haystack, string $needle,
        int $offset = 0, int $length = null
    ): int {
        if ( ! is_null($length)) {
            return substr_count($haystack, $needle, $offset, $length);
        }
        
        return substr_count($haystack, $needle, $offset);
    }
    
    /**
     * Return the remainder of a string after the last occurrence of a given
     * value.
     *
     * @param string $subject
     * @param string $search
     *
     * @return string
     */
    public static function afterLast(string $subject, string $search): string
    {
        if ($search === '') {
            return $subject;
        }
        $position = strrpos($subject, $search);
        if ($position === false) {
            return $subject;
        }
        
        return substr($subject, $position + strlen($search));
    }
    
    /**
     * Pluralize the last word of an English, studly caps case string.
     *
     * @param string               $value
     * @param \Countable|array|int $count
     *
     * @return string
     */
    public static function pluralStudly(string $value,
        Countable|array|int $count = 2
    ): string {
        $parts    = preg_split(
            '/(.)(?=[A-Z])/u', $value, -1, PREG_SPLIT_DELIM_CAPTURE
        );
        $lastWord = array_pop($parts);
        
        return implode('', $parts) . self::plural($lastWord, $count);
    }
    
    /**
     * Get the plural form of an English word.
     *
     * @param string               $value
     * @param \Countable|array|int $count
     *
     * @return string
     */
    public static function plural(string $value, Countable|array|int $count = 2
    ): string {
        return Pluralizer::plural($value, $count);
    }
    
    /**
     * Generate a random, secure password.
     *
     * @param int  $length
     * @param bool $letters
     * @param bool $numbers
     * @param bool $symbols
     * @param bool $spaces
     *
     * @return string
     * @throws \Random\RandomException
     */
    public static function password(int $length = 32, bool $letters = true,
        bool $numbers = true, bool $symbols = true, bool $spaces = false
    ): string {
        $password = new Collection();
        $options  = (new Collection([
            'letters' => $letters === true ? [
                'a', 'b', 'c', 'd', 'e', 'f', 'g', 'h', 'i', 'j', 'k',
                'l', 'm', 'n', 'o', 'p', 'q', 'r', 's', 't', 'u', 'v',
                'w', 'x', 'y', 'z', 'A', 'B', 'C', 'D', 'E', 'F', 'G',
                'H', 'I', 'J', 'K', 'L', 'M', 'N', 'O', 'P', 'Q', 'R',
                'S', 'T', 'U', 'V', 'W', 'X', 'Y', 'Z',
            ] : null,
            'numbers' => $numbers === true ? [
                '0', '1', '2', '3', '4', '5', '6', '7', '8', '9',
            ] : null,
            'symbols' => $symbols === true ? [
                '~', '!', '#', '$', '%', '^', '&', '*', '(', ')', '-',
                '_', '.', ',', '<', '>', '?', '/', '\\', '{', '}', '[',
                ']', '|', ':', ';',
            ] : null,
            'spaces'  => $spaces === true ? [' '] : null,
        ]))->filter()->each(
            fn($c) => $password->push($c[random_int(0, count($c) - 1)])
        )->flatten();
        $length   = $length - $password->count();
        
        return $password->merge(
            $options->pipe(
                fn($c) => Collection::times(
                    $length, fn() => $c[random_int(0, $c->count() - 1)]
                )
            )
        )->shuffle()->implode('');
    }
    
    /**
     * Find the multibyte safe position of the first occurrence of a given
     * substring in a string.
     *
     * @param string      $haystack
     * @param string      $needle
     * @param int         $offset
     * @param string|null $encoding
     *
     * @return int|false
     */
    public static function position(string $haystack, string $needle,
        int $offset = 0, string $encoding = null
    ): false|int {
        return mb_strpos($haystack, $needle, $offset, $encoding);
    }
    
    /**
     * Set the sequence that will be used to generate random strings.
     *
     * @param array         $sequence
     * @param callable|null $whenMissing
     *
     * @return void
     */
    public static function createRandomStringsUsingSequence(array $sequence,
        callable $whenMissing = null
    ): void {
        $next        = 0;
        $whenMissing ??= function ($length) use (&$next) {
            $factoryCache                = static::$randomStringFactory;
            static::$randomStringFactory = null;
            $randomString                = static::random($length);
            static::$randomStringFactory = $factoryCache;
            $next++;
            
            return $randomString;
        };
        static::createRandomStringsUsing(
            function ($length) use (&$next, $sequence, $whenMissing) {
                if (array_key_exists($next, $sequence)) {
                    return $sequence[$next++];
                }
                
                return $whenMissing($length);
            }
        );
    }
    
    /**
     * Generate a more truly "random" alpha-numeric string.
     *
     * @param int $length
     *
     * @return string
     */
    public static function random(int $length = 16): string
    {
        return (static::$randomStringFactory ?? function ($length) {
            $string = '';
            while (($len = strlen($string)) < $length) {
                $size      = $length - $len;
                $bytesSize = (int)ceil($size / 3) * 3;
                $bytes     = random_bytes($bytesSize);
                $string    .= substr(
                    str_replace(['/', '+', '='], '', base64_encode($bytes)), 0,
                    $size
                );
            }
            
            return $string;
        })(
            $length
        );
    }
    
    /**
     * Set the callable that will be used to generate random strings.
     *
     * @param callable|null $factory
     *
     * @return void
     */
    public static function createRandomStringsUsing(callable $factory = null
    ): void {
        static::$randomStringFactory = $factory;
    }
    
    /**
     * Indicate that random strings should be created normally and not using a
     * custom factory.
     *
     * @return void
     */
    public static function createRandomStringsNormally(): void
    {
        static::$randomStringFactory = null;
    }
    
    /**
     * Repeat the given string.
     *
     * @param string $string
     * @param int    $times
     *
     * @return string
     */
    public static function repeat(string $string, int $times): string
    {
        return str_repeat($string, $times);
    }
    
    /**
     * Replace a given value in the string sequentially with an array.
     *
     * @param string           $search
     * @param iterable<string> $replace
     * @param string           $subject
     *
     * @return string
     */
    public static function replaceArray(string $search, iterable $replace,
        string $subject
    ): string {
        if ( ! is_array($replace)) {
            if ($replace instanceof Traversable) {
                $replace = collect($replace)->all();
            }
        }
        $segments = explode($search, $subject);
        $result   = array_shift($segments);
        foreach ($segments as $segment) {
            $result .= self::toStringOr(array_shift($replace) ?? $search)
                . $segment;
        }
        
        return $result;
    }
    
    /**
     * Convert the given value to a string or return the given fallback on
     * failure.
     *
     * @param mixed $value
     *
     * @return string
     */
    private static function toStringOr(mixed $value): string
    {
        try {
            return (string)$value;
        } catch (Throwable $e) {
            return $fallback;
        }
    }
    
    /**
     * Replace the first occurrence of the given value if it appears at the
     * start of the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function replaceStart(string $search, string $replace,
        string $subject
    ): string {
        
        if ($search === '') {
            return $subject;
        }
        if (static::startsWith($subject, $search)) {
            return static::replaceFirst($search, $replace, $subject);
        }
        
        return $subject;
    }
    
    /**
     * Replace the first occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function replaceFirst(string $search, string $replace,
        string $subject
    ): string {
        
        if ($search === '') {
            return $subject;
        }
        $position = strpos($subject, $search);
        if ($position !== false) {
            return substr_replace(
                $subject, $replace, $position, strlen($search)
            );
        }
        
        return $subject;
    }
    
    /**
     * Replace the last occurrence of a given value if it appears at the end of
     * the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function replaceEnd(string $search, string $replace,
        string $subject
    ): string {
        
        if ($search === '') {
            return $subject;
        }
        if (static::endsWith($subject, $search)) {
            return static::replaceLast($search, $replace, $subject);
        }
        
        return $subject;
    }
    
    /**
     * Replace the last occurrence of a given value in the string.
     *
     * @param string $search
     * @param string $replace
     * @param string $subject
     *
     * @return string
     */
    public static function replaceLast(string $search, string $replace,
        string $subject
    ): string {
        
        if ($search === '') {
            return $subject;
        }
        $position = strrpos($subject, $search);
        if ($position !== false) {
            return substr_replace(
                $subject, $replace, $position, strlen($search)
            );
        }
        
        return $subject;
    }
    
    /**
     * Replace the patterns matching the given regular expression.
     *
     * @param array|string    $pattern
     * @param string|\Closure $replace
     * @param array|string    $subject
     * @param int             $limit
     *
     * @return string|string[]|null
     */
    public static function replaceMatches(array|string $pattern,
        string|Closure $replace, array|string $subject, int $limit = -1
    ): array|string|null {
        if ($replace instanceof Closure) {
            return preg_replace_callback($pattern, $replace, $subject, $limit);
        }
        
        return preg_replace($pattern, $replace, $subject, $limit);
    }
    
    /**
     * Remove any occurrence of the given string in the subject.
     *
     * @param iterable|string $search
     * @param iterable|string $subject
     * @param bool            $caseSensitive
     *
     * @return string
     */
    public static function remove(iterable|string $search,
        iterable|string $subject, bool $caseSensitive = true
    ): string {
        if ($search instanceof Traversable) {
            $search = collect($search)->all();
        }
        
        return $caseSensitive
            ? str_replace($search, '', $subject)
            : str_ireplace($search, '', $subject);
    }
    
    /**
     * Reverse the given string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function reverse(string $value): string
    {
        return implode(array_reverse(mb_str_split($value)));
    }
    
    /**
     * Begin a string with a single instance of a given value.
     *
     * @param string $value
     * @param string $prefix
     *
     * @return string
     */
    public static function start(string $value, string $prefix): string
    {
        $quoted = preg_quote($prefix, '/');
        
        return $prefix . preg_replace('/^(?:' . $quoted . ')+/u', '', $value);
    }
    
    /**
     * Convert the given string to proper case.
     *
     * @param string $value
     *
     * @return string
     */
    public static function title(string $value): string
    {
        return mb_convert_case($value, MB_CASE_TITLE, 'UTF-8');
    }
    
    /**
     * Convert the given string to proper case for each word.
     *
     * @param string $value
     *
     * @return string
     */
    public static function headline(string $value): string
    {
        $parts     = explode(' ', $value);
        $parts     = count($parts) > 1
            ? array_map([static::class, 'title'], $parts)
            : array_map([static::class, 'title'],
                static::ucsplit(implode('_', $parts)));
        $collapsed = static::replace(['-', '_', ' '], '_', implode('_', $parts)
        );
        
        return implode(' ', array_filter(explode('_', $collapsed)));
    }
    
    /**
     * Split a string into pieces by uppercase characters.
     *
     * @param string $string
     *
     * @return string[]
     */
    public static function ucsplit(string $string): array
    {
        return preg_split('/(?=\p{Lu})/u', $string, -1, PREG_SPLIT_NO_EMPTY);
    }
    
    /**
     * Convert the given string to APA-style title case.
     *
     * See:
     * https://apastyle.apa.org/style-grammar-guidelines/capitalization/title-case
     *
     * @param string $value
     *
     * @return string
     */
    public static function apa(string $value): string
    {
        if (trim($value) === '') {
            return $value;
        }
        $minorWords     = [
            'and', 'as', 'but', 'for', 'if', 'nor', 'or', 'so', 'yet', 'a',
            'an',
            'the', 'at', 'by', 'for', 'in', 'of', 'off', 'on', 'per', 'to',
            'up', 'via',
        ];
        $endPunctuation = ['.', '!', '?', ':', '—', ','];
        $words          = preg_split('/\s+/', $value, -1, PREG_SPLIT_NO_EMPTY);
        $words[0]       = ucfirst(mb_strtolower($words[0]));
        for ($i = 0; $i < count($words); $i++) {
            $lowercaseWord = mb_strtolower($words[$i]);
            if (str_contains($lowercaseWord, '-')) {
                $hyphenatedWords = explode('-', $lowercaseWord);
                $hyphenatedWords = array_map(
                    function ($part) use ($minorWords) {
                        return (in_array($part, $minorWords)
                            && mb_strlen($part) <= 3) ? $part : ucfirst($part);
                    }, $hyphenatedWords
                );
                $words[$i]       = implode('-', $hyphenatedWords);
            } else {
                if (in_array($lowercaseWord, $minorWords)
                    && mb_strlen(
                        $lowercaseWord
                    ) <= 3
                    &&
                    ! ($i === 0
                        || in_array(
                            mb_substr($words[$i - 1], -1), $endPunctuation
                        ))
                ) {
                    $words[$i] = $lowercaseWord;
                } else {
                    $words[$i] = ucfirst($lowercaseWord);
                }
            }
        }
        
        return implode(' ', $words);
    }
    
    /**
     * Get the singular form of an English word.
     *
     * @param string $value
     *
     * @return string
     */
    public static function singular(string $value): string
    {
        return Pluralizer::singular($value);
    }
    
    /**
     * Generate a URL friendly "slug" from a given string.
     *
     * @param string                $title
     * @param string                $separator
     * @param string|null           $language
     * @param array<string, string> $dictionary
     *
     * @return string
     */
    public static function slug(string $title, string $separator = '-',
        ?string $language = 'en', array $dictionary = ['@' => 'at']
    ): string {
        $title = $language ? static::ascii($title, $language) : $title;
        // Convert all dashes/underscores into separator
        $flip  = $separator === '-' ? '_' : '-';
        $title = preg_replace(
            '![' . preg_quote($flip) . ']+!u', $separator, $title
        );
        // Replace dictionary words
        foreach ($dictionary as $key => $value) {
            $dictionary[$key] = $separator . $value . $separator;
        }
        $title = str_replace(
            array_keys($dictionary), array_values($dictionary), $title
        );
        // Remove all characters that are not the separator, letters, numbers, or whitespace
        $title = preg_replace(
            '![^' . preg_quote($separator) . '\pL\pN\s]+!u', '',
            static::lower($title)
        );
        // Replace all separator characters and whitespace by a single separator
        $title = preg_replace(
            '![' . preg_quote($separator) . '\s]+!u', $separator, $title
        );
        
        return trim($title, $separator);
    }
    
    /**
     * Transliterate a UTF-8 value to ASCII.
     *
     * @param string $value
     * @param string $language
     *
     * @return string
     */
    public static function ascii(string $value, string $language = 'en'): string
    {
        return ASCII::to_ascii($value, $language);
    }
    
    /**
     * Remove all "extra" blank space from the given string.
     *
     * @param string $value
     *
     * @return string
     */
    public static function squish(string $value): string
    {
        return preg_replace(
            '~(\s|\x{3164}|\x{1160})+~u', ' ',
            preg_replace('~^[\s\x{FEFF}]+|[\s\x{FEFF}]+$~u', '', $value)
        );
    }
    
    /**
     * Replace text within a portion of a string.
     *
     * @param string|string[] $string
     * @param string|string[] $replace
     * @param int|int[]       $offset
     * @param int|int[]|null  $length
     *
     * @return string|string[]
     */
    public static function substrReplace(array|string $string,
        array|string $replace, array|int $offset = 0, array|int $length = null
    ): array|string {
        if ($length === null) {
            $length = strlen($string);
        }
        
        return substr_replace($string, $replace, $offset, $length);
    }
    
    /**
     * Swap multiple keywords in a string with other keywords.
     *
     * @param array  $map
     * @param string $subject
     *
     * @return string
     */
    public static function swap(array $map, string $subject): string
    {
        return strtr($subject, $map);
    }
    
    /**
     * Take the first or last {$limit} characters of a string.
     *
     * @param string $string
     * @param int    $limit
     *
     * @return string
     */
    public static function take(string $string, int $limit): string
    {
        if ($limit < 0) {
            return static::substr($string, $limit);
        }
        
        return static::substr($string, 0, $limit);
    }
    
    /**
     * Convert the given string to Base64 encoding.
     *
     * @param string $string
     *
     * @return string
     */
    public static function toBase64(string $string): string
    {
        return base64_encode($string);
    }
    
    /**
     * Decode the given Base64 encoded string.
     *
     * @param string $string
     * @param bool   $strict
     *
     * @return string|false
     */
    public static function fromBase64(string $string, bool $strict = false
    ): false|string {
        return base64_decode($string, $strict);
    }
    
    /**
     * Make a string's first character lowercase.
     *
     * @param string $string
     *
     * @return string
     */
    public static function lcfirst(string $string): string
    {
        return static::lower(static::substr($string, 0, 1)) . static::substr(
                $string, 1
            );
    }
    
    /**
     * Get the number of words a string contains.
     *
     * @param string      $string
     * @param string|null $characters
     *
     * @return int
     */
    public static function wordCount(string $string, string $characters = null
    ): int {
        return str_word_count($string, 0, $characters);
    }
    
    /**
     * Wrap a string to a given number of characters.
     *
     * @param string $string
     * @param int    $characters
     * @param string $break
     * @param bool   $cutLongWords
     *
     * @return string
     */
    public static function wordWrap(string $string, int $characters = 75,
        string $break = "\n", bool $cutLongWords = false
    ): string {
        return wordwrap($string, $characters, $break, $cutLongWords);
    }
    
    /**
     * Generate a time-ordered UUID.
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public static function orderedUuid(): UuidInterface
    {
        if (static::$uuidFactory) {
            return call_user_func(static::$uuidFactory);
        }
        $factory = new UuidFactory;
        $factory->setRandomGenerator(
            new CombGenerator(
                $factory->getRandomGenerator(),
                $factory->getNumberConverter()
            )
        );
        $factory->setCodec(
            new TimestampFirstCombCodec(
                $factory->getUuidBuilder()
            )
        );
        
        return $factory->uuid4();
    }
    
    /**
     * Set the sequence that will be used to generate UUIDs.
     *
     * @param array         $sequence
     * @param callable|null $whenMissing
     *
     * @return void
     */
    public static function createUuidsUsingSequence(array $sequence,
        callable $whenMissing = null
    ): void {
        $next        = 0;
        $whenMissing ??= function () use (&$next) {
            $factoryCache        = static::$uuidFactory;
            static::$uuidFactory = null;
            $uuid                = static::uuid();
            static::$uuidFactory = $factoryCache;
            $next++;
            
            return $uuid;
        };
        static::createUuidsUsing(
            function () use (&$next, $sequence, $whenMissing) {
                if (array_key_exists($next, $sequence)) {
                    return $sequence[$next++];
                }
                
                return $whenMissing();
            }
        );
    }
    
    /**
     * Generate a UUID (version 4).
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public static function uuid(): UuidInterface
    {
        return static::$uuidFactory
            ? call_user_func(static::$uuidFactory)
            : Uuid::uuid4();
    }
    
    /**
     * Set the callable that will be used to generate UUIDs.
     *
     * @param callable|null $factory
     *
     * @return void
     */
    public static function createUuidsUsing(callable $factory = null): void
    {
        static::$uuidFactory = $factory;
    }
    
    /**
     * Always return the same UUID when generating new UUIDs.
     *
     * @param \Closure|null $callback
     *
     * @return \Ramsey\Uuid\UuidInterface
     */
    public static function freezeUuids(Closure $callback = null): UuidInterface
    {
        $uuid = Str::uuid();
        Str::createUuidsUsing(fn() => $uuid);
        if ($callback !== null) {
            try {
                $callback($uuid);
            } finally {
                Str::createUuidsNormally();
            }
        }
        
        return $uuid;
    }
    
    /**
     * Indicate that UUIDs should be created normally and not using a custom
     * factory.
     *
     * @return void
     */
    public static function createUuidsNormally(): void
    {
        static::$uuidFactory = null;
    }
    
    /**
     * Set the sequence that will be used to generate ULIDs.
     *
     * @param array         $sequence
     * @param callable|null $whenMissing
     *
     * @return void
     */
    public static function createUlidsUsingSequence(array $sequence,
        callable $whenMissing = null
    ): void {
        $next        = 0;
        $whenMissing ??= function () use (&$next) {
            $factoryCache        = static::$ulidFactory;
            static::$ulidFactory = null;
            $ulid                = static::ulid();
            static::$ulidFactory = $factoryCache;
            $next++;
            
            return $ulid;
        };
        static::createUlidsUsing(
            function () use (&$next, $sequence, $whenMissing) {
                if (array_key_exists($next, $sequence)) {
                    return $sequence[$next++];
                }
                
                return $whenMissing();
            }
        );
    }
    
    /**
     * Generate a ULID.
     *
     * @param \DateTimeInterface|null $time
     *
     * @return \Symfony\Component\Uid\Ulid
     */
    public static function ulid(DateTimeInterface $time = null): Ulid
    {
        if (static::$ulidFactory) {
            return call_user_func(static::$ulidFactory);
        }
        if ($time === null) {
            return new Ulid();
        }
        
        return new Ulid(Ulid::generate($time));
    }
    
    /**
     * Set the callable that will be used to generate ULIDs.
     *
     * @param callable|null $factory
     *
     * @return void
     */
    public static function createUlidsUsing(callable $factory = null): void
    {
        static::$ulidFactory = $factory;
    }
    
    /**
     * Always return the same ULID when generating new ULIDs.
     *
     * @param Closure|null $callback
     *
     * @return Ulid
     */
    public static function freezeUlids(Closure $callback = null): Ulid
    {
        $ulid = Str::ulid();
        Str::createUlidsUsing(fn() => $ulid);
        if ($callback !== null) {
            try {
                $callback($ulid);
            } finally {
                Str::createUlidsNormally();
            }
        }
        
        return $ulid;
    }
    
    /**
     * Indicate that ULIDs should be created normally and not using a custom
     * factory.
     *
     * @return void
     */
    public static function createUlidsNormally(): void
    {
        static::$ulidFactory = null;
    }
    
    /**
     * Remove all strings from the casing caches.
     *
     * @return void
     */
    public static function flushCache(): void
    {
        static::$snakeCache  = [];
        static::$camelCache  = [];
        static::$studlyCache = [];
    }
}
