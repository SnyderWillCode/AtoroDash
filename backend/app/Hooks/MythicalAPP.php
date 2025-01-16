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

namespace MythicalClient\Hooks;

class MythicalAPP extends \MythicalSystems\Api\Api
{
    /**
     * Return a 200 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function OK(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(200, null, $message, true, $extraContent);
    }

    /**
     * Return a 201 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function Created(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(201, 'The request has been fulfilled and a new resource has been created.', $message, true, $extraContent);
    }

    /**
     * Return a 204 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function NoContent(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(204, 'The server has successfully fulfilled the request and there is no content to send in the response.', $message, true, $extraContent);
    }

    /**
     * Return a 400 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function BadRequest(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(400, 'The server cannot process the request due to a client error (e.g., malformed syntax).', $message, false, $extraContent);
    }

    /**
     * Return a 401 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function Unauthorized(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(401, 'The client must authenticate itself to get the requested response.', $message, false, $extraContent);
    }

    /**
     * Return a 403 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function Forbidden(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(403, 'The server understood the request, but refuses to authorize it.', $message, false, $extraContent);
    }

    /**
     * Return a 404 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function NotFound(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(404, 'The requested resource could not be found on the server.', $message, false, $extraContent);
    }

    /**
     * Return a 405 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function MethodNotAllowed(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(405, 'The method specified in the request is not allowed for the resource identified by the request URI.', $message, false, $extraContent);
    }

    /**
     * Return a 500 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function InternalServerError(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(500, 'A generic error message, given when an unexpected condition was encountered and no more specific message is suitable.', $message, false, $extraContent);
    }

    /**
     * Return a 502 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function BadGateway(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(502, 'The server, while acting as a gateway or proxy, received an invalid response from the upstream server it accessed in attempting to fulfill the request.', $message, false, $extraContent);
    }

    /**
     * Return a 503 response.
     *
     * @return void Returns a void so nothing it will die!
     */
    public static function ServiceUnavailable(?string $message, ?array $extraContent): void
    {
        self::sendManualResponse(503, 'The server is currently unable to handle the request due to temporary overloading or maintenance of the server.', $message, false, $extraContent);
    }

    /**
     * Send a manual response.
     *
     * @param int $code The HTTP status code
     * @param string|null $error The error message
     * @param string|null $message The message
     * @param bool|null $success If the request was successful
     * @param array|null $extraContent Extra content to send
     */
    public static function sendManualResponse(int $code, ?string $error, ?string $message, ?bool $success, ?array $extraContent): void
    {
        $response = [
            'code' => $code,
            'error' => $error,
            'message' => $message,
            'success' => $success,
            'core' => [
                'debug_os' => SYSTEM_OS_NAME,
                'debug_os_kernel' => SYSTEM_KERNEL_NAME,
                'debug_name' => 'MythicalClient',
                'debug_debug' => APP_DEBUG,
                'debug_version' => APP_VERSION,
                'debug_telemetry' => TELEMETRY,
                'debug' => [
                    'useRedis' => REDIS_ENABLED,
                    'rateLimit' => [
                        'enabled' => REDIS_ENABLED,
                        'limit' => RATE_LIMIT,
                    ],
                ],
            ],
        ];

        if (!$extraContent == null) {
            $response = array_merge($response, $extraContent);
        }

        http_response_code($code);
        exit(json_encode($response, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES));
    }
}
