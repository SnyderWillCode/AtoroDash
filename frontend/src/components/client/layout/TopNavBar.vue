<template>
    <nav class="fixed top-0 left-0 right-0 h-16 bg-gray-900/50 backdrop-blur-xs border-b border-gray-700/50 z-30">
        <div class="h-full px-4 flex items-center justify-between">
            <!-- Left: Logo & Menu Button -->
            <div class="flex items-center gap-3">
                <button class="lg:hidden p-2 hover:bg-gray-800/50 rounded-lg" @click="$emit('toggle-sidebar')">
                    <MenuIcon v-if="!isSidebarOpen" class="w-5 h-5" />
                    <XIcon v-else class="w-5 h-5" />
                </button>

                <div class="flex items-center gap-2">
                    <div class="w-8 h-8 flex items-center justify-center">
                        <img :src="appLogo" alt="MythicalClient" class="h-6 w-6" />
                    </div>
                    <span
                        class="text-xl font-bold bg-linear-to-r from-purple-400 to-purple-600 bg-clip-text text-transparent"
                    >
                        {{ appName }}
                    </span>
                </div>
            </div>

            <!-- Search Bar (Desktop) -->
            <div class="hidden lg:block absolute left-1/2 transform -translate-x-1/2">
                <div class="relative">
                    <SearchIcon class="absolute left-3 top-2.5 h-5 w-5 text-gray-400" />
                    <input
                        type="text"
                        :placeholder="t('components.search.placeholder')"
                        class="px-10 py-2 w-64 bg-gray-800/50 border border-gray-700/50 rounded-lg text-sm focus:outline-hidden focus:ring-2 focus:ring-purple-500/50"
                        @click="$emit('toggle-search')"
                        readonly
                    />
                </div>
            </div>

            <!-- Search Icon (Mobile) -->
            <button class="lg:hidden p-2 hover:bg-gray-800/50 rounded-lg" @click="$emit('toggle-search')">
                <SearchIcon class="w-5 h-5" />
            </button>

            <!-- Right: Actions -->
            <div class="flex items-center gap-2">
                <button @click="$emit('toggle-notifications')" class="p-2 hover:bg-gray-800/50 rounded-lg relative">
                    <BellIcon class="w-5 h-5" />
                    <span class="absolute top-1.5 right-1.5 w-2 h-2 bg-purple-500 rounded-full"></span>
                </button>

                <button @click="$emit('toggle-profile')" class="lg:hidden p-2 hover:bg-gray-800/50 rounded-lg">
                    <UserIcon class="w-5 h-5" />
                </button>

                <button
                    @click="$emit('toggle-profile')"
                    class="hidden lg:flex p-2 hover:bg-gray-800/50 rounded-lg items-center gap-2 relative"
                >
                    <img :src="Session.getInfo('avatar')" alt="Profile" class="w-8 h-8 rounded-full" />
                    <div class="flex flex-col items-start">
                        <span class="text-sm text-gray-300">{{ Session.getInfo('username') }}</span>
                        <span class="text-xs text-gray-400">{{ role }}</span>
                    </div>
                </button>
            </div>
        </div>
    </nav>
</template>

<script setup lang="ts">
import {
    Search as SearchIcon,
    Bell as BellIcon,
    User as UserIcon,
    Menu as MenuIcon,
    X as XIcon,
} from 'lucide-vue-next';
import { useSettingsStore } from '@/stores/settings';
const Settings = useSettingsStore();
import { useI18n } from 'vue-i18n';
import Session from '@/mythicalclient/Session';

const role =
    (Session.getInfo('role_real_name') ?? '').charAt(0).toUpperCase() +
    (Session.getInfo('role_real_name') ?? '').slice(1);
const { t } = useI18n();

defineProps<{
    isSidebarOpen: boolean;
}>();

defineEmits(['toggle-sidebar', 'toggle-search', 'toggle-notifications', 'toggle-profile']);

const appLogo = Settings.getSetting('app_logo');
const appName = Settings.getSetting('app_name');
</script>
