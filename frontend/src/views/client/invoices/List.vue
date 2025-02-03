<script setup lang="ts">
import { ref, onMounted, onErrorCaptured, h } from 'vue';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import { format } from 'date-fns';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import TableTanstack from '@/components/client/ui/Table/TableTanstack.vue';
import { AlertCircle, Receipt } from 'lucide-vue-next';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

const router = useRouter();
const { t } = useI18n();
MythicalDOM.setPageTitle(t('invoices.pages.invoices.title'));

interface Invoice {
    id: number;
    order: number;
    status: string;
    payment_gateway: string;
    created_at: string;
    due_date: string | null;
    paid_at: string | null;
    cancelled_at: string | null;
    refunded_at: string | null;
}

const invoices = ref<Invoice[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchInvoices = async () => {
    try {
        const response = await fetch('/api/user/invoices');
        const data = await response.json();

        if (data.success && Array.isArray(data.invoices)) {
            invoices.value = data.invoices;
        } else {
            throw new Error(data.error || 'Failed to fetch invoices');
        }
    } catch (err) {
        error.value = err instanceof Error ? err.message : t('invoices.pages.invoices.alerts.error.generic');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchInvoices);

onErrorCaptured((err) => {
    error.value = t('invoices.pages.invoices.alerts.error.generic');
    console.error('Error captured:', err);
    return false;
});

const columnsInvoices = [
    {
        accessorKey: 'order',
        header: t('invoices.pages.invoices.table.order'),
    },
    {
        accessorKey: 'status',
        header: t('invoices.pages.invoices.table.status'),
        cell: (info: { getValue: () => string }) => {
            const status = info.getValue().toUpperCase();
            let statusClass = '';
            switch (status) {
                case 'PAID':
                    statusClass = 'bg-green-100 text-green-800';
                    break;
                case 'PENDING':
                    statusClass = 'bg-orange-100 text-orange-800';
                    break;
                case 'CANCELLED':
                    statusClass = 'bg-red-100 text-red-800';
                    break;
                case 'REFUNDED':
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
                default:
                    statusClass = 'bg-gray-100 text-gray-800';
            }
            return h(
                'span',
                { class: `px-2 py-1 rounded-full text-xs font-semibold ${statusClass} dark:bg-opacity-50` },
                status,
            );
        },
    },
    {
        accessorKey: 'payment_gateway',
        header: t('invoices.pages.invoices.table.gateway'),
    },
    {
        accessorKey: 'created_at',
        header: t('invoices.pages.invoices.table.created'),
        cell: (info: { getValue: () => string | number | Date }) => format(new Date(info.getValue()), 'MMM d, yyyy'),
    },
    {
        accessorKey: 'due_date',
        header: t('invoices.pages.invoices.table.due'),
        cell: (info: { getValue: () => string | number | Date | null }) => {
            const value = info.getValue();
            return value ? format(new Date(value), 'MMM d, yyyy') : '-';
        },
    },
    {
        accessorKey: 'actions',
        header: t('invoices.pages.invoices.table.actions'),
        enableSorting: false,
        cell: ({ row }: { row: { original: { id: number } } }) =>
            h(
                'button',
                {
                    onClick: () => viewInvoice(row.original.id),
                    class: 'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-medium bg-purple-600 text-white hover:bg-purple-700',
                },
                t('invoices.pages.invoices.actions.view'),
            ),
    },
];

function viewInvoice(id: number) {
    router.push(`/invoice/${id}`);
}
</script>

<template>
    <LayoutDashboard>
        <div class="space-y-6 p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-100">{{ t('invoices.pages.invoices.title') }}</h1>
            </div>

            <Transition name="fade" mode="out-in">
                <div v-if="loading" class="space-y-4" key="loading">
                    <div v-for="i in 5" :key="i" class="bg-gray-800 rounded-lg p-4 animate-pulse">
                        <div class="h-6 bg-gray-700 rounded w-3/4 mb-2"></div>
                        <div class="h-4 bg-gray-700 rounded w-1/2"></div>
                    </div>
                </div>

                <div
                    v-else-if="error"
                    class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg flex items-center space-x-2"
                    role="alert"
                    key="error"
                >
                    <AlertCircle class="w-5 h-5 flex-shrink-0" />
                    <p>{{ error }}</p>
                </div>

                <div v-else-if="invoices.length === 0" class="text-center py-12 bg-gray-800 rounded-lg" key="empty">
                    <Receipt class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                    <p class="text-xl font-semibold text-gray-300">{{ t('invoices.pages.invoices.noInvoices') }}</p>
                    <p class="text-gray-400 mt-2">{{ t('invoices.pages.invoices.noInvoicesDesc') }}</p>
                </div>

                <div v-else key="table">
                    <TableTanstack
                        :data="invoices"
                        :columns="columnsInvoices"
                        :tableName="t('invoices.pages.invoices.table.title')"
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
