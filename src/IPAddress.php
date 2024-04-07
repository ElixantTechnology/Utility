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

use Darsyn\IP\Version\IPv4;

/**
 * IPAddress Class
 * Created Apr 07, 2024.
 *
 * The IPAddress class is a utility class that provides methods for detecting
 * the client's IP address and creating an instance of the IPAddress class.
 *
 * The IPAddress class extends the IPv4 class from the Darsyn IP library.
 *
 * @package         Elixant\Utility::IPAddress
 * @class           IPAddress
 * @version         GitHub: $Id$
 * @copyright       2024 (c) Elixant Corporation.
 * @license         MIT License
 * @author          Alexander M. Schmautz <a.schmautz91@gmail.com>
 * @since           1.0.0
 */
final class IPAddress extends IPv4
{
    /**
     * Headers that may contain the client's IP address.
     *
     * @var array|string[] IP Headers
     */
    private static array $ip_headers
        = [
            'HTTP_CF_CONNECTING_IP',
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'HTTP_X_REAL_IP',
            'REMOTE_ADDR'
        ];
    
    /**
     * Detect the Client's IP Address and create an instance of the IPAddress
     * class.
     *
     * @return \Elixant\Utility\IPAddress|\Darsyn\IP\Version\IPv4
     *
     * @throws \Darsyn\IP\Exception\InvalidIpAddressException
     * @throws \Darsyn\IP\Exception\WrongVersionException
     */
    public static function detect(): IPAddress|IPv4
    {
        $address = IPAddress::detectClientAddress();
        
        return IPAddress::factory($address);
    }
    
    /**
     * Detect the Client's IP Address.
     *
     * @return string The IP Address
     */
    private static function detectClientAddress(): string
    {
        foreach (IPAddress::$ip_headers as $header) {
            if (isset($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip  = trim(end($ips));
                $ip  = filter_var($ip, FILTER_VALIDATE_IP);
                if (false !== $ip) {
                    return $ip;
                }
            }
        }
        
        return $_SERVER['REMOTE_ADDR'];
    }
}
