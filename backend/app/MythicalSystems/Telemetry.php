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
