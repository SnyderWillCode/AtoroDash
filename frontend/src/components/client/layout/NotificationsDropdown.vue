<template>
    <div
        v-if="isOpen"
        class="absolute top-16 right-4 w-80 bg-gray-900/95 backdrop-blur-sm border border-gray-700/50 rounded-lg shadow-xl z-50 dropdown"
    >
        <div class="p-4">
            <h3 class="font-semibold mb-4 text-purple-400">{{ t('components.notifications.title') }}</h3>
            <div class="space-y-4">
                <div
                    v-for="notification in notifications"
                    :key="notification.id"
                    class="flex items-start gap-3 p-2 hover:bg-gray-800/50 rounded-lg transition-colors"
                >
                    <div class="w-8 h-8 rounded-full bg-purple-500/20 flex items-center justify-center flex-shrink-0">
                        <component :is="notification.icon" class="h-4 w-4 text-purple-400" />
                    </div>
                    <div>
                        <p class="font-medium">{{ notification.title }}</p>
                        <p class="text-sm text-gray-400">{{ notification.time }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>

<script setup lang="ts">
import Session from '@/mythicalclient/Session';
import { AlertTriangle as AlertTriangleIcon } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

defineProps<{
    isOpen: boolean;
}>();

const notifications = [
    {
        id: 1,
        title: t('components.notifications.credits', { credits: Session.getInfo('credits') }),
        time: new Date().toLocaleTimeString(),
        icon: AlertTriangleIcon,
    },
];
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
