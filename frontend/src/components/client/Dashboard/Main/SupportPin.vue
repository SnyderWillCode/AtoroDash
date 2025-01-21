<template>
    <!-- Support PIN -->
    <CardComponent>
        <h2 class="text-purple-200 text-sm font-medium mb-2">{{ t('Components.SupportPin.title') }}</h2>
        <div class="flex items-center gap-2">
            <span class="text-emerald-400 text-2xl font-mono font-bold">{{ Session.getInfo('support_pin') }}</span>
            <button @click="copyPin" class="text-purple-500 hover:text-white transition-colors">
                <CopyIcon class="w-4 h-4" />
            </button>
            <button @click="resetPin" class="text-purple-500 hover:text-white transition-colors">
                <RefreshCcwIcon class="w-4 h-4 transition-transform duration-500 hover:rotate-180" />
            </button>
        </div>
    </CardComponent>
</template>
<script setup lang="ts">
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Auth from '@/mythicalclient/Auth';
import Session from '@/mythicalclient/Session';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';
import { RefreshCcw as RefreshCcwIcon, Copy as CopyIcon } from 'lucide-vue-next';

const { t } = useI18n();

const copyPin = async () => {
    const pin = Session.getInfo('support_pin');
    try {
        if (!navigator.clipboard) {
            const textArea = document.createElement('textarea');
            textArea.value = pin;
            document.body.appendChild(textArea);
            textArea.select();
            document.execCommand('copy');
            document.body.removeChild(textArea);
        } else {
            await navigator.clipboard.writeText(pin);
        }

        Swal.fire({
            icon: 'success',
            title: t('Components.Global.Navigation.Copy.Title'),
            text: t('Components.Global.Navigation.Copy.Success'),
            footer: t('Components.Global.Navigation.Copy.Footer'),
        });
    } catch (err) {
        console.error('Failed to copy command to clipboard', err);
    }
};

const resetPin = async () => {
    try {
        const ping = await Auth.resetPin();
        const pinElement = document.querySelector('span.text-emerald-400');
        if (pinElement) {
            pinElement.textContent = ping.toString();
            Swal.fire({
                title: t('Components.SupportPin.alerts.success.title'),
                text: t('Components.SupportPin.alerts.success.pin_success'),
                icon: 'success',
                footer: t('Components.SupportPin.alerts.success.footer'),
            });
        }
    } catch (error) {
        console.error('Failed to reset support pin:', error);
        Swal.fire({
            title: t('Components.SupportPin.alerts.error.title'),
            text: t('Components.SupportPin.alerts.error.generic'),
            icon: 'error',
            footer: t('Components.SupportPin.alerts.error.footer'),
        });
    }
};
</script>
