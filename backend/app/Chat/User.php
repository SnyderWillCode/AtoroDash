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

namespace MythicalClient\Chat;

use Gravatar\Gravatar;
use MythicalClient\App;
use MythicalClient\Mail\Mail;
use MythicalClient\Mail\templates\Verify;
use MythicalSystems\CloudFlare\CloudFlare;
use MythicalClient\Mail\templates\NewLogin;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Mail\templates\ResetPassword;
use MythicalClient\Chat\interface\UserActivitiesTypes;
use MythicalClient\Chat\columns\EmailVerificationColumns;

class User extends Database
{
    public const TABLE_NAME = 'mythicalclient_users';

    /**
     * Register a new user in the database.
     *
     * @param string $username The username of the user
     * @param string $password The password of the user
     * @param string $email The email of the user
     * @param string $first_name The first name of the user
     * @param string $last_name The last name of the user
     * @param string $ip The ip of the user
     *
     * @return string
     */
    public static function register(string $username, string $password, string $email, string $first_name, string $last_name, string $ip): void
    {
        try {
            $first_name = App::getInstance(true)->encrypt($first_name);
            $last_name = App::getInstance(true)->encrypt($last_name);

            /**
             * The UUID generation and logic.
             */
            $uuidMngr = new \MythicalSystems\User\UUIDManager();
            $uuid = $uuidMngr->generateUUID();
            $token = App::getInstance(true)->encrypt(date('Y-m-d H:i:s') . $uuid . random_bytes(16) . base64_encode($email));

            /**
             * GRAvatar Logic.
             */
            try {
                $gravatar = new Gravatar(['s' => 9001], true);
                $avatar = $gravatar->avatar($email);
            } catch (\Exception) {
                $avatar = 'https://www.gravatar.com/avatar';
            }

            /**
             * Get the PDO connection.
             */
            $pdoConnection = self::getPdoConnection();

            /**
             * Prepare the statement.
             */
            $stmt = $pdoConnection->prepare('
            INSERT INTO ' . self::TABLE_NAME . ' 
            (username, first_name, last_name, email, password, avatar, background, uuid, token, role, first_ip, last_ip, banned, verified) 
            VALUES 
            (:username, :first_name, :last_name, :email, :password, :avatar, :background, :uuid, :token, :role, :first_ip, :last_ip, :banned, :verified)
        ');
            $password = App::getInstance(true)->encrypt($password);

            $stmt->execute([
                ':username' => $username,
                ':first_name' => $first_name,
                ':last_name' => $last_name,
                ':email' => $email,
                ':password' => $password,
                ':avatar' => $avatar,
                ':background' => 'https://cdn.mythicalsystems.xyz/background.gif',
                ':uuid' => $uuid,
                ':token' => $token,
                ':role' => 1,
                ':first_ip' => $ip,
                ':last_ip' => $ip,
                ':banned' => 'NO',
                ':verified' => 'false',
            ]);
            \MythicalClient\MythicalSystems\Telemetry::send(\MythicalClient\MythicalSystems\TelemetryCollection::USER_NEW);
            /**
             * Check if the mail is enabled.
             *
             * If it is, the user is not verified.
             *
             * If it is not, the user is verified.
             */
            if (Mail::isEnabled()) {
                try {
                    $verify_token = App::getInstance(true)->generateCode();
                    Verification::add($verify_token, $uuid, EmailVerificationColumns::$type_verify);
                    Verify::sendMail($uuid, $verify_token);
                } catch (\Exception $e) {
                    App::getInstance(true)->getLogger()->error('Failed to send email: ' . $e->getMessage());
                    self::updateInfo($token, UserColumns::VERIFIED, 'false', false);
                }
            } else {
                self::updateInfo($token, UserColumns::VERIFIED, 'true', false);
            }
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to register user: ' . $e->getMessage());
            throw new \Exception('Failed to register user: ' . $e->getMessage());
        }
    }

    /**
     * Get the list of users.
     *
     * @return array The list of users
     */
    public static function getList(): array
    {
        $con = self::getPdoConnection();
        $stmt = $con->prepare('SELECT * FROM ' . self::TABLE_NAME);
        $stmt->execute();

        return $stmt->fetchAll(\PDO::FETCH_ASSOC);
    }

    /**
     * Forgot password logic.
     *
     * @param string $email The email of the user
     *
     * @return bool If the email was sent
     */
    public static function forgotPassword(string $email): bool
    {
        try {
            $con = self::getPdoConnection();
            $stmt = $con->prepare('SELECT token, uuid FROM ' . self::TABLE_NAME . ' WHERE email = :email');
            $stmt->bindParam(':email', $email);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);

            if ($user) {
                if (Mail::isEnabled()) {
                    try {
                        $verify_token = $verify_token = App::getInstance(true)->generateCode();
                        Verification::add($verify_token, $user['uuid'], EmailVerificationColumns::$type_password);
                        ResetPassword::sendMail($user['uuid'], $verify_token);
                    } catch (\Exception $e) {
                        App::getInstance(true)->getLogger()->error('Failed to send email: ' . $e->getMessage());
                    }

                    return true;
                }

                return false;
            }

            return false;
        } catch (\Exception $e) {
            return false;
        }
    }

    /**
     * Login the user.
     *
     * @param string $login The login of the user
     * @param string $password The password of the user
     *
     * @return string If the login was successful
     */
    public static function login(string $login, string $password): string
    {
        try {
            $con = self::getPdoConnection();
            $stmt = $con->prepare('SELECT password, token, uuid FROM ' . self::TABLE_NAME . ' WHERE username = :login OR email = :login');
            $stmt->bindParam(':login', $login);
            $stmt->execute();
            $user = $stmt->fetch(\PDO::FETCH_ASSOC);
            if ($user) {
                if (App::getInstance(true)->decrypt($user['password']) == $password) {
                    self::logout();
                    if (!$user['token'] == '') {
                        setcookie('user_token', $user['token'], time() + 3600, '/');
                    } else {
                        App::getInstance(true)->getLogger()->error('Failed to login user: Token is empty');

                        return 'false';
                    }
                    if (Mail::isEnabled()) {
                        try {
                            NewLogin::sendMail($user['uuid']);
                        } catch (\Exception $e) {
                            App::getInstance(true)->getLogger()->error('Failed to send email: ' . $e->getMessage());
                        }
                    }
                    UserActivities::add($user['uuid'], UserActivitiesTypes::$login, CloudFlare::getRealUserIP());

                    return $user['token'];
                }

                return 'false';
            }

            return 'false';
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to login user: ' . $e->getMessage());

            return 'false';
        }
    }

    /**
     * Logout the user.
     */
    public static function logout(): void
    {
        setcookie('user_token', '', time() - 460800 * 460800 * 460800, '/');
    }

    /**
     * Does the user info exist?
     *
     * @param UserColumns $info
     */
    public static function exists(UserColumns|string $info, string $value): bool
    {
        if (!in_array($info, UserColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }

        $con = self::getPdoConnection();
        $stmt = $con->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE ' . $info . ' = :value');
        $stmt->bindParam(':value', $value);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }

    /**
     * Get the user info.
     *
     * @param UserColumns|string $info The column name
     *
     * @throws \InvalidArgumentException If the column name is invalid
     *
     * @return string|null The value of the column
     */
    public static function getInfo(string $token, UserColumns|string $info, bool $encrypted): ?string
    {
        if (!in_array($info, UserColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }
        $con = self::getPdoConnection();
        $stmt = $con->prepare('SELECT ' . $info . ' FROM ' . self::TABLE_NAME . ' WHERE token = :token');
        $stmt->bindParam(':token', $token);
        $stmt->execute();
        if ($encrypted) {
            return App::getInstance(true)->decrypt($stmt->fetchColumn());
        }

        return $stmt->fetchColumn();

    }

    /**
     * Update the user info.
     *
     * @param UserColumns|string $info The column name
     * @param string $value The value to update
     * @param bool $encrypted If the value is encrypted
     *
     * @throws \InvalidArgumentException If the column name is invalid
     *
     * @return bool If the update was successful
     */
    public static function updateInfo(string $token, UserColumns|string $info, string $value, bool $encrypted): bool
    {
        if (!in_array($info, UserColumns::getColumns())) {
            throw new \InvalidArgumentException('Invalid column name: ' . $info);
        }
        $con = self::getPdoConnection();
        if ($encrypted) {
            $value = App::getInstance(true)->encrypt($value);
        }
        $stmt = $con->prepare('UPDATE ' . self::TABLE_NAME . ' SET ' . $info . ' = :value WHERE token = :token');
        $stmt->bindParam(':value', $value);
        $stmt->bindParam(':token', $token);

        return $stmt->execute();
    }

    /**
     * Get the token from the UUID.
     *
     * @param string $uuid The UUID
     *
     * @return string The token
     */
    public static function getTokenFromUUID(string $uuid): string
    {
        $con = self::getPdoConnection();
        $stmt = $con->prepare('SELECT token FROM ' . self::TABLE_NAME . ' WHERE uuid = :uuid');
        $stmt->bindParam(':uuid', $uuid);
        $stmt->execute();

        return $stmt->fetchColumn();
    }

    /**
     * Process the template.
     *
     * @param string $template The template
     * @param string $uuid The UUID
     *
     * @return string The processed template
     */
    public static function processTemplate(string $template, string $uuid): string
    {
        try {

            $template = str_replace('${uuid}', $uuid, $template);
            $template = str_replace('${username}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::USERNAME, false), $template);
            $template = str_replace('${email}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::EMAIL, false), $template);
            $template = str_replace('${first_name}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::FIRST_NAME, true), $template);
            $template = str_replace('${last_name}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::LAST_NAME, true), $template);
            $template = str_replace('${avatar}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::AVATAR, false), $template);
            $template = str_replace('${background}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::BACKGROUND, false), $template);
            $template = str_replace('${role_id}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::ROLE_ID, false), $template);
            $template = str_replace('${first_ip}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::FIRST_IP, false), $template);
            $template = str_replace('${last_ip}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::LAST_IP, false), $template);
            $template = str_replace('${banned}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::BANNED, false), $template);
            $template = str_replace('${verified}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::VERIFIED, false), $template);
            $template = str_replace('${2fa_enabled}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::TWO_FA_ENABLED, false), $template);
            $template = str_replace('${deleted}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::DELETED, false), $template);
            $template = str_replace('${last_seen}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::LAST_SEEN, false), $template);
            $template = str_replace('${first_seen}', self::getInfo(self::getTokenFromUUID($uuid), UserColumns::FIRST_SEEN, false), $template);

            return $template;
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to render email template: ' . $e->getMessage());

            return null;
        }
    }
}
