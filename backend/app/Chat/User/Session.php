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

namespace MythicalClient\Chat\User;

use MythicalClient\App;
use MythicalClient\Chat\Database;
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
                    $this->setSecurityHeaders();

                    $this->app = $app;
                    $this->SESSION_KEY = $_COOKIE['user_token'];
                    $this->updateLastSeen();
                    $this->updateCookie();
                    $this->setSecurityCookies();

                    if ($this->getInfo(UserColumns::TWO_FA_BLOCKED, false) == 'true') {
                        $app->Unauthorized('Please verify 2fa to access this endpoint.', ['error_code' => 'TWO_FA_BLOCKED']);
                    }
                } catch (\Exception $e) {
                    $app->Unauthorized('Bad Request', ['error_code' => 'INVALID_ACCOUNT_TOKEN']);
                }
            } else {
                $app->Unauthorized('Login info provided are invalid!', ['error_code' => 'INVALID_ACCOUNT_TOKEN']);
            }
        } else {
            $app->Unauthorized('Please tell me who you are.', ['error_code' => 'MISSING_ACCOUNT_TOKEN']);
        }
    }

    public function __destruct()
    {
        unset($this->app);
    }

    public function updateCookie(): void
    {
        if (isset($_COOKIE['user_token']) && !empty($_COOKIE['user_token'])) {
            $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
            setcookie('user_token', $_COOKIE['user_token'], [
                'expires' => time() + 1800,
                'path' => '/',
                'domain' => $_SERVER['HTTP_HOST'],
                'secure' => $secure,
                'httponly' => true,
                'samesite' => 'Strict',
            ]);
        }
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

    /**
     * Set security headers for the application.
     */
    private function setSecurityHeaders(): void
    {
        // CORS headers
        header('Access-Control-Allow-Methods: GET, POST, PUT, DELETE, OPTIONS');
        header('Access-Control-Allow-Headers: Content-Type, Authorization, X-Requested-With');
        header('Access-Control-Allow-Credentials: true');

        // Security headers
        header('X-Content-Type-Options: nosniff');
        header('X-Frame-Options: SAMEORIGIN');
        header('X-XSS-Protection: 1; mode=block');
        header('Referrer-Policy: strict-origin-when-cross-origin');
        header('Permissions-Policy: geolocation=(), microphone=(), camera=()');

        // If using HTTPS
        if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on') {
            header('Strict-Transport-Security: max-age=31536000; includeSubDomains; preload');
        }

        // Content Security Policy
        header("Content-Security-Policy: default-src 'self'; script-src 'self' 'unsafe-inline' 'unsafe-eval'; style-src 'self' 'unsafe-inline';");
    }

    /**
     * Set additional security and functionality cookies.
     */
    private function setSecurityCookies(): void
    {
        $secure = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on';
        $cookieOptions = [
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => $secure,
            'httponly' => true,
            'samesite' => 'Strict',
        ];

        // Session fingerprint to prevent session hijacking
        $fingerprint = hash('sha256', $_SERVER['HTTP_USER_AGENT'] . CloudFlareRealIP::getRealIP());
        setcookie('session_fingerprint', $fingerprint, array_merge($cookieOptions, [
            'expires' => time() + 1800, // 30 minutes
        ]));

        // Last activity timestamp for session management
        setcookie('last_activity', time(), array_merge($cookieOptions, [
            'expires' => time() + 1800,
            'httponly' => false, // Allow JavaScript to read this for UI updates
        ]));

        // User preferences (theme, language, etc.)
        $userPrefs = [

        ];
        setcookie('user_preferences', json_encode($userPrefs), array_merge($cookieOptions, [
            'expires' => time() + (86400 * 30), // 30 days
            'httponly' => false, // Allow JavaScript to read preferences
        ]));

        // CSRF token for form submissions
        $csrfToken = bin2hex(random_bytes(32));
        setcookie('csrf_token', $csrfToken, array_merge($cookieOptions, [
            'expires' => time() + 1800,
        ]));

        setcookie('remember_me', '1', array_merge($cookieOptions, [
            'expires' => time() + (86400 * 30), // 30 days
        ]));
    }


    /**
     * Force user reauthorization by clearing cookies and session.
     */
    private function forceReauthorization(): void
    {
        $cookieOptions = [
            'expires' => time() - 3600,
            'path' => '/',
            'domain' => $_SERVER['HTTP_HOST'],
            'secure' => isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on',
            'httponly' => true,
            'samesite' => 'Strict',
        ];

        // Clear all security cookies
        setcookie('user_token', '', $cookieOptions);
        setcookie('session_fingerprint', '', $cookieOptions);
        setcookie('last_activity', '', $cookieOptions);
        setcookie('csrf_token', '', $cookieOptions);

        $this->app->Unauthorized('Security validation failed. Please login again.', [
            'error_code' => 'SECURITY_VALIDATION_FAILED',
        ]);
    }
}
