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
        $appInstance = App::getInstance(true);

        try {
            $appInstance->getLogger()->debug('Sending email to ' . $to);
            SMTPServer::send($to, $subject, $message);
            $appInstance->getLogger()->debug('Email sent to ' . $to);
        } catch (\Exception $e) {
            $appInstance->getLogger()->error('(' . APP_SOURCECODE_DIR . '/Mail/Mail.php) [send] Failed to send email: ' . $e->getMessage());
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
