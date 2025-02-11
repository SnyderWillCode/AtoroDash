<script setup lang="ts">
import Session from '@/mythicalclient/Session';
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.overflow-x-auto {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}
</style>

<template>
    <!-- User Info -->
    <div class="flex flex-col sm:flex-row items-start gap-4 mb-8 p-4 bg-gray-800/20 rounded-lg">
        <img :src="Session.getInfo('avatar')" alt="" class="w-16 h-16 rounded-sm bg-gray-800" />
        <div class="flex-1 min-w-0">
            <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
                <div class="space-y-1">
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400">UUID:</span>
                        <code class="text-xs font-mono text-purple-400">{{ Session.getInfo('uuid') }}</code>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400">Role:</span>
                        <span class="text-xs font-mono text-pink-400">{{ Session.getInfo('role_name') }}</span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400">First IP:</span>
                        <code class="text-xs font-mono text-gray-300">{{ Session.getInfo('first_ip') }}</code>
                        <span class="text-xs text-gray-500">(</span>
                        <code class="text-xs font-mono text-gray-300">{{ Session.getInfo('last_ip') }}</code>
                        <span class="text-xs text-gray-500">)</span>
                    </div>
                    <div class="flex items-center gap-2 flex-wrap">
                        <span class="text-xs text-gray-400">First seen:</span>
                        <code class="text-xs font-mono text-gray-300">{{ Session.getInfo('first_seen') }}</code>
                        <span class="text-xs text-gray-500">(</span>
                        <code class="text-xs font-mono text-gray-300">{{ Session.getInfo('last_seen') }}</code>
                        <span class="text-xs text-gray-500">)</span>
                    </div>
                </div>
                <div class="flex items-center gap-2">
                    <span class="text-xs text-gray-400">Verified:</span>
                    <span class="text-xs font-mono text-emerald-400">{{ Session.getInfo('verified') }}</span>
                </div>
            </div>
        </div>
    </div>
</template>
