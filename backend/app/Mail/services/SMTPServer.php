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

namespace MythicalClient\Mail\services;

use MythicalClient\App;
use MythicalClient\Chat\Database;
use MythicalClient\Config\ConfigFactory;
use MythicalClient\Config\ConfigInterface;

class SMTPServer
{
    public static function send(string $to, string $subject, string $body)
    {
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
                    App::getInstance(true)->getLogger()->info('Failed to send email, SMTP settings are not configured.');

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
                } catch (\Exception $e) {
                    App::getInstance(true)->getLogger()->error('Failed to send email: ' . $e->getMessage());

                    return;
                }

            }
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to send email: ' . $e->getMessage());
        }
        // No exception handling!!

    }
}
