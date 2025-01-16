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

namespace MythicalClient\Chat;

use MythicalClient\App;

class Billing extends Database
{
    public const TABLE_NAME = 'mythicalclient_billing';

    public static function updateBilling(
        string $uuid,
        ?string $company_name,
        ?string $vat_number,
        ?string $address1,
        ?string $address2,
        ?string $city,
        ?string $country,
        ?string $state,
        ?string $postcode,
    ): void {
        $conn = self::getPdoConnection();
        if (self::doesHaveBilling($uuid)) {
            $stmt = $conn->prepare('UPDATE ' . self::TABLE_NAME . ' SET company_name = :company_name, vat_number = :vat_number, address1 = :address1, address2 = :address2, city = :city, country = :country, state = :state, postcode = :postcode WHERE user = :uuid');
        } else {
            $stmt = $conn->prepare('INSERT INTO ' . self::TABLE_NAME . ' (user, company_name, vat_number, address1, address2, city, country, state, postcode) VALUES (:uuid, :company_name, :vat_number, :address1, :address2, :city, :country, :state, :postcode)');
        }
        $company_name = $company_name !== null ? App::getInstance(true)->encrypt($company_name) : null;
        $vat_number = $vat_number !== null ? App::getInstance(true)->encrypt($vat_number) : null;
        $address1 = $address1 !== null ? App::getInstance(true)->encrypt($address1) : null;
        $address2 = $address2 !== null ? App::getInstance(true)->encrypt($address2) : null;
        $city = $city !== null ? App::getInstance(true)->encrypt($city) : null;
        $country = $country !== null ? App::getInstance(true)->encrypt($country) : null;
        $state = $state !== null ? App::getInstance(true)->encrypt($state) : null;
        $postcode = $postcode !== null ? App::getInstance(true)->encrypt($postcode) : null;

        $stmt->execute([
            'uuid' => $uuid,
            'company_name' => $company_name,
            'vat_number' => $vat_number,
            'address1' => $address1,
            'address2' => $address2,
            'city' => $city,
            'country' => $country,
            'state' => $state,
            'postcode' => $postcode,
        ]);
    }

    public static function getBillingData(string $uuid): array
    {
        if (!self::doesHaveBilling($uuid)) {
            return [
                'company_name' => 'N/A',
                'vat_number' => 'N/A',
                'address1' => 'N/A',
                'address2' => 'N/A',
                'city' => 'N/A',
                'country' => 'N/A',
                'state' => 'N/A',
                'postcode' => 'N/A',
            ];
        }
        $conn = self::getPdoConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE user = :uuid');
        $stmt->execute([
            'uuid' => $uuid,
        ]);
        $result = $stmt->fetch();
        if ($result !== false) {
            return [
                'company_name' => $result['company_name'] !== null ? App::getInstance(true)->decrypt($result['company_name']) : 'N/A',
                'vat_number' => $result['vat_number'] !== null ? App::getInstance(true)->decrypt($result['vat_number']) : 'N/A',
                'address1' => $result['address1'] !== null ? App::getInstance(true)->decrypt($result['address1']) : 'N/A',
                'address2' => $result['address2'] !== null ? App::getInstance(true)->decrypt($result['address2']) : 'N/A',
                'city' => $result['city'] !== null ? App::getInstance(true)->decrypt($result['city']) : 'N/A',
                'country' => $result['country'] !== null ? App::getInstance(true)->decrypt($result['country']) : 'N/A',
                'state' => $result['state'] !== null ? App::getInstance(true)->decrypt($result['state']) : 'N/A',
                'postcode' => $result['postcode'] !== null ? App::getInstance(true)->decrypt($result['postcode']) : 'N/A',
            ];
        }

        return [];

    }

    private static function doesHaveBilling(string $uuid): bool
    {
        $conn = self::getPdoConnection();
        $stmt = $conn->prepare('SELECT * FROM ' . self::TABLE_NAME . ' WHERE user = :uuid');
        $stmt->execute([
            'uuid' => $uuid,
        ]);
        $result = $stmt->fetch();

        return $result !== false;
    }
}
