<script setup lang="ts">
import { ref, onMounted } from 'vue';
import {
    AlertTriangleIcon,
    ChevronDown as ChevronDownIcon,
    CopyPlusIcon,
    FileTextIcon,
    GlobeIcon,
    LayoutDashboardIcon,
    ServerIcon,
    TicketIcon,
} from 'lucide-vue-next';
import Translation from '@/mythicalclient/Translation';
import ServiceCategories from '@/mythicalclient/Services/ServiceCategories';

interface Category {
    id: number;
    name: string;
    uri: string;
    headline: string;
    enabled: string;
}

const categories = ref<Category[]>([]);
const isLoading = ref(true);

const fetchCategories = async () => {
    try {
        const response = await ServiceCategories.getCategoriesCache();
        categories.value = response;

        // Find the Services menu item
        const servicesMenuItem = menuSections.value[0].items.find((item) => item.name === 'Services');
        if (servicesMenuItem && servicesMenuItem.subitems) {
            // Update the Add Service menu item to include categories
            const addServiceItem = servicesMenuItem.subitems.find((item) => item.name === 'Add Service');
            if (addServiceItem) {
                addServiceItem.subitems = [
                    // Add categories as subitems
                    ...categories.value.map((category) => ({
                        name: category.name,
                        icon: ServerIcon,
                        href: `/services/${category.uri}`,
                        active: isActiveRoute([`/services/${category.uri}`]),
                        tooltip: category.headline,
                    })),
                ];
                addServiceItem.expanded = false; // Add expanded property
            }
        }
    } catch (error) {
        console.error('Failed to fetch categories:', error);
    } finally {
        isLoading.value = false;
    }
};

onMounted(() => {
    fetchCategories();
});

defineProps<{
    isSidebarOpen: boolean;
}>();

const isActiveRoute = (routes: string | string[]) => {
    return routes.includes(window.location.pathname);
};

const menuSections = ref([
    {
        title: 'General',
        items: [
            {
                name: Translation.getTranslation('components.sidebar.dashboard'),
                icon: LayoutDashboardIcon,
                href: '/',
                active: isActiveRoute(['/dashboard']),
            },
            {
                name: 'Services',
                icon: ServerIcon,
                href: '/services',
                active: isActiveRoute(['/services']),
                expanded: false,
                subitems: [
                    {
                        name: 'All Services',
                        icon: ServerIcon,
                        href: '/services',
                        active: isActiveRoute(['/services']),
                    },
                    {
                        name: 'Add Service',
                        icon: CopyPlusIcon,
                        href: '/services/add',
                        active: isActiveRoute(['/services/add']),
                        expanded: false,
                        subitems: [] as Array<{
                            name: string;
                            icon: typeof ServerIcon;
                            href: string;
                            active: boolean;
                            tooltip?: string;
                            subitems?:
                                | Array<{
                                      name: string;
                                      icon: typeof ServerIcon;
                                      href: string;
                                      active: boolean;
                                      tooltip?: string;
                                      expanded?: boolean;
                                  }>
                                | [];
                            expanded?: boolean;
                        }>,
                    },
                ],
            },
            {
                name: 'Invoices',
                icon: FileTextIcon,
                href: '/invoices',
                active: isActiveRoute(['/invoices']),
            },
        ],
    },
    {
        title: 'Support',
        items: [
            {
                name: 'Knowledge Base',
                icon: GlobeIcon,
                href: '/knowledge-base',
                active: isActiveRoute(['/knowledge-base']),
            },
            {
                name: Translation.getTranslation('components.sidebar.tickets'),
                icon: TicketIcon,
                href: '/ticket',
                active: isActiveRoute(['/ticket']),
                expanded: false,
                subitems: [
                    {
                        name: 'Open Ticket',
                        icon: AlertTriangleIcon,
                        href: '/ticket/create',
                        active: isActiveRoute(['/ticket/create']),
                    },
                    {
                        name: 'All Tickets',
                        icon: TicketIcon,
                        href: '/ticket',
                        active: isActiveRoute(['/ticket']),
                    },
                ],
            },
        ],
    },
]);

interface MenuItem {
    name: string;
    icon: typeof ServerIcon;
    href: string;
    active: boolean;
    expanded?: boolean;
    subitems?: MenuItem[];
}

const toggleSubitems = (item: MenuItem) => {
    item.expanded = !item.expanded;
};
</script>
<template>
    <aside
        class="fixed top-0 left-0 h-full w-64 bg-gray-900/50 backdrop-blur-sm border-r border-gray-700/50 transform transition-transform duration-200 ease-in-out z-50 lg:translate-x-0 lg:z-20"
        :class="isSidebarOpen ? 'translate-x-0' : '-translate-x-full'"
    >
        <!-- Sidebar Content -->
        <div class="flex flex-col h-full pt-16">
            <div class="flex-1 overflow-y-auto">
                <nav class="p-4">
                    <div v-for="(section, index) in menuSections" :key="index" class="mb-6">
                        <div class="text-xs uppercase tracking-wider text-gray-500 font-medium px-4 mb-2">
                            {{ section.title }}
                        </div>
                        <div class="space-y-1">
                            <template v-for="item in section.items" :key="item.name">
                                <div v-if="item.subitems">
                                    <button
                                        @click="toggleSubitems(item)"
                                        class="w-full flex items-center justify-between gap-3 px-4 py-2 rounded-lg hover:bg-gray-800/50 transition-colors"
                                        :class="{ 'bg-purple-500/10 text-purple-400': item.active }"
                                    >
                                        <div class="flex items-center gap-3">
                                            <component :is="item.icon" class="w-5 h-5" />
                                            {{ item.name }}
                                        </div>
                                        <ChevronDownIcon
                                            class="w-4 h-4 transition-transform duration-200"
                                            :class="{ 'rotate-180': item.expanded }"
                                        />
                                    </button>
                                    <transition
                                        enter-active-class="transition-all duration-300 ease-in-out"
                                        leave-active-class="transition-all duration-300 ease-in-out"
                                        enter-from-class="opacity-0 max-h-0"
                                        enter-to-class="opacity-100 max-h-[500px]"
                                        leave-from-class="opacity-100 max-h-[500px]"
                                        leave-to-class="opacity-0 max-h-0"
                                    >
                                        <div v-show="item.expanded" class="ml-4 mt-1 space-y-1 overflow-hidden">
                                            <template v-for="subitem in item.subitems" :key="subitem.name">
                                                <!-- Regular subitem -->
                                                <div>
                                                    <div v-if="subitem.subitems" class="w-full">
                                                        <button
                                                            @click="toggleSubitems(subitem)"
                                                            class="w-full flex items-center justify-between gap-3 px-4 py-2 rounded-lg hover:bg-gray-800/50 transition-colors text-sm"
                                                            :class="{
                                                                'bg-purple-500/10 text-purple-400': subitem.active,
                                                            }"
                                                        >
                                                            <div class="flex items-center gap-3">
                                                                <component :is="subitem.icon" class="w-4 h-4" />
                                                                {{ subitem.name }}
                                                            </div>
                                                            <ChevronDownIcon
                                                                v-if="subitem.subitems.length"
                                                                class="w-4 h-4 transition-transform duration-200"
                                                                :class="{ 'rotate-180': subitem.expanded }"
                                                            />
                                                        </button>

                                                        <!-- Nested categories -->
                                                        <transition
                                                            enter-active-class="transition-all duration-300 ease-in-out"
                                                            leave-active-class="transition-all duration-300 ease-in-out"
                                                            enter-from-class="opacity-0 max-h-0"
                                                            enter-to-class="opacity-100 max-h-[500px]"
                                                            leave-from-class="opacity-100 max-h-[500px]"
                                                            leave-to-class="opacity-0 max-h-0"
                                                        >
                                                            <div
                                                                v-if="subitem.subitems.length"
                                                                v-show="subitem.expanded"
                                                                class="ml-4 mt-1 space-y-1 overflow-hidden"
                                                            >
                                                                <RouterLink
                                                                    v-for="category in subitem.subitems"
                                                                    :key="category.name"
                                                                    :to="category.href"
                                                                    class="group relative flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-800/50 transition-colors text-sm"
                                                                    :class="{
                                                                        'bg-purple-500/10 text-purple-400':
                                                                            category.active,
                                                                    }"
                                                                >
                                                                    <component :is="category.icon" class="w-4 h-4" />
                                                                    {{ category.name }}

                                                                    <!-- Tooltip for category headline -->
                                                                    <div
                                                                        v-if="category.tooltip"
                                                                        class="absolute left-full ml-2 px-2 py-1 bg-gray-800 text-white text-xs rounded whitespace-nowrap opacity-0 group-hover:opacity-100 transition-opacity z-50 pointer-events-none"
                                                                    >
                                                                        {{ category.tooltip }}
                                                                    </div>
                                                                </RouterLink>
                                                            </div>
                                                        </transition>
                                                    </div>

                                                    <RouterLink
                                                        v-else
                                                        :to="subitem.href"
                                                        class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-800/50 transition-colors text-sm"
                                                        :class="{ 'bg-purple-500/10 text-purple-400': subitem.active }"
                                                    >
                                                        <component :is="subitem.icon" class="w-4 h-4" />
                                                        {{ subitem.name }}
                                                    </RouterLink>
                                                </div>
                                            </template>
                                        </div>
                                    </transition>
                                </div>
                                <RouterLink
                                    v-else
                                    :to="item.href"
                                    class="flex items-center gap-3 px-4 py-2 rounded-lg hover:bg-gray-800/50 transition-colors"
                                    :class="{ 'bg-purple-500/10 text-purple-400': item.active }"
                                >
                                    <component :is="item.icon" class="w-5 h-5" />
                                    {{ item.name }}
                                </RouterLink>
                            </template>
                        </div>
                    </div>
                </nav>
            </div>
        </div>
    </aside>
</template>

<style scoped>
.rotate-180 {
    transform: rotate(180deg);
}

/* Add z-index handling for tooltips */
.group:hover {
    z-index: 100;
}
</style>
