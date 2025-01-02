<template>
    <div v-if="isSearchOpen" class="fixed inset-0 bg-gray-900/95 backdrop-blur-sm z-50" @click="$emit('close')">
        <div class="max-w-3xl mx-auto pt-32 px-4" @click.stop>
            <div class="relative">
                <SearchIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" />
                <input
                    type="text"
                    placeholder="Search (Ctrl+/)"
                    v-model="searchQuery"
                    class="w-full bg-gray-800/50 border border-gray-700/50 rounded-lg pl-11 pr-4 py-3 focus:outline-none focus:ring-2 focus:ring-purple-500/50"
                    @keydown.esc="$emit('close')"
                    @input="performSearch"
                    ref="searchInput"
                />
            </div>
            <!-- Search Results -->
            <div v-if="searchResults.length > 0" class="mt-4 bg-gray-800/50 rounded-lg border border-gray-700/50">
                <div
                    v-for="result in searchResults"
                    :key="result.href"
                    class="p-4 hover:bg-gray-700/50 cursor-pointer"
                    @click="navigateToResult(result.href)"
                >
                    <div class="flex items-center gap-3">
                        <component :is="result.icon" class="w-5 h-5 text-purple-400" />
                        <div>
                            <div class="font-medium">{{ result.title }}</div>
                            <div class="text-sm text-gray-400">{{ result.description }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import {
    FileTextIcon,
    GlobeIcon,
    LayoutDashboardIcon,
    Search as SearchIcon,
    ServerIcon,
    TicketIcon,
} from 'lucide-vue-next';
import { useRouter } from 'vue-router';

const props = defineProps<{
    isSearchOpen: boolean;
}>();

const emit = defineEmits(['close', 'navigate']);

const router = useRouter();
const searchQuery = ref('');
const searchResults = ref<
    Array<{
        title: string;
        description: string;
        href: string;
        icon: unknown;
    }>
>([]);
const searchInput = ref<HTMLInputElement | null>(null);

const searchableItems = [
    {
        title: 'Dashboard',
        description: 'View your main dashboard',
        href: '/',
        icon: LayoutDashboardIcon,
    },
    {
        title: 'Services',
        description: 'Manage your services',
        href: '/services',
        icon: ServerIcon,
    },
    {
        title: 'Invoices',
        description: 'View and manage invoices',
        href: '/invoices',
        icon: FileTextIcon,
    },
    {
        title: 'Support Tickets',
        description: 'View your support tickets',
        href: '/ticket',
        icon: TicketIcon,
    },
    {
        title: 'Knowledge Base',
        description: 'Browse help articles',
        href: '/knowledge-base',
        icon: GlobeIcon,
    },
];

const performSearch = () => {
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    const lowercaseQuery = searchQuery.value.toLowerCase();
    searchResults.value = searchableItems.filter(
        (item) =>
            item.title.toLowerCase().includes(lowercaseQuery) ||
            item.description.toLowerCase().includes(lowercaseQuery),
    );

    // Auto-navigate if there's an exact match
    const exactMatch = searchResults.value.find((item) => item.title.toLowerCase() === lowercaseQuery);
    if (exactMatch) {
        navigateToResult(exactMatch.href);
    }
};

const navigateToResult = (href: string) => {
    emit('close');
    router.push(href);
};

watch(
    () => props.isSearchOpen,
    (newValue) => {
        if (newValue) {
            setTimeout(() => {
                if (searchInput.value) {
                    searchInput.value.focus();
                }
            }, 100);
        }
    },
);

onMounted(() => {
    if (searchInput.value) {
        searchInput.value.addEventListener('input', performSearch);
    }
});
</script>
