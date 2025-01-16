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

namespace MythicalClient\FastChat;

use Predis\Client;
use MythicalClient\App;

class Redis
{
    private $redis;

    public function __construct()
    {
        $app = App::getInstance(true);
        $app->loadEnv();
        $host = $_ENV['REDIS_HOST'];
        $pwd = $_ENV['REDIS_PASSWORD'];
        $client = new Client([
            'scheme' => 'tcp',
            'host' => $host,
        ]);
        $this->redis = $client;
    }

    public function getRedis(): Client
    {
        return $this->redis;
    }

    public function testConnection(): bool
    {
        try {
            $redis = $this->getRedis();
            $redis->connect();

            return $redis->isConnected();
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->error('Failed to connect to Redis: ' . $e->getMessage());

            return false;
        }
    }
}
