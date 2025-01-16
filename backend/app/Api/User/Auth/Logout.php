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

use MythicalClient\App;

$router->get('/api/user/auth/logout', function (): void {
    echo '<script>
        localStorage.clear();
        sessionStorage.clear();
    </script>';
    try {
        setcookie('user_token', '', time() - 460800 * 460800 * 460800, '/');
        if (session_status() === PHP_SESSION_ACTIVE) {
            session_unset();
            session_destroy();
        }

        header('location: /auth/login?href=api');
        exit;
    } catch (Exception $e) {
        App::getInstance(true)->getLogger()->error('Failed to logout user' . $e->getMessage());
        header('location: /auth/login?href=api');
    }
});
