<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * 2021-2025 (c) All rights reserved
 *
 * MIT License
 *
 * (c) MythicalSystems - All rights reserved
 * (c) NaysKutzu - All rights reserved
 * (c) Cassian Gherman- All rights reserved
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

use MythicalClient\App;
use MythicalClient\Chat\Can;
use MythicalClient\Chat\Session;
use MythicalClient\Hooks\GitHub;
use MythicalClient\Chat\Database;
use MythicalClient\Chat\UserActivities;
use MythicalClient\Chat\columns\UserColumns;

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
                    'github_data' => json_decode($github_data, true),
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
            $appInstance->ServiceUnavailable($e->getMessage(), ['error_code' => 'SERVICE_UNAVAILABLE']);
        }
    } else {
        $appInstance->Unauthorized('You do not have permission to access this endpoint.', ['error_code' => 'NO_PERMISSION']);
    }

});
