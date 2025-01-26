<script setup lang="ts">
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Tickets from '@/mythicalclient/Tickets';
import { ref, onMounted } from 'vue';
import { useI18n } from 'vue-i18n';

interface Ticket {
    id: number;
    title: string;
    date: string;
    status: string;
}
const { t } = useI18n();
const recentTickets = ref<Ticket[]>([]);

import { format } from 'date-fns';

const fetchRecentTickets = async () => {
    try {
        const response = await Tickets.getTickets();
        if (response.success && Array.isArray(response.tickets)) {
            recentTickets.value = response.tickets
                .slice(0, 3)
                .map((ticket: { date: string | number | Date; status: string }) => ({
                    ...ticket,
                    date: format(new Date(ticket.date), 'PPP'),
                    status: ticket.status
                        .split(' ')
                        .map((word) => word.charAt(0).toUpperCase() + word.slice(1).toLowerCase())
                        .join(' ')
                        .replace('Inprogress', 'In Progress'),
                }));
        } else {
            throw new Error(response.error || 'Failed to fetch tickets');
        }
    } catch (err) {
        console.error('Error fetching recent tickets:', err);
    }
};

onMounted(fetchRecentTickets);
</script>

<template>
    <!-- Recent Tickets -->
    <CardComponent>
        <h2 class="text-lg font-semibold text-white mb-4">{{ t('Components.Tickets.title') }}</h2>
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
                        ticket.status === t('Components.Tickets.status.Open')
                            ? 'bg-green-500/20 text-green-400'
                            : ticket.status === t('Components.Tickets.status.Closed')
                              ? 'bg-red-500/20 text-red-400'
                              : ticket.status === t('Components.Tickets.status.Waiting')
                                ? 'bg-orange-500/20 text-orange-400'
                                : ticket.status === t('Components.Tickets.status.Replied')
                                  ? 'bg-blue-500/20 text-blue-400'
                                  : ticket.status === t('Components.Tickets.status.InProgress')
                                    ? 'bg-purple-500/20 text-purple-400'
                                    : '',
                    ]"
                >
                    {{ ticket.status }}
                </span>
            </div>
        </div>
        <RouterLink
            to="/ticket"
            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
        >
            {{ t('Components.Tickets.viewMore') }}
        </RouterLink>
    </CardComponent>
</template>
