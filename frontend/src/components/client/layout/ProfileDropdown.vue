<template>
    <div
        v-if="isOpen"
        class="absolute top-16 right-4 w-64 bg-gray-900/95 backdrop-blur-sm border border-gray-700/50 rounded-lg shadow-xl z-50 dropdown"
    >
        <div class="p-4">
            <div class="flex items-center gap-3 mb-4">
                <div class="h-10 w-10 rounded-full bg-purple-500/20 flex items-center justify-center">
                    <UserIcon class="h-6 w-6 text-purple-400" />
                </div>
                <div>
                    <p class="font-medium">{{ userInfo.firstName }} {{ userInfo.lastName }}</p>
                    <p class="text-sm text-gray-400">{{ userInfo.roleName }}</p>
                </div>
            </div>
            <div class="space-y-1">
                <RouterLink
                    :to="item.href"
                    v-for="item in profileMenu"
                    :key="item.name"
                    class="w-full text-left px-3 py-2 rounded-lg hover:bg-gray-800/50 transition-colors flex items-center gap-3"
                >
                    <component :is="item.icon" class="h-5 w-5 text-purple-400" />
                    {{ item.name }}
                </RouterLink>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import { UserIcon } from 'lucide-vue-next';

defineProps<{
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
    };
}>();
</script>

<style scoped>
.dropdown {
    animation: dropdown 0.2s ease-out;
}

@keyframes dropdown {
    from {
        opacity: 0;
        transform: translateY(-8px);
    }

    to {
        opacity: 1;
        transform: translateY(0);
    }
}
</style>
