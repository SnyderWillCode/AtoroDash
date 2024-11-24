<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * MIT License
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

try {
    if (file_exists(APP_DIR . 'storage/packages')) {
        require APP_DIR . 'storage/packages/autoload.php';
    } else {
        throw new Exception('Packages not installed looked at this path: ' . APP_DIR . 'storage/packages');
    }
} catch (Exception $e) {
    echo $e->getMessage();
    echo "\n";
    exit;
}

ini_set('expose_php', 'off');
header_remove('X-Powered-By');
header_remove('Server');

if (!is_writable(__DIR__)) {
    exit('Please make sure the root directory is writable.');
}

if (!is_writable(__DIR__ . '/../storage')) {
    exit('Please make sure the storage directory is writable.');
}

if (!extension_loaded('mysqli')) {
    exit('MySQL extension is not installed!');
}

if (!extension_loaded('curl')) {
    exit('Curl extension is not installed!');
}

if (!extension_loaded('gd')) {
    exit('GD extension is not installed!');
}

if (!extension_loaded('mbstring')) {
    exit('MBString extension is not installed!');
}

if (!extension_loaded('openssl')) {
    exit('OpenSSL extension is not installed!');
}

if (!extension_loaded('zip')) {
    exit('Zip extension is not installed!');
}

if (!extension_loaded('bcmath')) {
    exit('Bcmath extension is not installed!');
}

if (!extension_loaded('json')) {
    exit('JSON extension is not installed!');
}

if (!extension_loaded('sodium')) {
    exit('sodium extension is not installed!');
}
if (version_compare(PHP_VERSION, '8.1.0', '<')) {
    exit('This application requires at least PHP 8.1.0');
}
