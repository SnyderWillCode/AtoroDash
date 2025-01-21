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

    public function warning(string $message, bool $sendTelemetry = false): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        if ($sendTelemetry) {
            $eventID = \Sentry\captureMessage($message, \Sentry\Severity::warning(), null);
        }
        $this->appendLog('[WARNING] (' . $eventID . ')[' . $caller . '] ' . $message);
    }

    public function error(string $message, bool $sendTelemetry = false): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        if ($sendTelemetry) {
            $eventID = \Sentry\captureMessage($message, \Sentry\Severity::error(), null);
        }
        $this->appendLog('[ERROR] (' . $eventID . ') [' . $caller . '] ' . $message);
    }

    public function critical(string $message, bool $sendTelemetry = false): void
    {
        $caller = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 2)[1]['class'] ?? 'unknown';
        if ($sendTelemetry) {

            $eventID = \Sentry\captureMessage($message, \Sentry\Severity::fatal(), null);
        }
        $this->appendLog('[CRITICAL] (' . $eventID . ') [' . $caller . '] ' . $message);
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
