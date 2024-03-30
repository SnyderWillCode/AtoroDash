<?php

namespace MythicalDash\Kernel;

use Exception;

class Kernel
{
    /**
     * Check if a directory exists and has the right permissions.
     *
     * @param string $dir The directory path
     * @return string
     *         - "OK" if everything is okay
     *         - "NOEX" if directory does not exist
     *         - "NOPERM" if directory exists but does not have the required permissions
     *         - "*" if an exception occurs (critical)
     */
    public static function checkDir(string $dir): string
    {
        if (!is_dir($dir)) {
            return "NOEX"; // Directory does not exist
        }

        if (!is_readable($dir) || !is_writable($dir) || !is_executable($dir)) {
            return "NOPERM"; // Directory exists but does not have required permissions
        }

        return "OK"; // Everything is okay
    }
    /**
     * Check if the render dirs are writable
     * 
     * @param string $dir_cache 
     * @param string $dir_config
     * @param string $dir_compile
     * @param string $dir_template
     * 
     * @return bool
     */
    public static function checkRenderDirs(string $dir_cache, string $dir_config, string $dir_compile, string $dir_template): bool
    {
        if (
            self::checkDir($dir_cache) == "OK" &&
            self::checkDir($dir_config) == "OK" &&
            self::checkDir($dir_compile) == "OK" &&
            self::checkDir($dir_template) == "OK"
        ) {
            return true;
        } else {
            return false;
        }
    }
}