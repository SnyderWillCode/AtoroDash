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

use MythicalClient\App;
use MythicalClient\Config\ConfigInterface;

$router->add('/api/system/settings', function (): void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $settings = [
        ConfigInterface::APP_NAME => $config->getSetting(ConfigInterface::APP_NAME, 'MythicalClient'),
        ConfigInterface::APP_LANG => $config->getSetting(ConfigInterface::APP_LANG, 'en_US'),
        ConfigInterface::APP_URL => $config->getSetting(ConfigInterface::APP_URL, 'framework.mythical.systems'),
        ConfigInterface::APP_VERSION => $config->getSetting(ConfigInterface::APP_VERSION, '1.0.0'),
        ConfigInterface::APP_TIMEZONE => $config->getSetting(ConfigInterface::APP_TIMEZONE, 'UTC'),
        ConfigInterface::APP_LOGO => $config->getSetting(ConfigInterface::APP_LOGO, 'https://github.com/mythicalltd.png'),
        ConfigInterface::SEO_DESCRIPTION => $config->getSetting(ConfigInterface::SEO_DESCRIPTION, 'Change this in the settings area!'),
        ConfigInterface::SEO_KEYWORDS => $config->getSetting(ConfigInterface::SEO_KEYWORDS, 'some,random,keywords'),
        ConfigInterface::TURNSTILE_ENABLED => $config->getSetting(ConfigInterface::TURNSTILE_ENABLED, 'false'),
        ConfigInterface::TURNSTILE_KEY_PUB => $config->getSetting(ConfigInterface::TURNSTILE_KEY_PUB, 'XXXX'),
        ConfigInterface::LEGAL_TOS => $config->getSetting(ConfigInterface::LEGAL_TOS, '/tos'),
        ConfigInterface::LEGAL_PRIVACY => $config->getSetting(ConfigInterface::LEGAL_PRIVACY, '/privacy'),
		
        ConfigInterface::COMPANY_NAME => $config->getSetting(ConfigInterface::COMPANY_NAME, 'MythicalClient'),
        ConfigInterface::COMPANY_ADDRESS => $config->getSetting(ConfigInterface::COMPANY_ADDRESS, '1234 Main St'),
        ConfigInterface::COMPANY_CITY => $config->getSetting(ConfigInterface::COMPANY_CITY, 'MythicalCity'),
        ConfigInterface::COMPANY_STATE => $config->getSetting(ConfigInterface::COMPANY_STATE, 'MythicalState'),
        ConfigInterface::COMPANY_ZIP => $config->getSetting(ConfigInterface::COMPANY_ZIP, '12345'),
        ConfigInterface::COMPANY_COUNTRY => $config->getSetting(ConfigInterface::COMPANY_COUNTRY, 'MythicalCountry'),
        ConfigInterface::COMPANY_VAT => $config->getSetting(ConfigInterface::COMPANY_VAT, '1234567890'),
    ];

    App::OK('Sure here are the settings you were looking for', ['settings' => $settings]);
});
