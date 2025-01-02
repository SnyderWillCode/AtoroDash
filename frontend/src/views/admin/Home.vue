<template>
    <LayoutDashboard>
        <h1 class="text-3xl font-bold mb-2 text-pink-400">Welcome to {{ Settings.getSetting('debug_name') }}!</h1>
        <p class="text-gray-400 mb-6">Version: {{ Settings.getSetting('debug_version') }}</p>

        <!-- Dashboard Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6 mb-6">
            <!-- Status Card -->
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-4">
                <div class="flex items-center text-green-400 mb-2">
                    <CheckCircle class="w-5 h-5 mr-2" />
                    <h2 class="font-medium">Your Panel is up to date</h2>
                </div>
                <p class="text-gray-400">You are currently using {{ Settings.getSetting('debug_version') }}</p>
            </div>

            <!-- Support Section -->
            <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-4">
                <div class="flex items-center justify-between mb-4">
                    <div class="flex items-center text-pink-400">
                        <Heart class="w-5 h-5 mr-2" />
                        <h2 class="font-medium text-gray-100">Support {{ Settings.getSetting('debug_name') }}</h2>
                    </div>
                    <a
                        href="https://github.com/sponsors/nayskutzu"
                        class="bg-gradient-to-r from-pink-500 to-violet-500 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:opacity-80"
                    >
                        Donate
                    </a>
                </div>
                <p class="text-gray-400">
                    Your support helps us continue to improve {{ Settings.getSetting('debug_name') }}!
                </p>
            </div>
        </div>
        <!-- Warning Section -->
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-4">
            <div class="flex items-center mb-2">
                <InfoIcon class="w-5 h-5 mr-2" />
                <h2 class="font-medium">Free Edition</h2>
            </div>
            <p class="">
                You are currently using the free edition of MythicalClient. Upgrade to the premium edition for more
                features and support.
            </p>
        </div>
        <br />
        <!-- Help Section -->
        <div class="bg-gray-800/50 backdrop-blur-md rounded-lg p-4">
            <div class="flex items-center justify-between mb-4">
                <div class="flex items-center text-violet-400">
                    <HelpCircle class="w-5 h-5 mr-2" />
                    <h2 class="font-medium text-gray-100">Need Help?</h2>
                </div>
                <a
                    href="https://www.mythical.systems/docs"
                    class="bg-gradient-to-r from-violet-500 to-pink-500 text-white px-4 py-2 rounded-lg transition-all duration-200 hover:opacity-80"
                >
                    Read Docs
                </a>
            </div>
            <p class="text-gray-400">Check out our documentation or join our Discord for assistance!</p>
        </div>
    </LayoutDashboard>
</template>
<script setup lang="ts">
import { CheckCircle, Heart, HelpCircle, InfoIcon } from 'lucide-vue-next';
import LayoutDashboard from '@/components/admin/LayoutDashboard.vue';
import Settings from '@/mythicalclient/Settings';
</script>
