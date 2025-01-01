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

class PluginEvents
{
    public static $_instance;
    /**
     * All listeners for the specified event.
     */
    protected array $listeners = [];

    /**
     * Returns the current instance of the PluginEvent class.
     */
    public static function getInstance(): PluginEvents
    {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * Adds a listener for the specified event.
     *
     * @param string $event the name of the event
     * @param callable $listener the listener function to be added
     *
     * @return static returns the current instance of the PluginEvent class
     */
    public function on(string $event, callable $listener): static
    {
        if (!isset($this->listeners[$event])) {
            $this->listeners[$event] = [];
        }

        $this->listeners[$event][] = $listener;

        return $this;
    }

    /**
     * Removes a listener for the specified event.
     *
     * @param string $event the name of the event
     * @param callable $listener the listener function to be removed
     */
    public function removeListener(string $event, callable $listener): void
    {
        if (isset($this->listeners[$event])) {
            $index = array_search($listener, $this->listeners[$event], true);

            if ($index !== false) {
                unset($this->listeners[$event][$index]);

                if (count($this->listeners[$event]) === 0) {
                    unset($this->listeners[$event]);
                }
            }
        }
    }

    /**
     * Removes all listeners for the specified event or all events if no event is specified.
     *
     * @param string|null $event the name of the event (optional)
     */
    public function removeAllListeners(?string $event = null): void
    {
        if ($event !== null) {
            unset($this->listeners[$event]);
        } else {
            $this->listeners = [];
        }
    }

    /**
     * Removes all listeners for the specified event or all events if no event is specified.
     *
     * @param string|null $event the name of the event (optional)
     */
    public function listeners(?string $event = null): array
    {
        if ($event === null) {
            $events = [];
            $eventNames = array_unique(
                array_merge(
                    array_keys($this->listeners),
                )
            );

            foreach ($eventNames as $eventName) {
                $events[$eventName] = array_merge(
                    $this->listeners[$eventName] ?? [],
                );
            }

            return $events;
        }

        return array_merge(
            $this->listeners[$event] ?? [],
        );
    }

    /**
     * Emits the specified event and triggers all associated listeners.
     *
     * @param string $event the name of the event
     * @param array $arguments the arguments to be passed to the listeners (optional)
     */
    public function emit(string $event, array $arguments = []): void
    {
        $listeners = [];
        if (isset($this->listeners[$event])) {
            $listeners = array_values($this->listeners[$event]);
        }

        if ($listeners !== []) {
            foreach ($listeners as $listener) {
                $listener(...$arguments);
            }
        }
    }
}
