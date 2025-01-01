<script setup lang="ts">
/* ---------------------------
 * Author: NaysKutzu Date: 2025-11-29
 *
 * Changes:
 * - Initial version
 *
 * ---------------------------*/
import { reactive } from 'vue';
import LayoutAccount from './Layout.vue';
import TextInput from '@/components/client/ui/TextForms/TextInput.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';
import Auth from '@/mythicalclient/Auth';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

const form = reactive({
    company_name: Session.getInfo('company_name'),
    vat_number: Session.getInfo('vat_number'),
    address1: Session.getInfo('address1'),
    address2: Session.getInfo('address2'),
    city: Session.getInfo('city'),
    country: Session.getInfo('country'),
    state: Session.getInfo('state'),
    postcode: Session.getInfo('postcode'),
});

document.title = t('account.pages.billing.page.title');

const saveChanges = async () => {
    try {
        const response = await Auth.updateBilling(
            form.company_name,
            form.vat_number,
            form.address1,
            form.address2,
            form.city,
            form.country,
            form.state,
            form.postcode,
        );
        if (response.success) {
            console.log('Account updated successfully');
            const title = t('account.pages.billing.alerts.success.title');
            const text = t('account.pages.billing.alerts.success.update_success');
            const footer = t('account.pages.billing.alerts.success.footer');
            Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                footer: footer,
                showConfirmButton: true,
            });
        } else {
            const title = t('account.pages.billing.alerts.error.title');
            const text = t('account.pages.billing.alerts.error.generic');
            const footer = t('account.pages.billing.alerts.error.footer');
            Swal.fire({
                icon: 'error',
                title: title,
                text: text,
                footer: footer,
                showConfirmButton: true,
            });
            console.error('Error updating account:', response.error);
        }
    } catch (error) {
        const title = t('account.pages.billing.alerts.error.title');
        const text = t('account.pages.billing.alerts.error.generic');
        const footer = t('account.pages.billing.alerts.error.footer');
        Swal.fire({
            icon: 'error',
            title: title,
            text: text,
            footer: footer,
            showConfirmButton: true,
        });
        console.error('Error updating account:', error);
    }
};

const resetFields = async () => {
    form.company_name = Session.getInfo('company_name');
    form.vat_number = Session.getInfo('vat_number');
    form.address1 = Session.getInfo('address1');
    form.address2 = Session.getInfo('address2');
    form.city = Session.getInfo('city');
    form.country = Session.getInfo('country');
    form.state = Session.getInfo('state');
    form.postcode = Session.getInfo('postcode');
};
</script>

<style scoped>
/* Hide scrollbar for Chrome, Safari and Opera */
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

/* Hide scrollbar for IE, Edge and Firefox */
.overflow-x-auto {
    -ms-overflow-style: none;
    /* IE and Edge */
    scrollbar-width: none;
    /* Firefox */
}
</style>

<template>
    <!-- User Info -->
    <LayoutAccount />

    <!-- Settings Form -->
    <CardComponent
        :cardTitle="t('account.pages.billing.page.title')"
        :cardDescription="t('account.pages.billing.page.subTitle')"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.company_name.label')
                        }}</span>
                        <TextInput v-model="form.company_name" name="company_name" id="company_name" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.vat_id.label')
                        }}</span>
                        <TextInput v-model="form.vat_number" name="vat_number" id="vat_number" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.address.label')
                        }}</span>
                        <TextInput v-model="form.address1" name="address1" id="address1" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.address2.label')
                        }}</span>
                        <TextInput v-model="form.address2" name="address2" id="address2" />
                    </label>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.city.label')
                        }}</span>
                        <TextInput v-model="form.city" name="city" id="city" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.country.label')
                        }}</span>
                        <TextInput v-model="form.country" name="country" id="country" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.state.label')
                        }}</span>
                        <TextInput v-model="form.state" name="state" id="state" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.billing.page.form.zip.label')
                        }}</span>
                        <TextInput v-model="form.postcode" name="postcode" id="postcode" />
                    </label>
                </div>
            </div>
        </div>
        <br />
        <div class="flex flex-wrap gap-3">
            <button
                @click="saveChanges"
                type="button"
                class="px-4 py-2 bg-purple-500 hover:bg-purple-600 text-white rounded text-sm font-medium transition-colors"
            >
                {{ t('account.pages.billing.page.form.update_button.label') }}
            </button>
            <button
                @click="resetFields"
                type="button"
                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded text-sm font-medium transition-colors"
            >
                {{ t('account.pages.billing.page.form.update_button.reset') }}
            </button>
        </div>
    </CardComponent>
</template>
