<script setup lang="ts">
import { ref, onMounted, onErrorCaptured } from 'vue';
import { format } from 'date-fns';
import LayoutAccount from './Layout.vue';
import TableTanstack from '@/components/client/ui/Table/TableTanstack.vue';
import Activities from '@/mythicalclient/Activities';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

document.title = t('account.pages.activity.page.title');

interface Activity {
    id: number;
    user: string;
    action: string;
    ip_address: string;
    deleted: boolean | string;
    locked: boolean | string;
    date: string;
}

const activities = ref<Activity[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);

const fetchActivities = async () => {
    try {
        const response = await Activities.get();
        activities.value = response;
    } catch (err) {
        error.value = err instanceof Error ? err.message : t('account.pages.activity.page.table.error');
    } finally {
        loading.value = false;
    }
};

onMounted(fetchActivities);

onErrorCaptured((err) => {
    error.value = 'An unexpected error occurred';
    console.error('Error captured:', err);
    return false;
});

const columnsActivities = [
    {
        accessorKey: 'action',
        header: t('account.pages.activity.page.table.columns.action'),
    },
    {
        accessorKey: 'ip_address',
        header: t('account.pages.activity.page.table.columns.ip'),
    },
    {
        accessorKey: 'date',
        header: t('account.pages.activity.page.table.columns.date'),
        // eslint-disable-next-line @typescript-eslint/no-explicit-any
        cell: (info: any) => format(new Date(info.getValue()), 'MMM d, yyyy HH:mm'),
    },
];
</script>

<template>
    <LayoutAccount />

    <div>
        <div v-if="loading" class="text-center py-4">
            <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-gray-900"></div>
            <p class="mt-2">Loading activities...</p>
        </div>

        <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
            <p>{{ error }}</p>
        </div>

        <div v-else class="overflow-x-auto">
            <TableTanstack
                :data="activities"
                :columns="columnsActivities"
                :tableName="t('account.pages.activity.page.title')"
            />
        </div>
    </div>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

.overflow-x-auto {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
