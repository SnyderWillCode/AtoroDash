<script setup lang="ts">
import { ref, onMounted, onErrorCaptured, h } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { format } from 'date-fns';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import TableTanstack from '@/components/client/ui/Table/TableTanstack.vue';
import Tickets from '@/mythicalclient/Tickets';
import { AlertCircle, Plus, Ticket } from 'lucide-vue-next';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

const router = useRouter();
const { t } = useI18n();
MythicalDOM.setPageTitle(t('tickets.pages.tickets.title'));

interface Department {
    id: number;
    name: string;
    description: string;
    time_open: string;
    time_close: string;
    enabled: string;
    deleted: string;
    locked: string;
    date: string;
}

interface Ticket {
    id: number;
    user: string;
    department: Department;
    priority: string;
    status: string;
    service: number | null;
    title: string;
    description: string;
    deleted: string;
    locked: string;
    date: string;
    department_id: number;
}

const tickets = ref<Ticket[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchTickets = async () => {
    try {
        const response = await Tickets.getTickets();
        if (response.success && Array.isArray(response.tickets)) {
            tickets.value = response.tickets;
        } else {
            throw new Error(response.error || 'Failed to fetch tickets');
        }
    } catch (err) {
        error.value = err instanceof Error ? err.message : t('tickets.pages.tickets.alerts.error.generic');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchTickets);

onErrorCaptured((err) => {
    error.value = t('tickets.pages.tickets.alerts.error.generic');
    console.error('Error captured:', err);
    return false;
});

const columnsTickets = [
    {
        accessorKey: 'title',
        header: t('tickets.pages.tickets.table.subject'),
    },
    {
        accessorKey: 'status',
        header: t('tickets.pages.tickets.table.status'),
        cell: (info: { getValue: () => string }) => {
            const status = info.getValue().toUpperCase();
            let statusClass = '';
            switch (status) {
                case 'OPEN':
                    statusClass = 'bg-green-100 text-green-800';
                    break;
                case 'CLOSED':
                    statusClass = 'bg-red-100 text-red-800';
                    break;
                case 'WAITING':
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'REPLIED':
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
                case 'INPROGRESS':
                    statusClass = 'bg-orange-100 text-orange-800';
                    break;
                default:
                    statusClass = 'bg-gray-100 text-gray-800';
            }
            return h(
                'span',
                { class: `px-2 py-1 rounded-full text-xs font-semibold ${statusClass} dark:bg-opacity-50` },
                status === 'INPROGRESS' ? 'IN PROGRESS' : status,
            );
        },
    },
    {
        accessorKey: 'priority',
        header: t('tickets.pages.tickets.table.priority'),
        cell: (info: { getValue: () => string }) => {
            const priority = info.getValue().toUpperCase();
            let priorityClass = '';
            switch (priority) {
                case 'LOW':
                    priorityClass = 'bg-green-100 text-green-800';
                    break;
                case 'MEDIUM':
                    priorityClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'HIGH':
                    priorityClass = 'bg-orange-100 text-orange-800';
                    break;
                case 'URGENT':
                    priorityClass = 'bg-red-100 text-red-800';
                    break;
                default:
                    priorityClass = 'bg-gray-100 text-gray-800';
            }
            return h(
                'span',
                { class: `px-2 py-1 rounded-full text-xs font-semibold ${priorityClass} dark:bg-opacity-50` },
                priority,
            );
        },
    },
    {
        accessorKey: 'department.name',
        header: t('tickets.pages.tickets.table.department'),
    },
    {
        accessorKey: 'date',
        header: t('tickets.pages.tickets.table.created'),
        cell: (info: { getValue: () => string | number | Date }) => format(new Date(info.getValue()), 'MMM d, yyyy'),
    },
    {
        accessorKey: 'actions',
        header: t('tickets.pages.tickets.table.actions'),
        enableSorting: false,
        cell: ({ row }: { row: { original: { id: number } } }) =>
            h(
                'button',
                {
                    onClick: () => viewTicket(row.original.id),
                    class: 'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-medium bg-purple-600 text-white hover:bg-purple-700',
                },
                t('tickets.pages.tickets.actions.view'),
            ),
    },
];

function viewTicket(id: number) {
    router.push(`/ticket/${id}`);
}

function createNewTicket() {
    router.push('/ticket/create');
}
</script>

<template>
    <LayoutDashboard>
        <div class="space-y-6 p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-100">{{ t('tickets.pages.tickets.title') }}</h1>
                <button
                    @click="createNewTicket"
                    class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-sm font-medium transition-colors flex items-center space-x-2"
                >
                    <Plus class="w-4 h-4" />
                    <span>{{ t('tickets.pages.tickets.actions.newTicket') }}</span>
                </button>
            </div>

            <Transition name="fade" mode="out-in">
                <div v-if="loading" class="space-y-4" key="loading">
                    <div v-for="i in 5" :key="i" class="bg-gray-800 rounded-lg p-4 animate-pulse">
                        <div class="h-6 bg-gray-700 rounded-sm w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-700 rounded-sm w-1/2"></div>
                    </div>
                </div>

                <div
                    v-else-if="error"
                    class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg flex items-center space-x-2"
                    role="alert"
                    key="error"
                >
                    <AlertCircle class="w-5 h-5 shrink-0" />
                    <p>{{ error }}</p>
                </div>

                <div v-else-if="tickets.length === 0" class="text-center py-12 bg-gray-800 rounded-lg" key="empty">
                    <Ticket class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                    <p class="text-xl font-semibold text-gray-300">{{ t('tickets.pages.tickets.noTickets') }}</p>
                    <p class="text-gray-400 mt-2">{{ t('tickets.pages.tickets.noTickets') }}</p>
                    <button
                        @click="createNewTicket"
                        class="mt-4 px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded-lg text-sm font-medium transition-colors"
                    >
                        {{ t('tickets.pages.tickets.actions.newTicket') }}
                    </button>
                </div>

                <div v-else key="table">
                    <TableTanstack
                        :data="tickets"
                        :columns="columnsTickets"
                        :tableName="t('tickets.pages.tickets.table.title')"
                    />
                </div>
            </Transition>
        </div>
    </LayoutDashboard>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}
</style>
