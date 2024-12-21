<?php

namespace MythicalClient\Chat\interface;

class UserActivitiesTypes {
    public static string $login = "auth:login";
    public static string $register = "auth:register";
    
    /**
     * Get all types
     * 
     * @return array All types
     */
    public static function getTypes(): array {
        return [
            self::$login,
            self::$register
        ];
    }
}