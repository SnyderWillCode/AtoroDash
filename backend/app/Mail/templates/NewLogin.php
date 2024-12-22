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

namespace MythicalClient\Mail\templates;

use MythicalClient\App;
use MythicalClient\Chat\Mails;
use MythicalClient\Chat\User;
use MythicalClient\Mail\Mail;
use MythicalClient\Chat\Database;

use MythicalClient\Chat\columns\UserColumns;

class NewLogin extends Mail
{
    public static function sendMail(string $uuid): void
    {
        try {
            $template = self::getFinalTemplate($uuid);
            $email = User::getInfo(User::getTokenFromUUID($uuid), UserColumns::EMAIL, false);
            Mails::add('New Login Detected', $template, $uuid);
            self::send($email, 'New Login Detected', $template);
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Mail/templates/NewLogin.php) [sendMail] Failed to send email: ' . $e->getMessage());
        }
    }

    private static function getFinalTemplate(string $uuid): string
    {
        return self::processTemplate(self::getTemplate(), $uuid);
    }

    private static function getTemplate(): ?string
    {
        try {
            $conn = Database::getPdoConnection();
            $query = $conn->prepare('SELECT content FROM mythicalclient_mail_templates WHERE name = :name');
            $query->execute(['name' => 'new_login']);
            $template = $query->fetchColumn();

            return $template;
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Mail/templates/NewLogin.php) [sendMail] Failed to process template: ' . $e->getMessage());

            return null;
        }
    }

    private static function processTemplate(string $template, string $uuid): string
    {
        try {
            $template = self::getTemplate();
            $template = User::processTemplate($template, $uuid);
            $template = Mail::processEmailTemplateGlobal($template);

            return $template;
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Mail/templates/NewLogin.php) [sendMail] Failed to process template: ' . $e->getMessage());

            return '';
        }
    }
}
