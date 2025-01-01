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

namespace MythicalClient\Chat;

use MythicalClient\App;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\CloudFlare\CloudFlareRealIP;

class Session extends Database
{
    public App $app;
    public string $SESSION_KEY;

    public function __construct(App $app)
    {
        if (isset($_COOKIE['user_token']) && !$_COOKIE['user_token'] == '') {
            if (User::exists(UserColumns::ACCOUNT_TOKEN, $_COOKIE['user_token'])) {
                try {
                    header('Access-Control-Allow-Origin: *');
                    header('Access-Control-Allow-Methods: GET, POST, OPTIONS');
                    header('Access-Control-Allow-Headers: Content-Type, Authorization');
                    $this->app = $app;
                    $this->SESSION_KEY = $_COOKIE['user_token'];
                    $this->updateLastSeen();
                    if ($this->getInfo(UserColumns::TWO_FA_BLOCKED, false) == 'true') {
                        $app->Unauthorized('Please verify 2fa to access this endpoint.', ['error_code' => 'TW0_FA_BLOCKED']);
                    }
                } catch (\Exception) {
                    $app->Unauthorized('Bad Request', ['error_code' => 'INVALID_ACCOUNT_TOKEN']);
                }
            } else {
                $app->Unauthorized('Login info provided are invalid!', ['error_code' => 'INVALID_ACCOUNT_TOKEN']);
            }
        } else {
            $app->Unauthorized('Please login to access this endpoint.', ['error_code' => 'MISSING_ACCOUNT_TOKEN']);
        }
    }

    public function __destruct()
    {
        unset($this->app);
    }

    public function getInfo(string|UserColumns $info, bool $encrypted): string
    {
        if (!in_array($info, UserColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }

        return User::getInfo($this->SESSION_KEY, $info, $encrypted);
    }

    public function setInfo(string|UserColumns $info, string $value, bool $encrypted): void
    {
        if (!in_array($info, UserColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }
        User::updateInfo($this->SESSION_KEY, $info, $value, $encrypted);
    }

    public function updateLastSeen(): void
    {
        try {
            $con = self::getPdoConnection();
            $ip = CloudFlareRealIP::getRealIP();
            $this->app->getLogger()->info('Updating last seen for ' . $this->SESSION_KEY . ' with IP: ' . $ip);
            $con->exec('UPDATE ' . User::TABLE_NAME . ' SET last_seen = NOW() WHERE token = "' . $this->SESSION_KEY . '";');
            $con->exec('UPDATE ' . User::TABLE_NAME . ' SET last_ip = "' . $ip . '" WHERE token = "' . $this->SESSION_KEY . '";');
        } catch (\Exception $e) {
            $this->app->getLogger()->error('Failed to update last seen: ' . $e->getMessage());
        }
    }

    /**
     * Check if the user has access to the admin panel.
     *
     * @return bool true if the user has access to the admin panel, otherwise false
     */
    public function canAccessAdmin(): bool
    {
        if ($this->getInfo(UserColumns::ROLE_ID, false) == '1' || $this->getInfo(UserColumns::ROLE_ID, false) == '2') {
            return false;
        }

        return true;

    }
}
