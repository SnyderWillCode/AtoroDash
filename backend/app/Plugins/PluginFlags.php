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

namespace MythicalClient\Plugins;

class PluginFlags
{
    /**
     * Get the flags.
     */
    public static function getFlags(): array
    {
        return [
            'ignorePlaceholders',
            'hasInstallScript',
            'hasRemovalScript',
            'hasUpdateScript',
            'developerIgnoreInstallScript',
            'developerEscalateInstallScript',
        ];
    }

    /**
     * Check if the flags are valid.
     *
     * @param array $flags The flags
     */
    public static function validFlags(array $flags): bool
    {
        $app = \MythicalClient\App::getInstance(true);
        try {
            $app->getLogger()->debug('Processing plugin flags');
            $flagList = PluginFlags::getFlags();
            foreach ($flagList as $flag) {
                if (in_array($flag, $flags)) {
                    $app->getLogger()->debug('Valid flag: ' . $flag);

                    return true;
                }
            }
            $app->getLogger()->debug('Invalid flags: ' . implode(', ', $flags));

            return false;
        } catch (\Exception $e) {
            $app->getLogger()->error('Failed to validate flags: ' . $e->getMessage());

            return false;
        }
    }
}
