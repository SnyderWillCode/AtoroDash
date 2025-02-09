<template>
    <CardComponent>
        <h2 class="text-white text-lg font-semibold mb-4">Billing Summary</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-purple-200">Current Balance:</span>
                <span class="text-white font-medium">{{ Session.getInfo('credits') ?? 0 }} EUR</span>
            </div>
            <div class="flex justify-between">
                <span class="text-purple-200">Next Payment:</span>
                <span class="text-white font-medium">NaN</span>
            </div>
            <div class="flex justify-between">
                <span class="text-purple-200">Due Date:</span>
                <span class="text-white font-medium">NaN</span>
            </div>
        </div>
        <RouterLink
            to="/addfunds"
            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
        >
            Add Funds
        </RouterLink>
    </CardComponent>
</template>
<script lang="ts" setup>
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';
</script>
