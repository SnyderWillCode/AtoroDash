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

namespace MythicalClient\Mail\templates;

use MythicalClient\App;
use MythicalClient\Chat\User;
use MythicalClient\Mail\Mail;
use MythicalClient\Chat\Mails;
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
