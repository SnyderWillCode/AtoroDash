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

namespace MythicalClient\Mail\services;

use MythicalClient\App;
use MythicalClient\Chat\Database;
use MythicalClient\Config\ConfigFactory;
use MythicalClient\Config\ConfigInterface;

class SMTPServer
{
    public static function send(string $to, string $subject, string $body)
    {
        $appInstance = App::getInstance(true);
        $appInstance->getLogger()->debug('Sending email to ' . $to);
        try {
            $config = new ConfigFactory(Database::getPdoConnection());
            if ($config->getSetting(ConfigInterface::SMTP_ENABLED, 'false') == 'true') {
                if (
                    $config->getSetting(ConfigInterface::SMTP_HOST, null) == null
                    || $config->getSetting(ConfigInterface::SMTP_PORT, null) == null
                    || $config->getSetting(ConfigInterface::SMTP_USER, null) == null
                    || $config->getSetting(ConfigInterface::SMTP_PASS, null) == null
                    || $config->getSetting(ConfigInterface::SMTP_FROM, null) == null
                ) {
                    $appInstance->getLogger()->info('Failed to send email, SMTP settings are not configured.');

                    return;
                }
                $mail = new \PHPMailer\PHPMailer\PHPMailer(false);
                try {
                    $mail->isSMTP();
                    $mail->Host = $config->getSetting(ConfigInterface::SMTP_HOST, null);
                    $mail->SMTPAuth = true;
                    $mail->Username = $config->getSetting(ConfigInterface::SMTP_USER, null);
                    $mail->Password = $config->getSetting(ConfigInterface::SMTP_PASS, null);
                    $mail->SMTPSecure = $config->getSetting(ConfigInterface::SMTP_ENCRYPTION, 'ssl');
                    $mail->Port = $config->getSetting(ConfigInterface::SMTP_PORT, null);
                    $mail->setFrom($config->getSetting(ConfigInterface::SMTP_FROM, null), $config->getSetting(ConfigInterface::APP_NAME, null));
                    $mail->addReplyTo($config->getSetting(ConfigInterface::SMTP_FROM, null), $config->getSetting(ConfigInterface::APP_NAME, null));
                    $mail->isHTML(true);
                    $mail->Subject = $subject;
                    $mail->Body = $body;
                    $mail->addAddress($to);
                    $mail->send();
                    $appInstance->getLogger()->debug('Email sent');
                } catch (\Exception $e) {
                    $appInstance->getLogger()->error('Failed to send email: ' . $e->getMessage());

                    return;
                }

            }
        } catch (\Exception $e) {
            $appInstance->getLogger()->error('Failed to send email: ' . $e->getMessage());
        }
    }
}
