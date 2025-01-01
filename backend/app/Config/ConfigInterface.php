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

namespace MythicalClient\Config;

interface ConfigInterface
{
    public const APP_NAME = 'app_name';
    public const APP_LANG = 'app_lang';
    public const APP_URL = 'app_url';
    public const APP_VERSION = 'app_version';
    public const APP_TIMEZONE = 'app_timezone';
    public const APP_LOGO = 'app_logo';
    public const SEO_DESCRIPTION = 'seo_description';
    public const SEO_KEYWORDS = 'seo_keywords';
    public const TURNSTILE_ENABLED = 'turnstile_enabled';
    public const TURNSTILE_KEY_PUB = 'turnstile_key_pub';
    public const TURNSTILE_KEY_PRIV = 'turnstile_key_priv';
    public const SMTP_ENABLED = 'smtp_enabled';
    public const SMTP_HOST = 'smtp_host';
    public const SMTP_PORT = 'smtp_port';
    public const SMTP_USER = 'smtp_user';
    public const SMTP_PASS = 'smtp_pass';
    public const SMTP_FROM = 'smtp_from';
    public const SMTP_ENCRYPTION = 'smtp_encryption';

    /**
     * Legal Values.
     */
    public const LEGAL_TOS = 'legal_tos_url';
    public const LEGAL_PRIVACY = 'legal_privacy_url';

}
