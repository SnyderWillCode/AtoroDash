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
use MythicalClient\Chat\Session;
use MythicalClient\Chat\Tickets;
use MythicalClient\Chat\Departments;
use MythicalClient\Chat\columns\UserColumns;

$router->get('/api/user/ticket/create', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    new Session($appInstance);

    $departments = Departments::getAll();
    $services = [
        [
            'id' => 1,
            'name' => 'Service 1',
            'active' => true,
        ],
    ];

    $appInstance->OK('Ticket Process', [
        'departments' => $departments,
        'services' => $services,
    ]);
});

$router->post('/api/user/ticket/create', function () {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyPOST();
    $session = new Session($appInstance);
    $accountToken = $session->SESSION_KEY;

    if (isset($_POST['department_id']) && $_POST['department_id'] != '') {
        $departmentId = $_POST['department_id'];
        if (Departments::exists((int) $departmentId)) {
            if (isset($_POST['service_id']) && $_POST['service_id'] != '') {
                $serviceId = $_POST['service_id'];
            } else {
                $serviceId = null;
            }

            if (isset($_POST['subject']) && $_POST['subject'] != '') {
                $subject = $_POST['subject'];
            } else {
                $appInstance->BadRequest('Subject is missing!', ['error_code' => 'SUBJECT_MISSING']);
            }

            if (isset($_POST['message']) && $_POST['message'] != '') {
                $message = $_POST['message'];
            } else {
                $appInstance->BadRequest('Message is missing!', ['error_code' => 'MESSAGE_MISSING']);
            }

            if (isset($_POST['priority']) && $_POST['priority'] != '') {
                $priority = $_POST['priority'];
            } else {
                $priority = 'normal';
            }

            // TODO: Check if service exists
            // TODO: Limit to 3 open tickets user

            $ticketId = Tickets::create($session->getInfo(UserColumns::UUID, false), $departmentId, $serviceId, $subject, $message, $priority);
            if ($ticketId == 0) {
                $appInstance->BadRequest('Failed to create ticket!', ['error_code' => 'FAILED_TO_CREATE_TICKET']);
            } else {
                $appInstance->OK('Ticket created successfully!', ['ticket_id' => $ticketId]);
            }
        } else {
            $appInstance->BadRequest('Department not found!', ['error_code' => 'DEPARTMENT_NOT_FOUND']);
        }
    } else {
        $appInstance->BadRequest('Department ID is missing!', ['error_code' => 'DEPARTMENT_ID_MISSING']);
    }
});
