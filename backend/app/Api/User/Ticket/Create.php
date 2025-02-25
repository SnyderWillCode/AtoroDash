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
use MythicalClient\Chat\User\User;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\Tickets\Tickets;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\Tickets\Departments;

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

    if (isset($_POST['department_id']) && $_POST['department_id'] != '') {
        $departmentId = $_POST['department_id'];
        if (Departments::exists((int) $departmentId)) {
            /**
             * Make that every info needed is provided.
             */
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
            /**
             * Check if the user has more than 3 open tickets.
             */
            $userTickets = Tickets::getAllTicketsByUser($session->getInfo(UserColumns::UUID, false), 150);
            $openTickets = array_filter($userTickets, function ($ticket) {
                return in_array($ticket['status'], ['open', 'waiting', 'replied', 'inprogress']);
            });
            if (count($openTickets) >= 3) {
                $appInstance->BadRequest('You have reached the limit of 3 open tickets!', ['error_code' => 'LIMIT_REACHED']);
            }
            /**
             * Create the ticket.
             */
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
