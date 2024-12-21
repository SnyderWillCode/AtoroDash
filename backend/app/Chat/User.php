<?php

/*
 * This file is part of MythicalClient.
 * Please view the LICENSE file that was distributed with this source code.
 *
 * MIT License
 *
 * (c) MythicalSystems <mythicalsystems.xyz> - All rights reserved
 * (c) NaysKutzu <nayskutzu.xyz> - All rights reserved
 * (c) Cassian Gherman <nayskutzu.xyz> - All rights reserved
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

namespace MythicalClient\Chat;

use Gravatar\Gravatar;
use MythicalClient\App;
use MythicalClient\Chat\interface\UserActivitiesTypes;
use MythicalClient\Mail\Mail;
use MythicalClient\Mail\templates\Verify;
use MythicalClient\Mail\templates\NewLogin;
use MythicalClient\Chat\columns\UserColumns;
use MythicalClient\Mail\templates\ResetPassword;
use MythicalClient\Chat\columns\EmailVerificationColumns;
use MythicalSystems\CloudFlare\CloudFlare;

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
