<?php

namespace MythicalClient\Hooks;

use MythicalClient\App;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;

class LicenseValidator {
    private string $licenseKey;
    private string $version;
    private string $cacheFile;
    private Client $httpClient;
    
    private const PRODUCT_ID = 2;
    private const API_URL = "https://activation.mythical.systems";
    private const CACHE_DURATION = 1; // 30 minutes
    private const CACHE_DIR = APP_CACHE_DIR . '/other';

    public function __construct(string|null $licenseKey, string $version = "1.0.0") {
        $this->licenseKey = $licenseKey ?? '';
        $this->version = $version;
        $this->cacheFile = self::CACHE_DIR . '/license_validation.json';
        $this->httpClient = new Client([
            'base_uri' => self::API_URL,
            'timeout' => 10.0,
            'verify' => true,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json'
            ]
        ]);

        // Ensure cache directory exists
        if (!is_dir(self::CACHE_DIR)) {
            mkdir(self::CACHE_DIR, 0755, true);
        }

        App::getInstance(true)->getLogger()->debug("License validator initialized");
    }

    public function validate(): bool {
        try {
            if ($this->checkCache()) {
                App::getInstance(true)->getLogger()->debug("License validation succeeded (cached)");
                return true;
            }

            $response = $this->httpClient->post(self::API_URL.'/api/v1/validate', [
                'json' => [
                    'licenseKey' => $this->licenseKey,
                    'productId' => self::PRODUCT_ID,
                    'productVersion' => $this->version,
                    'hwid' => $this->getHWID()
                ]
            ]);

            if ($response->getStatusCode() === 200) {

                $this->setCache();
                App::getInstance(true)->getLogger()->debug("License validation succeeded: " . $response->getBody());
                return true;
            }

            App::getInstance(true)->getLogger()->warning("License validation failed", false);
            return false;

        } catch (GuzzleException $e) {
            App::getInstance(true)->getLogger()->error("License validation error: " . $e->getMessage(), false);
            return false;
        }
    }

    private function checkCache(): bool {
        try {
            if (!file_exists($this->cacheFile)) {
                App::getInstance(true)->getLogger()->debug("License cache file not found");
                return false;
            }

            $cacheData = @json_decode(file_get_contents($this->cacheFile), true);
            if (!$this->isValidCacheData($cacheData)) {
                App::getInstance(true)->getLogger()->warning("Invalid license cache data format");
                return false;
            }

            if (time() - $cacheData['timestamp'] > self::CACHE_DURATION) {
                @unlink($this->cacheFile);
                App::getInstance(true)->getLogger()->debug("License cache expired");
                return false;
            }

            return $cacheData['valid'];

        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error("License cache check error: " . $e->getMessage(), false);
            return false;
        }
    }

    private function setCache(): void {
        try {
            $cacheData = json_encode([
                'timestamp' => time(),
                'valid' => true
            ]);

            file_put_contents($this->cacheFile, $cacheData, LOCK_EX);
            App::getInstance(true)->getLogger()->debug("License cache updated");
        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error("License cache set error: " . $e->getMessage(), false);
        }
    }

    private function isValidCacheData(?array $data): bool {
        return $data && isset($data['timestamp'], $data['valid']);
    }

    private function getHWID(): string {
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
            App::getInstance(true)->getLogger()->debug("HWID generated successfully");
            return $finalHWID;
            
        } catch (\Throwable $e) {
            App::getInstance(true)->getLogger()->error("HWID Generation Error: " . $e->getMessage(), false);
            return hash('sha256', uniqid('emergency_fallback_', true));
        }
    }

    private function getSystemIdentifiers(): array {
        $identifiers = [];

        if (PHP_OS === 'Linux') {
            App::getInstance(true)->getLogger()->debug("Generating HWID for Linux");
            $identifiers = $this->getLinuxIdentifiers();
        } else {
            App::getInstance(true)->getLogger()->debug("Generating HWID for Windows");
            $identifiers = $this->getWindowsIdentifiers();
        }

        // Add common identifiers
        $identifiers['hostname'] = @gethostname();

        return array_filter($identifiers);
    }

    private function getLinuxIdentifiers(): array {
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
            "cat /sys/class/net/$(ls /sys/class/net | head -n 1)/address",
            "ifconfig -a | grep -Po 'HWaddr \K.*$'",
            "ip link | grep -Po 'ether \K.*$'"
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

    private function getWindowsIdentifiers(): array {
        $identifiers = [];

        // CPU Info
        $wmic = @shell_exec('wmic cpu get ProcessorId');
        if ($wmic && preg_match('/([A-Z0-9]+)/', $wmic, $matches)) {
            $identifiers['cpu'] = $matches[1];
        }

        // MAC Address
        $mac = @shell_exec("getmac /NH /FO CSV | findstr /R \"[0-9A-Fa-f][0-9A-Fa-f]\"");
        if ($mac) {
            $identifiers['mac'] = preg_replace('/[^A-Fa-f0-9]/', '', $mac);
        }

        return $identifiers;
    }

    private function getFallbackIdentifier(): string {
        return implode('', [
            php_uname(),
            $_SERVER['SERVER_ADDR'] ?? '',
            $_SERVER['SERVER_NAME'] ?? '',
            $_SERVER['SERVER_SOFTWARE'] ?? ''
        ]);
    }
}