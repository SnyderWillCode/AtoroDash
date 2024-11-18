<?php

use MythicalDash\App;
use MythicalDash\Config\ConfigFactory;
use MythicalDash\Config\ConfigInterface;

$router->add('/api/system/settings', function () : void {
    App::init();
    $appInstance = App::getInstance(true);
    $config = $appInstance->getConfig();

    $settings = [
        ConfigInterface::APP_NAME => $config->getSetting(ConfigInterface::APP_NAME, "MythicalDash"),
        ConfigInterface::APP_LANG => $config->getSetting(ConfigInterface::APP_LANG, "en_US"),
        ConfigInterface::APP_URL => $config->getSetting(ConfigInterface::APP_URL, "framework.mythical.systems"),
        ConfigInterface::APP_VERSION => $config->getSetting(ConfigInterface::APP_VERSION, "1.0.0"),
        ConfigInterface::APP_TIMEZONE => $config->getSetting(ConfigInterface::APP_TIMEZONE, "UTC"),
        ConfigInterface::APP_LOGO => $config->getSetting(ConfigInterface::APP_LOGO, "https://github.com/mythicalltd.png"),
        ConfigInterface::SEO_DESCRIPTION => $config->getSetting(ConfigInterface::SEO_DESCRIPTION, "Change this in the settings area!"),
        ConfigInterface::SEO_KEYWORDS => $config->getSetting(ConfigInterface::SEO_KEYWORDS, "some,random,keywords"),
        ConfigInterface::TURNSTILE_ENABLED => $config->getSetting(ConfigInterface::TURNSTILE_ENABLED, "false"),
        ConfigInterface::TURNSTILE_KEY_PUB => $config->getSetting(ConfigInterface::TURNSTILE_KEY_PUB, "XXXX"),
        ConfigInterface::LEGAL_TOS => $config->getSetting(ConfigInterface::LEGAL_TOS, "/tos"),
        ConfigInterface::LEGAL_PRIVACY => $config->getSetting(ConfigInterface::LEGAL_PRIVACY, "/privacy"),
    ];

    App::OK('Sure here are the settings you were looking for', ["settings"=>$settings]);
});