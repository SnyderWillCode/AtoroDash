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

namespace MythicalClient\Mail;

use MythicalClient\App;
use MythicalClient\Chat\Database;
use MythicalClient\Config\ConfigFactory;
use MythicalClient\Config\ConfigInterface;
use MythicalClient\Mail\services\SMTPServer;

class Mail
{
    /**
     * Send an email.
     *
     * @param string $to The email address of the recipient
     * @param string $subject The subject of the email
     * @param string $message The message of the email
     */
    public static function send(string $to, string $subject, string $message): void
    {
        // TODO: Add more drivers
        try {
            SMTPServer::send($to, $subject, $message);
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Mail/Mail.php) [send] Failed to send email: ' . $e->getMessage());
        }
    }

    /**
     * Is the SMTP server enabled?
     */
    public static function isEnabled(): bool
    {
        $config = new ConfigFactory(Database::getPdoConnection());

        if ($config->getSetting(ConfigInterface::SMTP_ENABLED, 'false') == 'true') {
            return true;
        }

        return false;

    }

    public static function processEmailTemplateGlobal(string $template): string
    {
        $config = new ConfigFactory(Database::getPdoConnection());

        $template = str_replace('${app_name}', $config->getSetting(ConfigInterface::APP_NAME, 'MythicalSystems'), $template);
        $template = str_replace('${app_url}', $config->getSetting(ConfigInterface::APP_URL, 'localhost'), $template);
        $template = str_replace('${app_logo}', $config->getSetting(ConfigInterface::APP_LOGO, 'https://github.com/mythicalltd.png'), $template);
        $template = str_replace('${app_lang}', $config->getSetting(ConfigInterface::APP_LANG, 'en_US'), $template);
        $template = str_replace('${app_timezone}', $config->getSetting(ConfigInterface::APP_TIMEZONE, 'UTC'), $template);
        $template = str_replace('${app_version}', $config->getSetting(ConfigInterface::APP_VERSION, '1.0.0'), $template);

        return $template;
    }
}
