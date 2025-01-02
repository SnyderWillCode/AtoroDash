<template>
    <div
        v-if="isOpen"
        class="absolute top-16 right-4 w-80 bg-gray-900/95 backdrop-blur-sm border border-gray-700/50 rounded-lg shadow-xl z-50 dropdown"
    >
        <div class="p-4">
            <h3 class="font-semibold mb-4 text-purple-400">Notifications</h3>
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
import { AlertTriangle as AlertTriangleIcon, Server as ServerIcon, Database as DatabaseIcon } from 'lucide-vue-next';

defineProps<{
    isOpen: boolean;
}>();

const notifications = [
    { id: 1, title: 'High CPU Usage Alert', time: '5 minutes ago', icon: AlertTriangleIcon },
    { id: 2, title: 'System Update Available', time: '1 hour ago', icon: ServerIcon },
    { id: 3, title: 'Backup Completed', time: '2 hours ago', icon: DatabaseIcon },
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
