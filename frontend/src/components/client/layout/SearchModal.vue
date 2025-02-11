<template>
    <div
        v-if="isSearchOpen"
        class="fixed inset-0 bg-gray-900/95 backdrop-blur-xs z-50"
        @click="$emit('close')"
        @keydown.ctrl.k.prevent="$emit('close')"
        @keydown.ctrl.d.prevent="toggleDashboard"
    >
        <div class="max-w-3xl mx-auto pt-32 px-4" @click.stop>
            <div class="relative">
                <SearchIcon class="absolute left-4 top-3.5 h-5 w-5 text-gray-400" />
                <input
                    type="text"
                    :placeholder="$t('components.search.placeholder')"
                    v-model="searchQuery"
                    class="w-full bg-gray-800/50 border border-gray-700/50 rounded-lg pl-11 pr-4 py-3 focus:outline-hidden focus:ring-2 focus:ring-purple-500/50 text-gray-100"
                    @keydown.esc="$emit('close')"
                    @input="performSearch"
                    @keydown.enter="handleEnter"
                    @keydown.arrow-up.prevent="navigateResults(-1)"
                    @keydown.arrow-down.prevent="navigateResults(1)"
                    ref="searchInput"
                />
                <div class="absolute right-4 top-3 flex space-x-2">
                    <kbd
                        class="px-2 py-1 text-xs font-semibold text-gray-400 bg-gray-800 rounded-md border border-gray-700"
                    >
                        Ctrl K
                    </kbd>
                </div>
            </div>

            <!-- Search Results -->
            <div
                v-if="searchResults.length > 0"
                class="mt-4 bg-gray-800/50 rounded-lg border border-gray-700/50 overflow-hidden"
            >
                <div
                    v-for="(result, index) in searchResults"
                    :key="result.id || result.href"
                    :class="[
                        'p-4 cursor-pointer transition-colors duration-150',
                        selectedIndex === index ? 'bg-purple-500/20' : 'hover:bg-gray-700/50',
                    ]"
                    @click="navigateToResult(result)"
                    @mouseover="selectedIndex = index"
                >
                    <div class="flex items-center gap-3">
                        <component :is="result.icon" class="w-5 h-5 text-purple-400" />
                        <div class="flex-1">
                            <div class="font-medium text-gray-100">
                                <span v-if="result.id" class="text-purple-400 mr-2">#{{ result.id }}</span>
                                {{ result.title }}
                            </div>
                            <div class="text-sm text-gray-400">{{ result.description }}</div>
                        </div>
                        <div v-if="result.shortcut" class="text-sm text-gray-500">
                            {{ result.shortcut }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- No Results -->
            <div
                v-else-if="searchQuery"
                class="mt-4 p-4 bg-gray-800/50 rounded-lg border border-gray-700/50 text-gray-400"
            >
                {{ $t('components.search.no_results') }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref, watch } from 'vue';
import { LayoutDashboardIcon, Search as SearchIcon, TicketIcon } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';

const props = defineProps<{
    isSearchOpen: boolean;
}>();

const emit = defineEmits(['close', 'navigate', 'shortcut']);
const { t } = useI18n();
const router = useRouter();
const searchQuery = ref('');
const selectedIndex = ref(0);

interface SearchResult {
    id?: number;
    title: string;
    description: string;
    href: string;
    icon: unknown;
    shortcut?: string;
}

const searchResults = ref<SearchResult[]>([]);
const searchInput = ref<HTMLInputElement | null>(null);

const searchableItems: SearchResult[] = [
    {
        id: 1,
        title: t('dashboard.title'),
        description: t('components.search.items.dashboard'),
        href: '/',
        icon: LayoutDashboardIcon,
    },
    {
        id: 2,
        title: t('components.sidebar.tickets'),
        description: t('components.search.items.tickets'),
        href: '/ticket',
        icon: TicketIcon,
    },
];

const performSearch = () => {
    selectedIndex.value = 0;
    if (!searchQuery.value.trim()) {
        searchResults.value = [];
        return;
    }

    const query = searchQuery.value.toLowerCase();
    const isNumeric = /^\d+$/.test(query);

    if (isNumeric) {
        // First try exact ID match
        searchResults.value = searchableItems.filter((item) => item.id?.toString() === query);

        // If no exact matches, try partial ID matches
        if (searchResults.value.length === 0) {
            searchResults.value = searchableItems.filter((item) => item.id?.toString().includes(query));
        }
    } else {
        searchResults.value = searchableItems.filter(
            (item) => item.title.toLowerCase().includes(query) || item.description.toLowerCase().includes(query),
        );
    }
};

const navigateToResult = (result: SearchResult) => {
    emit('close');
    router.push(result.href);
};

const handleEnter = () => {
    if (searchResults.value.length > 0) {
        navigateToResult(searchResults.value[selectedIndex.value]);
    }
};

const navigateResults = (direction: number) => {
    const newIndex = selectedIndex.value + direction;
    if (newIndex >= 0 && newIndex < searchResults.value.length) {
        selectedIndex.value = newIndex;
    }
};

const toggleDashboard = () => {
    emit('close');
    router.push('/');
};

watch(
    () => props.isSearchOpen,
    (newValue) => {
        if (newValue) {
            searchQuery.value = '';
            searchResults.value = [];
            selectedIndex.value = 0;
            setTimeout(() => {
                searchInput.value?.focus();
            }, 100);
        }
    },
);
</script>
