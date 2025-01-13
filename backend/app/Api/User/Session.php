<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * # MythicalSystems License v2.0
 *
 * ## Copyright (c) 2021–2025 MythicalSystems and Cassian Gherman
 *
 * ### Preamble
 * This license governs the use, modification, and distribution of the software known as MythicalClient or MythicalDash ("the Software"). By using the Software, you agree to the terms outlined in this document. These terms aim to protect the Software’s integrity, ensure fair use, and establish guidelines for authorized distribution, modification, and commercial use.
 *
 * For any inquiries, abuse reports, or violation notices, contact us at [abuse@mythicalsystems.xyz](mailto:abuse@mythicalsystems.xyz).
 *
 * ## 1. Grant of License
 *
 * ### 1.1. Usage Rights
 * - You are granted a non-exclusive, revocable license to use the Software, provided you comply with the terms herein.
 * - The Software must be linked to an active account on our public platform, MythicalSystems.
 *
 * ### 1.2. Modification Rights
 * - You may modify the Software only for personal use and must not distribute modified versions unless explicitly approved by MythicalSystems or Cassian Gherman.
 *
 * ### 1.3. Redistribution and Commercial Use
 * - Redistribution of the Software, whether modified or unmodified, is strictly prohibited unless explicitly authorized in writing by MythicalSystems or Cassian Gherman.
 * - Selling the Software or its derivatives is only permitted on authorized marketplaces specified by MythicalSystems.
 * - Unauthorized leaking, sharing, or redistribution of the Software or its components is illegal and subject to legal action.
 *
 * ### 1.4. Third-Party Addons and Plugins
 * - The creation, sale, and distribution of third-party addons or plugins for the Software are permitted, provided they comply with this license.
 * - All third-party addons or plugins must not attempt to bypass, modify, or interfere with the core functionality or licensing requirements of the Software.
 *
 * ## 2. Restrictions
 *
 * ### 2.1. Account Requirement
 * - The Software requires an active account on MythicalSystems. Attempts to modify, bypass, or remove this requirement are strictly prohibited.
 *
 * ### 2.2. Unauthorized Use
 * - Use of the Software to perform unauthorized actions, including but not limited to exploiting vulnerabilities, bypassing authentication, or reverse engineering, is prohibited.
 *
 * ### 2.3. Leaking and Distribution
 * - Any unauthorized leaking, sharing, or distribution of the Software is a direct violation of this license. Legal action will be taken against violators.
 * - Leaked or pirated copies of the Software are considered illegal, and users found utilizing such versions will face immediate termination of access and potential legal consequences.
 *
 * ### 2.4. Modification of Terms
 * - The terms and conditions of this license may not be modified, replaced, or overridden in any distributed version of the Software.
 *
 * ## 3. Attribution and Copyright
 *
 * ### 3.1. Attribution
 * - You must retain all copyright notices, attributions, and references to MythicalSystems and Cassian Gherman in all copies, derivatives, or distributions of the Software.
 *
 * ### 3.2. Copyright Invariance
 * - Copyright notices must remain intact and unaltered in all versions of the Software, including modified versions.
 *
 * ## 4. Legal and Liability Terms
 *
 * ### 4.1. Disclaimer of Liability
 * - The Software is provided "as is," without any warranty, express or implied, including but not limited to warranties of merchantability, fitness for a particular purpose, or non-infringement.
 * - MythicalSystems and Cassian Gherman shall not be held liable for any damages arising from the use, misuse, or inability to use the Software, including but not limited to:
 * 	- Loss of data, profits, or revenue.
 * 	- Security vulnerabilities such as SQL injection, zero-day exploits, or other potential risks.
 * 	- System failures, downtime, or disruptions.
 *
 * ### 4.2. Enforcement
 * - Violations of this license will result in immediate termination of access to the Software and may involve legal action.
 * - MythicalSystems reserves the right to suspend or terminate access to any user, client, or hosting provider without prior notice.
 *
 * ### 4.3. No Guarantees
 * - MythicalSystems does not guarantee uninterrupted or error-free operation of the Software.
 *
 * ## 5. Privacy and Data Sharing
 *
 * ### 5.1. Public Information
 * - Some user information may be shared with third parties or made publicly visible in accordance with our Privacy Policy and Terms of Service. For more details, please visit:
 * 	- [Privacy Policy](https://www.mythical.systems/privacy)
 * 	- [Terms of Service](https://www.mythical.systems/terms)
 *
 * ### 5.2. Data Collection
 * - The Software may collect and transmit anonymized usage data to improve performance and functionality.
 *
 * ## 6. Governing Law
 *
 * ### 6.1. Jurisdiction
 * - This license shall be governed and construed in accordance with the laws of Austria.
 *
 * ### 6.2. Dispute Resolution
 * - All disputes arising under or in connection with this license shall be subject to the exclusive jurisdiction of the courts in Austria.
 *
 * ## 7. Termination
 *
 * ### 7.1. Violation of Terms
 * - MythicalSystems reserves the right to terminate access to the Software for any user found in violation of this license.
 *
 * ### 7.2. Immediate Termination
 * - Termination may occur immediately and without prior notice.
 *
 * ## 8. Contact Information
 * For abuse reports, legal inquiries, or support, contact [abuse@mythicalsystems.xyz](mailto:abuse@mythicalsystems.xyz).
 *
 * ## 9. Acceptance
 * By using, modifying, or distributing the Software, you agree to the terms outlined in this license.
 */

use MythicalClient\App;
use MythicalClient\Chat\User;
use MythicalClient\Chat\Roles;
use MythicalClient\Chat\Billing;
use MythicalClient\Chat\Session;
use MythicalClient\Chat\UserActivities;
use MythicalClient\Chat\columns\UserColumns;

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

$router->get('/api/user/session', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;
    try {
        $billing = Billing::getBillingData(User::getInfo($accountToken, UserColumns::UUID, false));
        $appInstance->OK('Account token is valid', [
            'user_info' => [
                'username' => User::getInfo($accountToken, UserColumns::USERNAME, false),
                'email' => User::getInfo($accountToken, UserColumns::EMAIL, false),
                'verified' => User::getInfo($accountToken, UserColumns::VERIFIED, false),
                'banned' => User::getInfo($accountToken, UserColumns::BANNED, false),
                '2fa_blocked' => User::getInfo($accountToken, UserColumns::TWO_FA_BLOCKED, false),
                '2fa_enabled' => User::getInfo($accountToken, UserColumns::TWO_FA_ENABLED, false),
                '2fa_secret' => User::getInfo($accountToken, UserColumns::TWO_FA_KEY, false),
                'first_name' => User::getInfo($accountToken, UserColumns::FIRST_NAME, true),
                'last_name' => User::getInfo($accountToken, UserColumns::LAST_NAME, true),
                'avatar' => User::getInfo($accountToken, UserColumns::AVATAR, false),
                'uuid' => User::getInfo($accountToken, UserColumns::UUID, false),
                'role_id' => User::getInfo($accountToken, UserColumns::ROLE_ID, false),
                'first_ip' => User::getInfo($accountToken, UserColumns::FIRST_IP, false),
                'last_ip' => User::getInfo($accountToken, UserColumns::LAST_IP, false),
                'deleted' => User::getInfo($accountToken, UserColumns::DELETED, false),
                'last_seen' => User::getInfo($accountToken, UserColumns::LAST_SEEN, false),
                'first_seen' => User::getInfo($accountToken, UserColumns::FIRST_SEEN, false),
                'background' => User::getInfo($accountToken, UserColumns::BACKGROUND, false),
                'role_name' => Roles::getUserRoleName(User::getInfo($accountToken, UserColumns::UUID, false)),
                'role_real_name' => strtolower(Roles::getUserRoleName(User::getInfo($accountToken, UserColumns::UUID, false))),
            ],
            'billing' => $billing,
        ]);
    } catch (Exception $e) {
        $appInstance->BadRequest('Bad Request', ['error_code' => 'INVALID_ACCOUNT_TOKEN', 'error' => $e->getMessage()]);
    }

});

$router->get('/api/user/session/activities', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $appInstance->allowOnlyGET();

    $session = new Session($appInstance);

    $accountToken = $session->SESSION_KEY;

    $appInstance->OK('User activities', [
        'activities' => UserActivities::get(User::getInfo($accountToken, UserColumns::UUID, false)),
    ]);
});
