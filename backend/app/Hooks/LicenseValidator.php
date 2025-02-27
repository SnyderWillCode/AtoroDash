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

namespace MythicalClient\Hooks;

use GuzzleHttp\Client;
use MythicalClient\App;
use GuzzleHttp\Exception\GuzzleException;

class LicenseValidator
{
    private const PRODUCT_ID = 1;
    private const API_URL = 'https://activation.mythical.systems';
    private const CACHE_DURATION = 1800; // 30 minutes
    private const CACHE_DIR = APP_CACHE_DIR . '/other';
    private string $licenseKey;
    private string $version;
    private string $cacheFile;
    private Client $httpClient;

    public function __construct(?string $licenseKey, string $version = '1.0.0')
    {
        $this->licenseKey = $licenseKey ?? '';
        $this->version = $version;
        $this->cacheFile = self::CACHE_DIR . '/license_validation.json';
        $this->httpClient = new Client([
            'base_uri' => self::API_URL,
            'timeout' => 10.0,
            'verify' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
            ],
        ]);

        $this->ensureCacheDirectory();

        App::getInstance(true)->getLogger()->debug('License validator initialized');
    }

    private function ensureCacheDirectory(): void
    {
        if (!is_dir(self::CACHE_DIR)) {
            $created = @mkdir(self::CACHE_DIR, 0755, true);
            if (!$created) {
                App::getInstance(true)->getLogger()->error('Failed to create cache directory: ' . self::CACHE_DIR);
            }
        }

        // Verify directory is writable
        if (!is_writable(self::CACHE_DIR)) {
            App::getInstance(true)->getLogger()->error('Cache directory is not writable: ' . self::CACHE_DIR);
        }
    }

    public function validate(): bool
    {
        try {
            // First check if we have valid cache
            $cachedResult = $this->checkCache();
            if ($cachedResult !== null) {
                App::getInstance(true)->getLogger()->debug('Using cached license validation result');
                return $cachedResult;
            }

            // No valid cache, perform actual validation
            $response = $this->httpClient->post(self::API_URL . '/api/v1/validate', [
                'json' => [
                    'licenseKey' => $this->licenseKey,
                    'productId' => self::PRODUCT_ID,
                    'productVersion' => $this->version,
                    'hwid' => $this->getHWID(),
                ],
            ]);

            $responseData = json_decode($response->getBody()->getContents(), true);
            $isValid = $response->getStatusCode() === 200 && isset($responseData['valid']) && $responseData['valid'] === true;

            // Cache the result
            $this->setCache($isValid);
            
            App::getInstance(true)->getLogger()->debug('License validation result: ' . ($isValid ? 'valid' : 'invalid'));
            return $isValid;

        } catch (GuzzleException $e) {
            App::getInstance(true)->getLogger()->error('License validation request failed: ' . $e->getMessage());
            return false;
        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error('Unexpected error during license validation: ' . $e->getMessage());
            return false;
        }
    }

    private function checkCache(): ?bool
    {
        try {
            if (!file_exists($this->cacheFile)) {
                return null;
            }

            $cacheContent = @file_get_contents($this->cacheFile);
            if ($cacheContent === false) {
                App::getInstance(true)->getLogger()->warning('Failed to read license cache file');
                return null;
            }

            $cacheData = @json_decode($cacheContent, true);
            if (!$this->isValidCacheData($cacheData)) {
                App::getInstance(true)->getLogger()->warning('Invalid cache data format');
                @unlink($this->cacheFile);
                return null;
            }

            // Check if cache has expired
            if (time() - $cacheData['timestamp'] > self::CACHE_DURATION) {
                App::getInstance(true)->getLogger()->debug('License cache has expired');
                @unlink($this->cacheFile);
                return null;
            }

            return $cacheData['valid'];

        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error('Error checking license cache: ' . $e->getMessage());
            return null;
        }
    }

    private function setCache(bool $isValid): void
    {
        try {
            $cacheData = [
                'timestamp' => time(),
                'valid' => $isValid,
                'version' => $this->version,
                'hwid' => $this->getHWID(),
            ];

            $written = @file_put_contents(
                $this->cacheFile, 
                json_encode($cacheData), 
                LOCK_EX
            );

            if ($written === false) {
                throw new \RuntimeException('Failed to write to cache file');
            }

            // Verify the cache was written correctly
            clearstatcache(true, $this->cacheFile);
            if (!file_exists($this->cacheFile)) {
                throw new \RuntimeException('Cache file does not exist after writing');
            }

            App::getInstance(true)->getLogger()->debug('License cache updated successfully');

        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error('Failed to set license cache: ' . $e->getMessage());
        }
    }

    private function isValidCacheData(?array $data): bool
    {
        return $data && isset($data['timestamp'], $data['valid']);
    }

    private function getHWID(): string
    {
        try {
            $hwid = '';

            // Get system-specific identifiers
            $identifiers = $this->getSystemIdentifiers();
            $hwid = implode('', array_filter($identifiers));

            // Fallback if no hardware info could be gathered
            if (empty($hwid)) {
                $hwid = $this->getFallbackIdentifier();
            }

            $finalHWID = hash('sha256', $hwid ?: uniqid('fallback_', true));
            App::getInstance(true)->getLogger()->debug('HWID generated successfully');

            return $finalHWID;

        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error('HWID Generation Error: ' . $e->getMessage(), false);

            return hash('sha256', uniqid('emergency_fallback_', true));
        }
    }

    private function getSystemIdentifiers(): array
    {
        $identifiers = [];

        if (PHP_OS === 'Linux') {
            App::getInstance(true)->getLogger()->debug('Generating HWID for Linux');
            $identifiers = $this->getLinuxIdentifiers();
        } else {
            App::getInstance(true)->getLogger()->debug('Generating HWID for Windows');
            $identifiers = $this->getWindowsIdentifiers();
        }

        // Add common identifiers
        $identifiers['hostname'] = @gethostname();

        return array_filter($identifiers);
    }

    private function getLinuxIdentifiers(): array
    {
        $identifiers = [];

        // CPU Info
        if (is_readable('/proc/cpuinfo')) {
            $cpuinfo = @file_get_contents('/proc/cpuinfo');
            if ($cpuinfo !== false) {
                if (preg_match('/^Serial\s*: (.+)$/m', $cpuinfo, $matches)) {
                    $identifiers['cpu_serial'] = $matches[1];
                } elseif (preg_match('/^Hardware\s*: (.+)$/m', $cpuinfo, $matches)) {
                    $identifiers['cpu_hardware'] = $matches[1];
                }
            }
        }

        // MAC Address
        $methods = [
            'cat /sys/class/net/$(ls /sys/class/net | head -n 1)/address',
            "ifconfig -a | grep -Po 'HWaddr \K.*$'",
            "ip link | grep -Po 'ether \K.*$'",
        ];

        foreach ($methods as $method) {
            $mac = @shell_exec($method);
            if ($mac) {
                $identifiers['mac'] = preg_replace('/[^A-Fa-f0-9]/', '', $mac);
                break;
            }
        }

        return $identifiers;
    }

    private function getWindowsIdentifiers(): array
    {
        $identifiers = [];

        // CPU Info
        $wmic = @shell_exec('wmic cpu get ProcessorId');
        if ($wmic && preg_match('/([A-Z0-9]+)/', $wmic, $matches)) {
            $identifiers['cpu'] = $matches[1];
        }

        // MAC Address
        $mac = @shell_exec('getmac /NH /FO CSV | findstr /R "[0-9A-Fa-f][0-9A-Fa-f]"');
        if ($mac) {
            $identifiers['mac'] = preg_replace('/[^A-Fa-f0-9]/', '', $mac);
        }

        return $identifiers;
    }

    private function getFallbackIdentifier(): string
    {
        return implode('', [
            php_uname(),
            $_SERVER['SERVER_ADDR'] ?? '',
            $_SERVER['SERVER_NAME'] ?? '',
            $_SERVER['SERVER_SOFTWARE'] ?? '',
        ]);
    }
}
