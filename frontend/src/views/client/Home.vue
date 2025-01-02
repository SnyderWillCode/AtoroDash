<template>
    <LayoutDashboard>
        <div class="p-6 space-y-6">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-white mb-2">My Dashboard</h1>
                    <div class="text-purple-200 text-sm">
                        <RouterLink to="/" class="hover:text-white transition-colors">Portal Home</RouterLink>
                        <span class="mx-2">/</span>
                        <span>Client Area</span>
                    </div>
                </div>
                <div class="flex items-center space-x-4">
                    <div class="text-right">
                        <p class="text-purple-200 text-sm">Welcome back,</p>
                        <p class="text-white font-semibold">{{ Session.getInfo('first_name') }}</p>
                    </div>
                    <img
                        :src="`${Session.getInfo('avatar')}?height=40&width=40`"
                        alt="Profile"
                        class="w-10 h-10 rounded-full border-2 border-purple-500"
                    />
                </div>
            </div>

            <div class="grid gap-6 lg:grid-cols-4">
                <!-- Left Column -->
                <div class="space-y-6">
                    <!-- Support PIN -->
                    <CardComponent>
                        <h2 class="text-purple-200 text-sm font-medium mb-2">Support Pin</h2>
                        <div class="flex items-center gap-2">
                            <span class="text-emerald-400 text-2xl font-mono font-bold">637238</span>
                            <button class="text-purple-500 hover:text-white transition-colors">
                                <RefreshCcwIcon class="w-4 h-4" />
                            </button>
                        </div>
                    </CardComponent>

                    <!-- Profile Card -->
                    <CardComponent>
                        <div class="flex flex-col items-center text-center">
                            <MarketIcon class="w-20 h-20 text-purple-500 mb-4" />
                            <div class="text-xl text-white mb-2">
                                {{ Session.getInfo('company_name') }}
                            </div>
                            <div class="text-purple-200 text-sm space-y-1 mb-4">
                                <div>{{ Session.getInfo('vat_number') }}</div>
                                <div>{{ Session.getInfo('address1') }}</div>
                                <div>
                                    {{ Session.getInfo('city') }} ({{ Session.getInfo('postcode') }}),
                                    {{ Session.getInfo('country') }}
                                </div>
                            </div>
                            <div class="flex gap-2 w-full">
                                <RouterLink
                                    to="/account"
                                    class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-sm"
                                >
                                    Update
                                </RouterLink>
                                <a
                                    href="/api/auth/logout"
                                    class="flex-1 px-4 py-2 bg-purple-800 hover:bg-purple-700 text-white rounded transition-colors text-sm"
                                >
                                    Logout
                                </a>
                            </div>
                        </div>
                    </CardComponent>

                    <!-- Billing Summary -->
                    <CardComponent>
                        <h2 class="text-white text-lg font-semibold mb-4">Billing Summary</h2>
                        <div class="space-y-2 text-sm">
                            <div class="flex justify-between">
                                <span class="text-purple-200">Current Balance:</span>
                                <span class="text-white font-medium">$250.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-purple-200">Next Invoice:</span>
                                <span class="text-white font-medium">$120.00</span>
                            </div>
                            <div class="flex justify-between">
                                <span class="text-purple-200">Due Date:</span>
                                <span class="text-white font-medium">15 Jul 2023</span>
                            </div>
                        </div>
                        <RouterLink
                            to="/billing"
                            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
                        >
                            View Billing Details
                        </RouterLink>
                    </CardComponent>
                </div>

                <!-- Main Content -->
                <div class="lg:col-span-3 space-y-6">
                    <!-- Stats Grid -->
                    <div class="grid grid-cols-3 gap-4">
                        <CardComponent v-for="(stat, index) in stats" :key="index">
                            <component :is="stat.icon" class="w-6 h-6 text-purple-500 mb-2" />
                            <div class="text-3xl font-bold text-white mb-1">{{ stat.value }}</div>
                            <div class="text-purple-200 text-sm">{{ stat.label }}</div>
                        </CardComponent>
                    </div>

                    <!-- Active Products -->
                    <CardComponent>
                        <div class="flex items-center justify-between mb-4">
                            <h2 class="text-lg font-semibold text-white">Your Active Products/Services</h2>
                            <button class="text-purple-500 hover:text-white transition-colors">
                                <MenuIcon class="w-5 h-5" />
                            </button>
                        </div>
                        <div class="space-y-4">
                            <div
                                v-for="(server, index) in servers"
                                :key="index"
                                class="flex items-center justify-between py-3 border-b border-purple-700 last:border-0"
                            >
                                <div>
                                    <div class="font-medium text-white">{{ server.name }}</div>
                                    <div class="text-sm text-purple-500">{{ server.hostname }}</div>
                                </div>
                                <div class="flex items-center gap-3">
                                    <span
                                        class="px-2 py-1 bg-emerald-500/20 text-emerald-400 rounded text-xs font-medium"
                                    >
                                        Active
                                    </span>
                                    <button
                                        class="px-3 py-1 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-sm"
                                    >
                                        Manage
                                    </button>
                                </div>
                            </div>
                        </div>
                    </CardComponent>

                    <!-- Recent Tickets -->
                    <CardComponent>
                        <h2 class="text-lg font-semibold text-white mb-4">Recent Tickets</h2>
                        <div class="space-y-3">
                            <div
                                v-for="ticket in recentTickets"
                                :key="ticket.id"
                                class="flex items-center justify-between py-2 border-b border-purple-700 last:border-0"
                            >
                                <div>
                                    <div class="font-medium text-white">{{ ticket.title }}</div>
                                    <div class="text-sm text-purple-500">{{ ticket.date }}</div>
                                </div>
                                <span
                                    :class="[
                                        'px-2 py-1 rounded text-xs font-medium',
                                        ticket.status === 'Open'
                                            ? 'bg-yellow-500/20 text-yellow-400'
                                            : 'bg-emerald-500/20 text-emerald-400',
                                    ]"
                                >
                                    {{ ticket.status }}
                                </span>
                            </div>
                        </div>
                        <RouterLink
                            to="/support"
                            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
                        >
                            View All Tickets
                        </RouterLink>
                    </CardComponent>

                    <!-- Announcements -->
                    <CardComponent>
                        <h2 class="text-lg font-semibold text-white mb-4">Announcements</h2>
                        <div class="space-y-4">
                            <div
                                v-for="announcement in announcements"
                                :key="announcement.id"
                                class="border-b border-purple-700 last:border-0 pb-4 last:pb-0"
                            >
                                <h3 class="text-white font-medium mb-1">{{ announcement.title }}</h3>
                                <p class="text-purple-200 text-sm mb-2">{{ announcement.content }}</p>
                                <span class="text-purple-500 text-xs">{{ announcement.date }}</span>
                            </div>
                        </div>
                    </CardComponent>
                </div>
            </div>
        </div>
    </LayoutDashboard>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';
import {
    RefreshCcw as RefreshCcwIcon,
    Server as ServerIcon,
    FileText as FileTextIcon,
    Ticket as TicketIcon,
    Menu as MenuIcon,
    BookMarkedIcon as MarketIcon,
} from 'lucide-vue-next';

const stats = [
    { icon: ServerIcon, value: '2', label: 'Services' },
    { icon: FileTextIcon, value: '0', label: 'Unpaid Invoices' },
    { icon: TicketIcon, value: '1', label: 'Tickets' },
];

const servers = ref([
    {
        name: 'Storage Root Server Frankfurt - Storage KVM S',
        hostname: 'backup2.mythical.systems',
    },
    {
        name: 'Storage Root Server Frankfurt - Storage KVM S',
        hostname: 'backup.mythical.systems',
    },
]);

const recentTickets = ref([
    { id: 1, title: 'Server Performance Issue', date: '2023-07-01', status: 'Open' },
    { id: 2, title: 'Billing Inquiry', date: '2023-06-28', status: 'Closed' },
    { id: 3, title: 'Domain Transfer Request', date: '2023-06-25', status: 'Open' },
]);

const announcements = ref([
    {
        id: 1,
        title: 'Scheduled Maintenance',
        content: 'We will be performing scheduled maintenance on July 15th from 2 AM to 4 AM UTC.',
        date: '2023-07-05',
    },
    {
        id: 2,
        title: 'New Feature: Two-Factor Authentication',
        content: 'Enhance your account security with our new two-factor authentication feature.',
        date: '2023-06-30',
    },
]);
</script>
