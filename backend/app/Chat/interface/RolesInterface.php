<?php
namespace MythicalClient\Chat\interface;
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

class RolesInterface
{
    public static string $DEFAULT = 'default';
    public static string $VIP = 'vip';
    public static string $SUPPORTBUDDY = 'supportbuddy';
    public static string $SUPPORT = 'support';
    public static string $SUPPORTLVL3 = 'supportlvl3';
    public static string $SUPPORTLVL4 = 'supportlvl4';
    public static string $ADMIN = 'admin';
    public static string $ADMINISTRATOR = 'administrator';

	public static function getRoles(): array
	{
		return [
			self::$DEFAULT,
			self::$VIP,
			self::$SUPPORTBUDDY,
			self::$SUPPORT,
			self::$SUPPORTLVL3,
			self::$SUPPORTLVL4,
			self::$ADMIN,
			self::$ADMINISTRATOR,
		];
	}
}
