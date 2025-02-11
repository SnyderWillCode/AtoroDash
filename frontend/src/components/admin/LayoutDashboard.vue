<!-- eslint-disable @typescript-eslint/no-explicit-any -->
<template>
    <div class="min-h-screen bg-gray-900 text-gray-100 font-sans">
        <!-- Mobile Menu Button -->
        <button
            @click="isSidebarOpen = !isSidebarOpen"
            class="lg:hidden fixed top-4 left-4 z-50 p-2 bg-gray-800/50 rounded-full backdrop-blur-xs"
        >
            <Menu v-if="!isSidebarOpen" class="w-6 h-6 text-pink-400" />
            <X v-else class="w-6 h-6 text-pink-400" />
        </button>

        <!-- Sidebar -->
        <aside
            :class="[
                'fixed inset-y-0 left-0 z-40 w-64 transition-transform duration-300 ease-in-out transform',
                isSidebarOpen ? 'translate-x-0' : '-translate-x-full',
                'lg:translate-x-0 bg-gray-800/50 backdrop-blur-md',
            ]"
        >
            <div class="p-6">
                <h1 class="text-2xl font-bold bg-linear-to-r from-pink-500 to-violet-500 bg-clip-text text-transparent">
                    <span class="block text-lg">{{ Settings.getSetting('debug_name') }}</span>
                    <span class="block text-sm text-gray-400">{{ Settings.getSetting('debug_version') }}</span>
                </h1>
            </div>
            <nav class="p-6">
                <div v-for="(menuGroup, index) in menuGroups" :key="index" class="mb-6">
                    <h3 class="text-sm uppercase tracking-wider text-gray-400 mb-4">{{ menuGroup.title }}</h3>
                    <ul class="space-y-2">
                        <li v-for="item in menuGroup.items" :key="item.name">
                            <!-- Menu item with submenu -->
                            <template v-if="item.subMenu">
                                <div
                                    @click="toggleSubmenu(item)"
                                    class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 hover:bg-gray-700/50 cursor-pointer"
                                >
                                    <component :is="item.icon" class="w-5 h-5 mr-3 text-pink-400" />
                                    <span>{{ item.name }}</span>
                                    <ChevronDown
                                        class="w-4 h-4 ml-auto transition-transform duration-200"
                                        :class="{ 'rotate-180': item.isOpen }"
                                    />
                                    <span
                                        v-if="'count' in item"
                                        class="ml-2 text-xs bg-violet-500 text-white px-2 py-1 rounded-full"
                                    >
                                        {{ item.count }}
                                    </span>
                                </div>
                                <ul v-if="item.isOpen" class="mt-2 ml-4 space-y-2">
                                    <li v-for="subItem in item.subMenu" :key="subItem.name">
                                        <RouterLink
                                            :to="subItem.path || ''"
                                            class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 hover:bg-gray-700/50"
                                            :class="{ 'bg-gray-700/50': route.path === subItem.path }"
                                        >
                                            <component :is="subItem.icon" class="w-5 h-5 mr-3 text-pink-400" />
                                            <span>{{ subItem.name }}</span>
                                            <span
                                                v-if="'count' in subItem"
                                                class="ml-auto text-xs bg-violet-500 text-white px-2 py-1 rounded-full"
                                            >
                                                {{ subItem.count }}
                                            </span>
                                        </RouterLink>
                                    </li>
                                </ul>
                            </template>
                            <!-- Regular menu item -->
                            <RouterLink
                                v-else
                                :to="item.path || ''"
                                class="flex items-center px-4 py-2 rounded-lg transition-all duration-200 hover:bg-gray-700/50"
                                :class="{ 'bg-gray-700/50': item.active }"
                            >
                                <component :is="item.icon" class="w-5 h-5 mr-3 text-pink-400" />
                                <span>{{ item.name }}</span>
                                <span
                                    v-if="'count' in item"
                                    class="ml-auto text-xs bg-violet-500 text-white px-2 py-1 rounded-full"
                                >
                                    {{ item.count }}
                                </span>
                            </RouterLink>
                        </li>
                    </ul>
                </div>
            </nav>
        </aside>

        <div class="lg:ml-64 min-h-screen flex flex-col">
            <!-- Top Navigation -->
            <header class="bg-gray-800/50 backdrop-blur-md p-4 flex items-center justify-between">
                <div class="relative w-full max-w-xl">
                    <input
                        v-model="searchQuery"
                        type="search"
                        placeholder="Search..."
                        class="w-full bg-gray-700/50 text-gray-100 placeholder-gray-400 rounded-full py-2 pl-10 pr-4 focus:outline-hidden focus:ring-2 focus:ring-pink-500"
                        @focus="isSearchFocused = true"
                        @blur="handleSearchBlur"
                    />
                    <Search class="absolute left-3 top-1/2 transform -translate-y-1/2 text-gray-400 w-5 h-5" />

                    <!-- Search Results Dropdown -->
                    <div
                        v-if="isSearchFocused && filteredResults.length > 0"
                        class="absolute z-10 w-full mt-2 bg-gray-800/90 backdrop-blur-md rounded-lg shadow-xl max-h-60 overflow-y-auto"
                    >
                        <a
                            v-for="result in filteredResults"
                            :key="result.id"
                            :href="result.path"
                            class="block px-4 py-2 hover:bg-gray-700/50 transition-colors duration-200"
                            @mousedown.prevent="handleResultClick(result)"
                        >
                            {{ result.name }}
                        </a>
                    </div>
                </div>

                <div class="relative ml-4">
                    <button
                        @click="isProfileOpen = !isProfileOpen"
                        class="flex items-center space-x-2 focus:outline-hidden"
                    >
                        <img :src="Session.getInfo('avatar')" alt="User Avatar" class="w-8 h-8 rounded-full" />
                        <ChevronDown class="w-4 h-4 text-gray-400" :class="{ 'rotate-180': isProfileOpen }" />
                    </button>
                    <div
                        v-if="isProfileOpen"
                        class="absolute right-0 mt-2 w-48 bg-gray-800/90 backdrop-blur-md rounded-lg shadow-xl py-1 animate-fadeIn"
                    >
                        <RouterLink
                            v-for="item in profileMenu"
                            :key="item.name"
                            :to="item.path"
                            class="block px-4 py-2 hover:bg-gray-700/50 transition-colors duration-200"
                        >
                            {{ item.name }}
                        </RouterLink>
                    </div>
                </div>
            </header>

            <!-- Main Content -->
            <main class="grow p-6 overflow-y-auto">
                <div class="p-3">
                    <slot></slot>
                </div>
            </main>

            <!-- Footer -->
            <footer class="bg-gray-800/50 backdrop-blur-md py-4 px-6">
                <div class="flex flex-col md:flex-row justify-between items-center">
                    <p class="text-gray-400 text-sm mb-2 md:mb-0">© 2023 MythicalClient. All rights reserved.</p>
                    <div class="flex space-x-4">
                        <a
                            v-for="link in footerLinks"
                            :key="link.name"
                            :href="link.path"
                            class="text-gray-400 hover:text-pink-400 text-sm transition-colors duration-200"
                        >
                            {{ link.name }}
                        </a>
                    </div>
                </div>
            </footer>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, computed } from 'vue';
import {
    LayoutDashboard,
    Users,
    Database,
    HardDrive,
    Search,
    ChevronDown,
    Menu,
    X,
    PaperclipIcon,
    InfoIcon,
    PlusCircle,
    SettingsIcon,
} from 'lucide-vue-next';
import Session from '@/mythicalclient/Session';
import StorageMonitor from '@/mythicalclient/StorageMonitor';
import { useSettingsStore } from '@/stores/settings';
const Settings = useSettingsStore();
const router = useRouter();

new StorageMonitor();

if (!Session.isSessionValid()) {
    router.push('/auth/login');
}

try {
    Session.startSession();
} catch (error) {
    console.error('Session failed:', error);
}

if (Session.getInfo('role') == '1' && Session.getInfo('role') == '2') {
    router.push('/dashboard');
}

// Existing refs here

const handleResultClick = (result: { id: number; name: string; path: string }) => {
    searchQuery.value = '';
    console.log(`Navigating to ${result.path}`);
    isSearchFocused.value = false;
};

const toggleSubmenu = (item: MenuItem) => {
    item.isOpen = !item.isOpen;
    menuGroups.value = [...menuGroups.value];
};

const isSidebarOpen = ref(false);
const isProfileOpen = ref(false);
const searchQuery = ref('');
const isSearchFocused = ref(false);

const handleSearchBlur = () => {
    setTimeout(() => {
        isSearchFocused.value = false;
    }, 200);
};

import { useRoute, useRouter } from 'vue-router';
import Dashboard from '@/mythicalclient/admin/Dashboard';

const dashBoard = ref({ count: { user_count: 0, invoices_count: 0, tickets_count: 0 } });
Dashboard.get().then((data) => {
    dashBoard.value = data;
});

const route = useRoute();
const adminBaseUri = '/mc-admin';

interface MenuItem {
    name: string;
    path?: string;
    icon: unknown;
    active?: boolean;
    count?: unknown;
    subMenu?: MenuItem[];
    isOpen?: boolean;
}

const menuGroups = ref<{ title: string; items: MenuItem[] }[]>([
    {
        title: 'Main Menu',
        items: [
            {
                name: 'Dashboard',
                path: `${adminBaseUri}`,
                icon: LayoutDashboard,
                active: route.path === `${adminBaseUri}`,
            },
        ],
    },
    {
        title: 'Management',
        items: [
            {
                name: 'Users',
                path: `${adminBaseUri}/users`,
                icon: Users,
                count: computed(() => dashBoard.value.count.user_count || 0),
                active: route.path === `${adminBaseUri}/users`,
            },
            {
                name: 'Invoices',
                icon: PaperclipIcon,
                subMenu: [
                    {
                        name: 'All Invoices',
                        path: `${adminBaseUri}/invoices`,
                        icon: PaperclipIcon,
                    },
                    {
                        name: 'Create Invoice',
                        path: `${adminBaseUri}/invoices/create`,
                        icon: PlusCircle,
                    },
                ],
                active: route.path === `${adminBaseUri}/invoices`,
                count: computed(() => dashBoard.value.count.invoices_count || 0),
            },
            {
                name: 'Tickets',
                path: `${adminBaseUri}/tickets`,
                icon: InfoIcon,
                active: route.path === `${adminBaseUri}/tickets`,
                count: computed(() => dashBoard.value.count.tickets_count || 0),
            },
        ],
    },
    {
        title: 'Advanced',
        items: [
            {
                name: 'Settings',
                path: `${adminBaseUri}/settings`,
                icon: SettingsIcon,
                active: route.path === `${adminBaseUri}/settings`,
            },
            {
                name: 'Shard Hosts',
                path: `${adminBaseUri}/database-hosts`,
                icon: Database,
                active: route.path === `${adminBaseUri}/database-hosts`,
            },
            {
                name: 'Addons',
                path: `${adminBaseUri}/mounts`,
                icon: HardDrive,
                active: route.path === `${adminBaseUri}/mounts`,
            },
        ],
    },
]);

const profileMenu = [
    { name: 'Profile', path: '/account' },
    { name: 'Exit Admin', path: '/dashboard' },
    { name: 'Sign out', path: '/auth/logout' },
];

const footerLinks = [
    { name: 'Privacy Policy', path: '#' },
    { name: 'Terms of Service', path: '#' },
    { name: 'Contact Us', path: '#' },
];

const searchResults = [
    { id: 1, name: 'Dashboard', path: '/dashboard' },
    { id: 2, name: 'Users', path: '/users' },
    { id: 3, name: 'Settings', path: '/settings' },
    { id: 4, name: 'API Keys', path: '/api-keys' },
    { id: 5, name: 'Shard Hosts', path: '/database-hosts' },
    { id: 6, name: 'Addons', path: '/mounts' },
];

const filteredResults = computed(() => {
    if (!searchQuery.value) return [];
    return searchResults.filter((result) => result.name.toLowerCase().includes(searchQuery.value.toLowerCase()));
});
</script>

<style>
@import url('https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&display=swap');

body {
    font-family: 'Inter', sans-serif;
}

.animate-fadeIn {
    animation: fadeIn 0.2s ease-out;
}

@keyframes fadeIn {
    from {
        opacity: 0;
        transform: translateY(-10px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}

/* Custom scrollbar styles */
::-webkit-scrollbar {
    width: 8px;
}

::-webkit-scrollbar-track {
    background: rgba(255, 255, 255, 0.1);
}

::-webkit-scrollbar-thumb {
    background: rgba(255, 255, 255, 0.3);
    border-radius: 4px;
}

::-webkit-scrollbar-thumb:hover {
    background: rgba(255, 255, 255, 0.5);
}

/* Glassmorphism effect */
.backdrop-blur-md {
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
}

/* Gradient text effect */
.bg-gradient-text {
    background-clip: text;
    -webkit-background-clip: text;
    color: transparent;
}

/* Smooth transitions */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 150ms;
}

/* Hover effects */
.hover\:opacity-80:hover {
    opacity: 0.8;
}

.hover\:bg-gray-700\/50:hover {
    background-color: rgba(55, 65, 81, 0.5);
}

/* Responsive layout adjustments */
@media (max-width: 1023px) {
    .lg\:ml-64 {
        margin-left: 0;
    }
}
</style>
