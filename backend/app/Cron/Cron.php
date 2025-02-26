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

namespace MythicalClient\Cron;

class Cron
{
    /**
     * @var array Supported time units and their minute equivalents
     */
    private const TIME_UNITS = [
        'S' => 1,        // Seconds
        'M' => 1,        // Minutes
        'H' => 60,       // Hours
        'D' => 1440,     // Days
        'W' => 10080,    // Weeks
    ];

    /**
     * @var string The unique identifier for this cron job
     */
    private string $identifier;

    /**
     * @var int Minutes between executions
     */
    private int $interval;

    /**
     * @var string Path to store last run timestamps
     */
    private string $storageFile;

    /**
     * Initialize a new Cron instance.
     *
     * @param string $identifier Unique identifier for this cron job
     * @param string $interval Interval string (e.g., "30M", "1H", "1D")
     *
     * @throws \Exception If interval format is invalid
     */
    public function __construct(string $identifier, string $interval)
    {
        $this->identifier = $identifier;
        $this->interval = $this->parseInterval($interval);
        $this->storageFile = $this->getStoragePath();

        // Ensure storage directory exists
        if (!is_dir(dirname($this->storageFile))) {
            mkdir(dirname($this->storageFile), 0755, true);
        }
    }

    /**
     * Check if the cron job should run.
     */
    public function shouldRun(): bool
    {
        if (!file_exists($this->storageFile)) {
            return true;
        }

        $lastRun = (int) file_get_contents($this->storageFile);
        $minutesSinceLastRun = (time() - $lastRun) / 60;

        return $minutesSinceLastRun >= $this->interval;
    }

    /**
     * Mark the cron job as run.
     */
    public function markAsRun(): void
    {
        file_put_contents($this->storageFile, time());
    }

    /**
     * Get the last run time.
     */
    public function getLastRunTime(): ?\DateTime
    {
        if (!file_exists($this->storageFile)) {
            return null;
        }

        $timestamp = (int) file_get_contents($this->storageFile);

        return (new \DateTime())->setTimestamp($timestamp);
    }

    /**
     * Get the next scheduled run time.
     */
    public function getNextRunTime(): \DateTime
    {
        $lastRun = $this->getLastRunTime() ?? new \DateTime();
        $nextRun = clone $lastRun;

        return $nextRun->modify("+{$this->interval} minutes");
    }

    /**
     * Execute the cron job if it's due.
     *
     * @param callable $callback Function to execute
     *
     * @return bool Whether the job was executed
     */
    public function runIfDue(callable $callback): bool
    {
        if (!$this->shouldRun()) {
            return false;
        }

        try {
            $callback();
            $this->markAsRun();

            return true;
        } catch (\Exception $e) {
            // Log the error but don't mark as run
            error_log("Cron job {$this->identifier} failed: " . $e->getMessage());

            return false;
        }
    }

    /**
     * Parse the interval string into minutes.
     *
     * @param string $interval Interval string (e.g., "30M", "1H", "1D")
     *
     * @throws \Exception If interval format is invalid
     *
     * @return int Minutes
     */
    private function parseInterval(string $interval): int
    {
        if (!preg_match('/^(\d+)([MHDWS])$/', $interval, $matches)) {
            throw new \Exception('Invalid interval format. Use number followed by M (minutes), H (hours), D (days), W (weeks), or S (seconds). Example: 30M');
        }

        $value = (int) $matches[1];
        $unit = $matches[2];

        if (!isset(self::TIME_UNITS[$unit])) {
            throw new \Exception('Invalid time unit. Use M, H, D, W, or S');
        }

        return $value * self::TIME_UNITS[$unit];
    }

    /**
     * Get the storage path for this cron's last run time.
     */
    private function getStoragePath(): string
    {
        return APP_CACHE_DIR . "/cron/{$this->identifier}.myc";
    }
}
