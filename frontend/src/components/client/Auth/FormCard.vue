<script setup lang="ts">
import { HelpCircleIcon } from 'lucide-vue-next';
import { useI18n } from 'vue-i18n';
import { useSettingsStore } from '@/stores/settings';
const Settings = useSettingsStore();
const { t } = useI18n();

defineProps({
    title: String,
});

defineEmits(['submit']);
</script>
<template>
    <div class="w-full max-w-md bg-[#12121f]/90 backdrop-blur-xs rounded-lg shadow-xl p-8">
        <!-- Logo and Title -->
        <div class="flex items-center gap-3 mb-6">
            <img :src="Settings.getSetting('app_logo')" alt="Logo" class="w-8 h-8" />
            <h1 class="text-xl font-semibold text-white">{{ Settings.getSetting('app_name') }}</h1>
        </div>

        <!-- Form Title -->
        <h2 class="text-xl text-white mb-6">{{ title }}</h2>

        <!-- Form Content -->
        <form @submit.prevent="$emit('submit')">
            <slot></slot>
        </form>

        <!-- Footer Links -->
        <div class="mt-6 flex items-center justify-center gap-6 text-sm text-gray-400">
            <a href="#" class="flex items-center gap-2 hover:text-white transition-colors">
                <HelpCircleIcon class="w-4 h-4" />
                {{ t('auth.pages.login.components.form.support_center') }}
            </a>
        </div>
    </div>
</template>
