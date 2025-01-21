<template>
    <!-- Recent Tickets -->
    <CardComponent>
        <h2 class="text-lg font-semibold text-white mb-4">Recent Tickets</h2>
        <div class="space-y-3">
            <div
                v-for="ticket in recentTickets"
                :key="ticket.id"
                class="flex items-center justify-between py-2 border-b border-purple-700 last:border-0"
            >
                <div>
                    <div class="font-medium text-white">{{ ticket.title }}</div>
                    <div class="text-sm text-purple-500">{{ ticket.date }}</div>
                </div>
                <span
                    :class="[
                        'px-2 py-1 rounded text-xs font-medium',
                        ticket.status === 'Open'
                            ? 'bg-yellow-500/20 text-yellow-400'
                            : 'bg-emerald-500/20 text-emerald-400',
                    ]"
                >
                    {{ ticket.status }}
                </span>
            </div>
        </div>
        <RouterLink
            to="/support"
            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
        >
            View All Tickets
        </RouterLink>
    </CardComponent>
</template>

<script setup lang="ts">
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import { ref } from 'vue';

const recentTickets = ref([
    { id: 1, title: 'Server Performance Issue', date: '2023-07-01', status: 'Open' },
    { id: 2, title: 'Billing Inquiry', date: '2023-06-28', status: 'Closed' },
    { id: 3, title: 'Domain Transfer Request', date: '2023-06-25', status: 'Open' },
]);
</script>
