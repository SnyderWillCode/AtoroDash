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

namespace MythicalClient\MythicalSystems;

use MythicalClient\App;

class Telemetry implements TelemetryCollection
{
    public static function send(TelemetryCollection|string $telemetryCollection): void
    {
        try {
            App::getInstance(true)->getLogger()->debug('Sending telemetry data: ' . $telemetryCollection);
            $url = sprintf(
                'https://api.mythicalsystems.xyz/telemetry?authKey=%s&project=%s&action=%s&osName=%s&kernelName=%s&cpuArchitecture=%s&osArchitecture=%s',
                'AxWTnecj85SI4bG6rIP8bvw2uCF7W5MmkJcQIkrYS80MzeTraQWyICL690XOio8F',
                'mythicalclient',
                urlencode((string) $telemetryCollection),
                urlencode(SYSTEM_OS_NAME),
                urlencode(SYSTEM_KERNEL_NAME),
                'amd',
                '64'
            );

            $curl = curl_init();
            curl_setopt_array($curl, [
                CURLOPT_URL => $url,
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_ENCODING => '',
                CURLOPT_MAXREDIRS => 10,
                CURLOPT_TIMEOUT => 10,
                CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
                CURLOPT_CUSTOMREQUEST => 'GET',
                CURLOPT_POSTFIELDS => '',
                CURLOPT_HTTPHEADER => [
                    'Content-Type: application/json',
                    'User-Agent: mythicalclient/' . APP_VERSION,
                ],
            ]);

            curl_exec($curl);
            curl_close($curl);
        } catch (\Exception $e) {
            App::getInstance(true)->getLogger()->debug('Failed to send telemetry data: ' . $e->getMessage());
            // No one cares!
        }
    }
}
