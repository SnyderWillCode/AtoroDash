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
use MythicalClient\Chat\User\Roles;
use MythicalClient\Chat\User\Billing;
use MythicalClient\Chat\User\Session;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Chat\User\UserActivities;

$router->post('/api/user/session/info/update', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();
    $session = new Session($appInstance);

    try {
        if (!isset($_POST['first_name']) && $_POST['first_name'] == '') {
            $appInstance->BadRequest('First name is missing!', ['error_code' => 'FIRST_NAME_MISSING']);
        }
        if (!isset($_POST['last_name']) && $_POST['last_name'] == '') {
            $appInstance->BadRequest('Last name is missing!', ['error_code' => 'LAST_NAME_MISSING']);
        }
        if (!isset($_POST['email']) && $_POST['email'] == '') {
            $appInstance->BadRequest('Email is missing!', ['error_code' => 'EMAIL_MISSING']);
        }
        if (!isset($_POST['avatar']) && $_POST['avatar'] == '') {
            $appInstance->BadRequest('Avatar is missing!', ['error_code' => 'AVATAR_MISSING']);
        }
        if (!isset($_POST['background']) && $_POST['background'] == '') {
            $appInstance->BadRequest('Background is missing!', ['error_code' => 'BACKGROUND_MISSING']);
        }

        if ($_POST['email'] != $session->getInfo(UserColumns::EMAIL, false) && User::exists(UserColumns::EMAIL, $_POST['email'])) {
            $appInstance->BadRequest('Email already exists!', ['error_code' => 'EMAIL_EXISTS']);
        }

        $session->setInfo(UserColumns::FIRST_NAME, $_POST['first_name'], true);
        $session->setInfo(UserColumns::LAST_NAME, $_POST['last_name'], true);
        $session->setInfo(UserColumns::EMAIL, $_POST['email'], false);
        $session->setInfo(UserColumns::AVATAR, $_POST['avatar'], false);
        $session->setInfo(UserColumns::BACKGROUND, $_POST['background'], false);

        $appInstance->OK('User info updated successfully!', []);
    } catch (Exception $e) {
        $appInstance->getLogger()->error('Failed to update user info! ' . $e->getMessage());
        $appInstance->BadRequest('Bad Request', ['error_code' => 'DB_ERROR', 'error' => $e->getMessage()]);
    }
});

$router->post('/api/user/session/billing/update', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyPOST();
    $session = new Session($appInstance);

    try {
        if (!isset($_POST['company_name']) && $_POST['company_name'] == '') {
            $appInstance->BadRequest('Company name is missing!', ['error_code' => 'COMPANY_NAME_MISSING']);
        }
        $companyName = $_POST['company_name'];
        if (!isset($_POST['vat_number']) && $_POST['vat_number'] == '') {
            $appInstance->BadRequest('VAT Number is missing!', ['error_code' => 'VAT_NUMBER_MISSING']);
        }
        $vatNumber = $_POST['vat_number'];
        if (!isset($_POST['address1']) && $_POST['address1'] == '') {
            $appInstance->BadRequest('Address 1 is missing', ['error_code' => 'ADDRESS1_MISSING']);
        }
        $address1 = $_POST['address1'];
        if (!isset($_POST['address2']) && $_POST['address2'] == '') {
            $appInstance->BadRequest('Address 2 is missing', ['error_code' => 'ADDRESS2_MISSING']);
        }
        $address2 = $_POST['address2'];
        if (!isset($_POST['city']) && $_POST['city'] == '') {
            $appInstance->BadRequest('City is missing', ['error_code' => 'CITY_MISSING']);
        }
        $city = $_POST['city'];
        if (!isset($_POST['country']) && $_POST['country'] == '') {
            $appInstance->BadRequest('Country is missing', ['error_code' => 'COUNTRY_MISSING']);
        }
        $country = $_POST['country'];
        if (!isset($_POST['state']) && $_POST['state'] == '') {
            $appInstance->BadRequest('State is missing', ['error_code' => 'STATE_MISSING']);
        }
        $state = $_POST['state'];
        if (!isset($_POST['postcode']) && $_POST['postcode'] == '') {
            $appInstance->BadRequest('PostCode is missing', ['error_code' => 'POSTCODE_MISSING']);
        }
        $postcode = $_POST['postcode'];

        Billing::updateBilling(
            $session->getInfo(UserColumns::UUID, false),
            $companyName,
            $vatNumber,
            $address1,
            $address2,
            $city,
            $country,
            $state,
            $postcode
        );

        $appInstance->OK('Billing info saved successfully!', []);
    } catch (Exception $e) {
        $appInstance->getLogger()->error('Failed to save billing info! ' . $e->getMessage());
        $appInstance->BadRequest('Bad Request', ['error_code' => 'DB_ERROR', 'error' => $e->getMessage()]);
    }
});

$router->add('/api/user/session/newPin', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyPOST();
    $session = new Session($appInstance);
    $pin = $appInstance->generatePin();
    try {
        $session->setInfo(UserColumns::SUPPORT_PIN, $pin, false);
        $appInstance->OK('Support pin updated successfully!', ['pin' => $pin]);
    } catch (Exception $e) {
        $appInstance->getLogger()->error('Failed to generate new pin: ' . $e->getMessage());
        $appInstance->BadRequest('Bad Request', ['error_code' => 'DB_ERROR', 'error' => $e->getMessage()]);
    }
});

$router->get('/api/user/session', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);
    $accountToken = $session->SESSION_KEY;
    try {
        $billing = Billing::getBillingData(User::getInfo($accountToken, UserColumns::UUID, false));
        $columns = [
            UserColumns::USERNAME,
            UserColumns::EMAIL,
            UserColumns::VERIFIED,
            UserColumns::SUPPORT_PIN,
            UserColumns::BANNED,
            UserColumns::TWO_FA_BLOCKED,
            UserColumns::TWO_FA_ENABLED,
            UserColumns::TWO_FA_KEY,
            UserColumns::FIRST_NAME,
            UserColumns::LAST_NAME,
			UserColumns::AVATAR,
			UserColumns::CREDITS,
            UserColumns::UUID,
            UserColumns::ROLE_ID,
            UserColumns::FIRST_IP,
            UserColumns::LAST_IP,
            UserColumns::DELETED,
            UserColumns::LAST_SEEN,
            UserColumns::FIRST_SEEN,
            UserColumns::BACKGROUND,
        ];

        $info = User::getInfoArray($accountToken, $columns, [
            UserColumns::FIRST_NAME,
            UserColumns::LAST_NAME,
            UserColumns::TWO_FA_KEY,
        ]);
        $info['role_name'] = Roles::getUserRoleName($info[UserColumns::UUID]);
        $info['role_real_name'] = strtolower($info['role_name']);

        $appInstance->OK('Account token is valid', [
            'user_info' => $info,
            'billing' => $billing,
        ]);

    } catch (Exception $e) {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_ACCOUNT_TOKEN', 'error' => $e->getMessage()]);
    }

});

$router->get('/api/user/session/activities', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $appInstance->allowOnlyGET();
    $session = new Session($appInstance);
    $accountToken = $session->SESSION_KEY;
    $appInstance->OK('User activities', [
        'activities' => UserActivities::get(User::getInfo($accountToken, UserColumns::UUID, false)),
    ]);
});
