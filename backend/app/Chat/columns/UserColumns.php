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

namespace MythicalClient\Chat\columns;

class UserColumns
{
    public const USERNAME = 'username';
    public const PASSWORD = 'password';
    public const EMAIL = 'email';
    public const FIRST_NAME = 'first_name';
    public const LAST_NAME = 'last_name';
    public const AVATAR = 'avatar';
    public const UUID = 'uuid';
    public const ACCOUNT_TOKEN = 'token';
    public const ROLE_ID = 'role';
    public const FIRST_IP = 'first_ip';
    public const LAST_IP = 'last_ip';
    public const BANNED = 'banned';
    public const VERIFIED = 'verified';
    public const TWO_FA_ENABLED = '2fa_enabled';
    public const TWO_FA_KEY = '2fa_key';
    public const TWO_FA_BLOCKED = '2fa_blocked';
    public const DELETED = 'deleted';
    public const LAST_SEEN = 'last_seen';
    public const FIRST_SEEN = 'first_seen';
    public const BACKGROUND = 'background';

    public static function getColumns(): array
    {
        return [
            self::USERNAME,
            self::PASSWORD,
            self::EMAIL,
            self::FIRST_NAME,
            self::LAST_NAME,
            self::AVATAR,
            self::UUID,
            self::ACCOUNT_TOKEN,
            self::ROLE_ID,
            self::FIRST_IP,
            self::LAST_IP,
            self::BANNED,
            self::VERIFIED,
            self::TWO_FA_ENABLED,
            self::TWO_FA_KEY,
            self::TWO_FA_BLOCKED,
            self::DELETED,
            self::LAST_SEEN,
            self::FIRST_SEEN,
            self::BACKGROUND,
        ];
    }
}
