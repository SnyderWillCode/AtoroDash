<script lang="ts" setup>
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';

import { BookMarkedIcon as MarketIcon } from 'lucide-vue-next';
</script>

<template>
    <!-- Profile Card -->
    <CardComponent>
        <div class="flex flex-col items-center text-center">
            <MarketIcon class="w-20 h-20 text-purple-500 mb-4" />
            <div class="text-xl text-white mb-2">
                {{ Session.getInfo('company_name') }}
            </div>
            <div class="text-purple-200 text-sm space-y-1 mb-4">
                <div>{{ Session.getInfo('vat_number') }}</div>
                <div>{{ Session.getInfo('address1') }}</div>
                <div>
                    {{ Session.getInfo('city') }} ({{ Session.getInfo('postcode') }}),
                    {{ Session.getInfo('country') }}
                </div>
            </div>
            <div class="flex gap-2 w-full">
                <RouterLink
                    to="/account"
                    class="flex-1 px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-sm"
                >
                    {{ $t('components.profile.update') }}
                </RouterLink>
                <a
                    href="/auth/logout"
                    class="flex-1 px-4 py-2 bg-purple-800 hover:bg-purple-700 text-white rounded transition-colors text-sm"
                >
                    {{ $t('components.profile.logout') }}
                </a>
            </div>
        </div>
    </CardComponent>
</template>
