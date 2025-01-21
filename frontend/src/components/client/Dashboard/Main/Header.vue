<template>
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
</template>
<script setup lang="ts">
import Session from '@/mythicalclient/Session';
</script>
