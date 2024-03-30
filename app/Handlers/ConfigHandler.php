<?php

namespace MythicalDash\Handlers;

use MythicalSystems\Helpers\ConfigHelper;
use MythicalDash\Kernel\Kernel;

class ConfigHandler
{
    private static $configPath = __DIR__ . "/../../settings.json";
    /**
     * Get something from the config file!
     * 
     * @param string $section The section of the config
     * @param string $key The value you want to get from config
     * 
     * @return string|null
     */
    public static function get(string $section, string $key): string|null
    {
        $config = new ConfigHelper(self::$configPath);
        return ($config->get($section, $key));
    }

    /**
     * Set something inside the config file!
     * 
     * @param string $section The section of the config
     * @param string $key The value you want to get from config
     * @param string $value The value you want to set
     *
     * @return void
     */
    public static function set(string $section, string $key, string $value): void
    {
        $config = new ConfigHelper(self::$configPath);
        $config->set($section, $key, $value);
    }
    
    /**
     * !Deprecated!
     * 
     * Please use set instead!
     * 
     * !Deprecated!
     * 
     * @return void
     */
    public static function add(string $section, string $key, string $value): void
    {
        $config = new ConfigHelper(self::$configPath);
        $config->add($section, $key, $value);
    }

    /**
     * Remove a value from the settings file
     * 
     * @param string $section The section of the config
     * @param string $key The value you want to get from config
     * 
     * @return void
     */
    public static function remove(string $section, string $key) : void {
        $config = new ConfigHelper(self::$configPath);
        $config->remove($section, $key);
    }
}