<script setup lang="ts">
import { ref, reactive } from 'vue';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import TextInput from '@/components/client/ui/TextForms/TextInput.vue';
import SelectInput from '@/components/client/ui/TextForms/SelectInput.vue';
import Button from '@/components/client/ui/Button.vue';
import { CreditCard, ArrowLeft } from 'lucide-vue-next';
import { useRouter } from 'vue-router';
import { useI18n } from 'vue-i18n';
import Session from '@/mythicalclient/Session';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
import Swal from 'sweetalert2';
import failedAlertSfx from '@/assets/sounds/error.mp3';
import { useSound } from '@vueuse/sound';
import Settings from '@/mythicalclient/Settings';

const router = useRouter();
const { t } = useI18n();
const { play: playError } = useSound(failedAlertSfx);

MythicalDOM.setPageTitle(t('billing.pages.add_funds.title'));

const loading = ref(false);
const form = reactive({
    amount: '',
    payment_method: '',
});

const paymentMethods = ref<{value: string, label: string}[]>([]);

if (Settings.getSetting('enable_stripe') === 'true') {
    paymentMethods.value.push({
        value: 'stripe',
        label: 'Stripe'
    });
}

if (Settings.getSetting('enable_paypal') === 'true') {
    paymentMethods.value.push({
        value: 'paypal',
        label: 'PayPal'
    });
}

if (paymentMethods.value.length === 0) {
    Swal.fire({
		icon: 'error',
		title: t('billing.pages.add_funds.alerts.error.title'),
		text: t('billing.pages.add_funds.alerts.error.no_gateway'),
		showConfirmButton: true,
	});
    router.push('/');
}


const handleSubmit = async () => {
    if (!form.amount || !form.payment_method) {
        playError();
        Swal.fire({
            icon: 'error',
            title: t('billing.pages.add_funds.alerts.error.title'),
            text: t('billing.pages.add_funds.alerts.error.missing_fields'),
            showConfirmButton: true,
        });
        return;
    }

    const amount = parseFloat(form.amount);
    if (isNaN(amount) || amount <= 0) {
        playError();
        Swal.fire({
            icon: 'error',
            title: t('billing.pages.add_funds.alerts.error.title'),
            text: t('billing.pages.add_funds.alerts.error.invalid_amount'),
            showConfirmButton: true,
        });
        return;
    }

    try {
        loading.value = true;
        await new Promise(resolve => setTimeout(resolve, 1500));
        location.href = `/api/stripe/process?coins=`+amount;
    } catch (error) {
        playError();
        console.error('Payment processing error:', error);
        Swal.fire({
            icon: 'error',
            title: t('billing.pages.add_funds.alerts.error.title'),
            text: t('billing.pages.add_funds.alerts.error.payment_failed'),
            showConfirmButton: true,
        });
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <LayoutDashboard>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex justify-between items-center">
                <h1 class="text-2xl font-semibold text-gray-100">{{ t('billing.pages.add_funds.title') }}</h1>
                <router-link to="/">
                    <button
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200 flex items-center gap-2"
                    >
                        <ArrowLeft class="w-4 h-4" />
                        {{ t('billing.pages.add_funds.back') }}
                    </button>
                </router-link>
            </div>

            <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                <!-- Payment Form -->
                <div class="md:col-span-2">
                    <CardComponent
                        :cardTitle="t('billing.pages.add_funds.form.title')"
                        :cardDescription="t('billing.pages.add_funds.form.description')"
                    >
                        <form @submit.prevent="handleSubmit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    {{ t('billing.pages.add_funds.form.amount.label') }}
                                </label>
                                <TextInput
                                    v-model="form.amount"
                                    type="number"
                                    step="0.01"
                                    min="0"
                                    required
                                    :placeholder="t('billing.pages.add_funds.form.amount.placeholder')"
                                />
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-300 mb-2">
                                    {{ t('billing.pages.add_funds.form.payment_method.label') }}
                                </label>
                                <SelectInput
                                    v-model="form.payment_method"
                                    :options="paymentMethods"
                                    required
                                />
                            </div>

                            <Button type="submit" variant="primary" class="w-full" :loading="loading">
                                <template #icon>
                                    <CreditCard class="w-4 h-4" />
                                </template>
                                {{ loading ? t('billing.pages.add_funds.form.processing') : t('billing.pages.add_funds.form.submit') }}
                            </Button>
                        </form>
                    </CardComponent>
                </div>

                <!-- Summary -->
                <div>
                    <CardComponent :cardTitle="t('billing.pages.add_funds.summary.title')">
                        <div class="space-y-4">
                            <div class="flex justify-between items-center p-4 bg-gray-800/50 rounded-lg">
                                <span class="text-gray-300">{{ t('billing.pages.add_funds.summary.current_balance') }}</span>
                                <span class="text-white font-medium">{{ Session.getInfo('credits') ?? 0 }} EUR</span>
                            </div>

                            <div class="p-4 bg-purple-500/10 border border-purple-500/20 rounded-lg">
                                <p class="text-sm text-purple-200">
                                    {{ t('billing.pages.add_funds.summary.info') }}
                                </p>
                            </div>
                        </div>
                    </CardComponent>
                </div>
            </div>
        </div>
    </LayoutDashboard>
</template>
