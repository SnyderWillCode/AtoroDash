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

namespace MythicalClient\Logger;

class LoggerFactory
{
    public $logFile;

    public function __construct(string $logFile)
    {
        $this->logFile = $logFile;
        if ($this->doesLogFileExist()) {
            $this->createLogFile();
        }
    }

    public function info(string $message): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        $this->appendLog('[INFO] [' . $caller . '] ' . $message);
    }

    public function warning(string $message): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        $this->appendLog('[WARNING] [' . $caller . '] ' . $message);
    }

    public function error(string $message): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        $this->appendLog('[ERROR] [' . $caller . '] ' . $message);
    }

    public function critical(string $message): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        $this->appendLog('[CRITICAL] [' . $caller . '] ' . $message);
    }

    public function debug(string $message): void
    {
        if (APP_DEBUG == true) {
            $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
            $this->appendLog('[DEBUG] [' . $caller . '] ' . $message);
        }
    }

    private function getFormattedDate(): string
    {
        return date('Y-m-d H:i:s');
    }

    private function appendLog(string $message): void
    {
        file_put_contents($this->logFile, '| (' . $this->getFormattedDate() . ') ' . $message . PHP_EOL, FILE_APPEND);
    }

    private function createLogFile(): void
    {
        if (!$this->doesLogFileExist()) {
            file_put_contents($this->logFile, '');
        }
    }

    private function doesLogFileExist(): bool
    {
        return file_exists($this->logFile);
    }
}
