<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * Breaking any of the following rules will result in a permanent ban from the MythicalSystems community and all of its services.
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
    public const SUPPORT_PIN = 'support_pin';
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
            self::SUPPORT_PIN,
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
