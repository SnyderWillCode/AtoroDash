<script setup lang="ts">
import { ref, onMounted, onErrorCaptured, h } from 'vue';
import { useI18n } from 'vue-i18n';
import { format } from 'date-fns';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import TableTanstack from '@/components/client/ui/Table/TableTanstack.vue';
import Order, { type OrderInterface } from '@/mythicalclient/Services/Order';
import { AlertCircle, Package } from 'lucide-vue-next';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

const { t } = useI18n();
MythicalDOM.setPageTitle(t('orders.pages.orders.title'));

const orders = ref<OrderInterface[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchOrders = async () => {
    try {
        const response = await Order.getOrders();
        if (response.success && Array.isArray(response.orders)) {
            orders.value = response.orders;
        } else {
            throw new Error(response.error || 'Failed to fetch orders');
        }
    } catch (err) {
        error.value = err instanceof Error ? err.message : 'Failed to fetch orders';
    } finally {
        loading.value = false;
    }
};

onMounted(fetchOrders);

onErrorCaptured((err) => {
    error.value = 'An error occurred while fetching orders';
    console.error('Error captured:', err);
    return false;
});

const columnsOrders = [
    {
        accessorKey: 'service.name',
        header: t('orders.pages.orders.table.service'),
    },
    {
        accessorKey: 'status',
        header: t('orders.pages.orders.table.status'),
        cell: (info: { getValue: () => string }) => {
            const status = info.getValue().toUpperCase();
            let statusClass = '';
            switch (status) {
                case 'PROCESSING':
                    statusClass = 'bg-yellow-100 text-yellow-800';
                    break;
                case 'PROCESSED':
                    statusClass = 'bg-green-100 text-green-800';
                    break;
                case 'CANCELLED':
                    statusClass = 'bg-red-100 text-red-800';
                    break;
                case 'FAILED':
                    statusClass = 'bg-red-100 text-red-800';
                    break;
                case 'DEPLOYED':
                    statusClass = 'bg-blue-100 text-blue-800';
                    break;
                case 'DEPLOYING':
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
        accessorKey: 'date',
        header: t('orders.pages.orders.table.created'),
        cell: (info: { getValue: () => string }) => format(new Date(info.getValue()), 'MMM d, yyyy'),
    },
    {
        accessorKey: 'service.price',
        header: t('orders.pages.orders.table.price'),
        cell: (info: { getValue: () => number }) => `$${info.getValue()}`,
    },
];
</script>

<template>
    <LayoutDashboard>
        <div class="space-y-6 p-6">
            <div class="flex justify-between items-center">
                <h1 class="text-3xl font-bold text-gray-100">{{ t('orders.pages.orders.title') }}</h1>
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

                <div v-else-if="orders.length === 0" class="text-center py-12 bg-gray-800 rounded-lg" key="empty">
                    <Package class="w-16 h-16 text-gray-600 mx-auto mb-4" />
                    <p class="text-xl font-semibold text-gray-300">{{ t('orders.pages.orders.noOrders') }}</p>
                    <p class="text-gray-400 mt-2">{{ t('orders.pages.orders.noOrdersDesc') }}</p>
                </div>

                <div v-else key="table">
                    <TableTanstack
                        :data="orders"
                        :columns="columnsOrders"
                        :tableName="t('orders.pages.orders.table.title')"
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
