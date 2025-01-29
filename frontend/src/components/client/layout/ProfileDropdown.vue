<template>
    <Transition name="dropdown">
        <div
            v-if="isOpen"
            class="absolute top-16 right-4 w-80 bg-gray-900/95 backdrop-blur-sm border border-gray-700/50 rounded-xl shadow-2xl z-50"
            @click.stop
        >
            <!-- User Profile Section -->
            <div class="p-5 border-b border-gray-700/50">
                <div class="flex items-center gap-4">
                    <div class="relative">
                        <div
                            class="h-12 w-12 rounded-full bg-gradient-to-tr from-purple-500/20 to-blue-500/20 flex items-center justify-center ring-2 ring-purple-500/20"
                        >
                            <img :src="userInfo.avatar" alt="User Avatar" class="h-full w-full rounded-full" />
                        </div>
                        <div
                            class="absolute bottom-0 right-0 h-3.5 w-3.5 rounded-full bg-green-500 ring-2 ring-gray-900"
                        ></div>
                    </div>
                    <div class="flex-1 min-w-0">
                        <h3 class="font-medium truncate">{{ userInfo.firstName }} {{ userInfo.lastName }}</h3>
                        <p class="text-sm text-gray-400 truncate">{{ userInfo.email }}</p>
                    </div>
                </div>
            </div>

            <!-- Quick Stats -->
            <div class="grid grid-cols-3 gap-1 p-3 border-b border-gray-700/50">
                <div class="text-center p-2 rounded-lg hover:bg-gray-800/50 transition-colors">
                    <p class="text-lg font-semibold text-purple-400">{{ stats.tickets }}</p>
                    <p class="text-xs text-gray-400">{{ $t('components.profile.tickets') }}</p>
                </div>
                <div class="text-center p-2 rounded-lg hover:bg-gray-800/50 transition-colors">
                    <p class="text-lg font-semibold text-purple-400">{{ stats.services }}</p>
                    <p class="text-xs text-gray-400">{{ $t('components.profile.services') }}</p>
                </div>
                <div class="text-center p-2 rounded-lg hover:bg-gray-800/50 transition-colors">
                    <p class="text-lg font-semibold text-purple-400">{{ stats.invoices }}</p>
                    <p class="text-xs text-gray-400">{{ $t('components.profile.invoices') }}</p>
                </div>
            </div>

            <!-- Menu Items -->
            <div class="p-2">
                <RouterLink
                    v-for="item in profileMenu"
                    :key="item.name"
                    :to="item.href"
                    class="flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-gray-800/50 transition-colors group"
                >
                    <component
                        :is="item.icon"
                        class="h-5 w-5 text-gray-400 group-hover:text-purple-400 transition-colors"
                    />
                    <span class="text-sm">{{ item.name }}</span>
                </RouterLink>

                <!-- Logout Button -->
                <button
                    @click="handleLogout"
                    class="w-full flex items-center gap-3 px-3 py-2.5 rounded-lg hover:bg-red-500/10 text-red-400 transition-colors group"
                >
                    <LogOutIcon class="h-5 w-5" />
                    <span class="text-sm">{{ $t('components.profile.logout') }}</span>
                </button>
            </div>
        </div>
    </Transition>
</template>

<script setup lang="ts">
import { LogOutIcon } from 'lucide-vue-next';

interface Props {
    isOpen: boolean;
    profileMenu: Array<{
        name: string;
        icon: unknown;
        href: string;
    }>;
    userInfo: {
        firstName: string;
        lastName: string;
        roleName: string;
        email: string;
        avatar: string;
    };
    stats: {
        tickets: number;
        services: number;
        invoices: number;
    };
}

withDefaults(defineProps<Props>(), {
    stats: () => ({
        tickets: 0,
        services: 0,
        invoices: 0,
    }),
});

const emit = defineEmits<{
    (e: 'logout'): void;
}>();

const handleLogout = () => {
    emit('logout');
};
</script>

<style scoped>
.dropdown-enter-active,
.dropdown-leave-active {
    transition: all 0.2s cubic-bezier(0.4, 0, 0.2, 1);
}

.dropdown-enter-from,
.dropdown-leave-to {
    opacity: 0;
    transform: translateY(-8px) scale(0.98);
}

.dropdown-enter-to,
.dropdown-leave-from {
    opacity: 1;
    transform: translateY(0) scale(1);
}
</style>
