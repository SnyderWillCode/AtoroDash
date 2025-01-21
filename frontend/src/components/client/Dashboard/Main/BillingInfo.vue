<template>
    <CardComponent>
        <h2 class="text-white text-lg font-semibold mb-4">Billing Summary</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-purple-200">Current Balance:</span>
                <span class="text-white font-medium">$250.00</span>
            </div>
            <div class="flex justify-between">
                <span class="text-purple-200">Next Invoice:</span>
                <span class="text-white font-medium">$120.00</span>
            </div>
            <div class="flex justify-between">
                <span class="text-purple-200">Due Date:</span>
                <span class="text-white font-medium">15 Jul 2023</span>
            </div>
        </div>
        <RouterLink
            to="/billing"
            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
        >
            View Invoices
        </RouterLink>
    </CardComponent>
</template>
<script lang="ts" setup>
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
</script>
