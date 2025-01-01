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
