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

namespace MythicalClient\Cli;

class App extends \MythicalSystems\Utils\BungeeChatApi
{
    public $prefix = '&7[&5&lMythical&d&lClient&7] &8&l| &7';
    public $bars = '&7&m-----------------------------------------------------&r';
    public static App $instance;

    public function __construct(string $commandName, array $args)
    {
        $this->handleCustomCommands($commandName, $args);
        self::$instance = $this;

        $commandName = ucfirst($commandName);
        $commandFile = __DIR__ . "/Commands/$commandName.php";

        if (!file_exists($commandFile)) {
            self::send('Command not found.');

            return;
        }

        require_once $commandFile;

        $commandClass = "MythicalClient\\Cli\\Commands\\$commandName";

        if (!class_exists($commandClass)) {
            self::send('Command not found.');

            return;
        }

        $commandClass::execute($args);
    }

    /**
     * Send a message to the console.
     *
     * @param string $message the message to send
     */
    public function send(string $message): void
    {
        self::sendOutputWithNewLine($this->prefix . $message);
    }

    /**
     * Get the instance of the App.
     */
    public static function getInstance(): App
    {
        return self::$instance;
    }

    /**
     * Custom commands handler.
     *
     * This method is used to handle custom commands that are not part of the CLI.
     *
     * The following commands are handled:
     * - frontend:build
     * - frontend:watch
     * - backend:lint
     *
     * DO NOT REMOVE OR MODIFY THIS METHOD.
     * IF YOU DO NOT KNOW WHAT YOU ARE DOING, DO NOT MODIFY THIS METHOD.
     */
    private function handleCustomCommands(string $cmdName, array $subCmd): void
    {
        if ($cmdName == 'frontend:build') {
            $process = popen('cd frontend && yarn build 2>&1', 'r');
            if (is_resource($process)) {
                while (!feof($process)) {
                    $output = fgets($process);
                    $this->sendOutput($this->prefix . $output);
                }
                $returnVar = pclose($process);
                if ($returnVar !== 0) {
                    $this->sendOutput('Failed to build frontend.');
                    $this->sendOutput("\n");
                } else {
                    $this->sendOutput('Frontend built successfully.');
                    $this->sendOutput("\n");
                }
            } else {
                $this->sendOutput($this->prefix . 'Failed to start build process.');
            }

            exit;
        } elseif ($cmdName == 'frontend:watch') {
            $process = popen('cd frontend && yarn dev 2>&1', 'r');
            if (is_resource($process)) {
                while (!feof($process)) {
                    $output = fgets($process);
                    $this->sendOutput($this->prefix . $output);
                }
                $returnVar = pclose($process);
                if ($returnVar !== 0) {
                    $this->sendOutput('Failed to watch frontend.');
                    $this->sendOutput("\n");
                } else {
                    $this->sendOutput('Frontend is now being watched.');
                    $this->sendOutput("\n");
                }
            } else {
                $this->sendOutput($this->prefix . 'Failed to start watch process.');
            }

            exit;
        } elseif ($cmdName == 'backend:lint') {
            $process = popen('cd backend && export COMPOSER_ALLOW_SUPERUSER=1 && composer run lint 2>&1', 'r');
            if (is_resource($process)) {
                while (!feof($process)) {
                    $output = fgets($process);
                    $this->sendOutput($this->prefix . $output);
                }
                $returnVar = pclose($process);
                if ($returnVar !== 0) {
                    $this->sendOutput('Failed to lint backend.');
                    $this->sendOutput("\n");
                } else {
                    $this->sendOutput('Backend linted successfully.');
                    $this->sendOutput("\n");
                }
            } else {
                $this->sendOutput($this->prefix . 'Failed to start lint process.');
            }
            exit;
        } elseif ($cmdName == 'backend:watch') {
            $process = popen('tail -f backend/storage/logs/mythicalclient.log backend/storage/logs/framework.log', 'r');
            $this->sendOutput('Please wait while we attach to the process...');
            $this->sendOutput(message: "\n");
            sleep(5);
            if (is_resource($process)) {
                $this->sendOutput('Attached to the process.');
                $this->sendOutput(message: "\n");
                while (!feof($process)) {
                    $output = fgets($process);
                    if (strpos($output, '[DEBUG]') !== false) {
                        $this->sendOutput($this->prefix . "\e[34m" . $output . "\e[0m"); // Blue for DEBUG
                    } elseif (strpos($output, '[INFO]') !== false) {
                        $this->sendOutput($this->prefix . "\e[32m" . $output . "\e[0m"); // Green for INFO
                    } elseif (strpos($output, '[WARNING]') !== false) {
                        $this->sendOutput($this->prefix . "\e[33m" . $output . "\e[0m"); // Yellow for WARNING
                    } elseif (strpos($output, '[ERROR]') !== false) {
                        $this->sendOutput($this->prefix . "\e[31m" . $output . "\e[0m"); // Red for ERROR
                    } elseif (strpos($output, '[CRITICAL]') !== false) {
                        $this->sendOutput($this->prefix . "\e[35m" . $output . "\e[0m"); // Magenta for CRITICAL
                    } else {
                        $this->sendOutput($this->prefix . $output);
                    }
                }
                $returnVar = pclose($process);
                if ($returnVar !== 0) {
                    $this->sendOutput('Failed to watch backend.');
                    $this->sendOutput(message: "\n");
                } else {
                    $this->sendOutput('Backend is now being watched.');
                    $this->sendOutput("\n");
                }
            } else {
                $this->sendOutput($this->prefix . 'Failed to start watch process.');
            }
            exit;
        }
    }
}
