<script lang="ts" setup>
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';
import { useSettingsStore } from '@/stores/settings';
import { useI18n } from 'vue-i18n';

const Settings = useSettingsStore();
const { t } = useI18n();
</script>
<template>
    <CardComponent>
        <h2 class="text-white text-lg font-semibold mb-4">{{ t('components.dashboard.billing.title') }}</h2>
        <div class="space-y-2 text-sm">
            <div class="flex justify-between">
                <span class="text-purple-200">{{ t('components.dashboard.billing.current_balance') }}:</span>
                <span class="text-white font-medium"
                    >{{ Session.getInfo('credits') ?? 0 }} {{ Settings.getSetting('currency') }}</span
                >
            </div>
        </div>
        <RouterLink
            to="/addfunds"
            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-sm transition-colors text-center text-sm"
        >
            {{ t('components.dashboard.billing.add_funds') }}
        </RouterLink>
    </CardComponent>
</template>
