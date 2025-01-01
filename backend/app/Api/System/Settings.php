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
    ];

    App::OK('Sure here are the settings you were looking for', ['settings' => $settings]);
});
