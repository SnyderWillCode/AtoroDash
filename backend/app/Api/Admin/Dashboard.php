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
use MythicalClient\Hooks\GitHub;
use MythicalClient\Chat\Database;
use MythicalClient\Chat\User\Can;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\User\UserActivities;

$router->get('/api/admin', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);
    if (Can::canAccessAdminUI($session->getInfo(UserColumns::ROLE_ID, false))) {
        try {
            $github_data = new GitHub();
            $github_data = $github_data->getRepoData();
            $activity = UserActivities::getAll(150);
            $userCount = Database::getTableRowCount('mythicalclient_users');
            $addonsCount = Database::getTableRowCount('mythicalclient_addons');
            $departmentsCount = Database::getTableRowCount('mythicalclient_departments');
            $invoicesCount = Database::getTableRowCount('mythicalclient_invoices');
            $rolesCount = Database::getTableRowCount('mythicalclient_roles');
            $servicesCount = Database::getTableRowCount('mythicalclient_services');
            $ticketsCount = Database::getTableRowCount('mythicalclient_tickets');

            $appInstance->OK('Dashboard data retrieved successfully.', [
                'core' => [
                    'github_data' => $github_data,
                ],
                'count' => [
                    'user_count' => $userCount,
                    'addons_count' => $addonsCount,
                    'departments_count' => $departmentsCount,
                    'invoices_count' => $invoicesCount,
                    'roles_count' => $rolesCount,
                    'services_count' => $servicesCount,
                    'tickets_count' => $ticketsCount,
                ],
                'etc' => [
                    'activity' => $activity,
                ],
            ]);

        } catch (Exception $e) {
            $appInstance->InternalServerError($e->getMessage(), ['error_code' => 'SERVICE_UNAVAILABLE']);
        }
    } else {
        $appInstance->Unauthorized('You do not have permission to access this endpoint.', ['error_code' => 'NO_PERMISSION']);
    }

});
