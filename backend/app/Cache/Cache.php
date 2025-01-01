<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * 2021-2025 (c) All rights reserved
 *
 * MIT License
 *
 * (c) MythicalSystems - All rights reserved
 * (c) NaysKutzu - All rights reserved
 * (c) Cassian Gherman- All rights reserved
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

namespace MythicalClient\Cache;

/**
 * Class Cache.
 *
 * Provides simple file-based caching functionality.
 */
class Cache
{
    protected static $cacheDir = APP_CACHE_DIR . '/other';

    /**
     * Stores a value in the cache with a specified expiration time.
     *
     * @param string $key the cache key used to reference the data
     * @param mixed $value the data to be cached
     * @param int $minutes number of minutes before the data expires
     *
     * @return void
     */
    public static function put($key, $value, $minutes = 60)
    {
        if (!is_dir(self::$cacheDir)) {
            mkdir(self::$cacheDir, 0777, true);
        }
        $filename = self::$cacheDir . '/' . md5($key);
        $data = [
            'expires' => time() + ($minutes * 60),
            'value' => $value,
        ];
        file_put_contents($filename, serialize($data));
    }

    /**
     * Retrieves a value from the cache by its key.
     *
     * @param string $key the cache key for the stored data
     *
     * @return mixed|null returns the stored data or null if not found or expired
     */
    public static function get($key)
    {
        $filename = self::$cacheDir . '/' . md5($key);
        if (!file_exists($filename)) {
            return null;
        }
        $data = unserialize(file_get_contents($filename));
        if (time() > $data['expires']) {
            unlink($filename);

            return null;
        }

        return $data['value'];
    }

    /**
     * Removes a cached entry by its key.
     *
     * @param string $key the cache key to remove
     *
     * @return void
     */
    public static function forget($key)
    {
        $filename = self::$cacheDir . '/' . md5($key);
        if (file_exists($filename)) {
            unlink($filename);
        }
    }

    /**
     * Clears all entries in the cache directory.
     *
     * @return void
     */
    public static function clear()
    {
        $files = glob(self::$cacheDir . '/*');
        foreach ($files as $file) {
            if (is_file($file)) {
                unlink($file);
            }
        }
    }

    /**
     * Checks if a valid cached entry exists for the specified key.
     *
     * @param string $key the cache key to check
     *
     * @return bool true if a valid cached entry exists, otherwise false
     */
    public static function exists($key)
    {
        $filename = self::$cacheDir . '/' . md5($key);
        if (!is_file($filename)) {
            return false;
        }
        $data = @unserialize(file_get_contents($filename));
        if (!is_array($data) || !isset($data['expires'])) {
            return false;
        }

        return time() <= $data['expires'];
    }

    /**
     * Stores JSON data in the cache with a specified expiration time.
     *
     * @param string $key the cache key used to reference the JSON data
     * @param mixed $json the JSON data to be cached
     * @param int $minutes number of minutes before the data expires
     *
     * @return void
     */
    public static function putJson($key, $json, $minutes = 60)
    {
        self::put($key, $json, $minutes);
    }

    /**
     * Retrieves a previously stored JSON data by its key.
     *
     * @param string $key the cache key for the JSON data
     *
     * @return mixed|null returns the JSON data or null if not found or expired
     */
    public static function getJson($key)
    {
        return self::get($key);
    }
}
