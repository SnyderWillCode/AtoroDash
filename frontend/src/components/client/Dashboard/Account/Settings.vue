<script setup lang="ts">
import { reactive } from 'vue';
import LayoutAccount from './Layout.vue';
import TextInput from '@/components/client/ui/TextForms/TextInput.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';
import { useI18n } from 'vue-i18n';
import Swal from 'sweetalert2';
import Auth from '@/mythicalclient/Auth';

const { t } = useI18n();

const form = reactive({
    firstName: Session.getInfo('first_name'),
    lastName: Session.getInfo('last_name'),
    email: Session.getInfo('email'),
    avatar: Session.getInfo('avatar'),
    background: Session.getInfo('background'),
});

document.title = t('account.pages.settings.page.title');

const saveChanges = async () => {
    try {
        const response = await Auth.updateUserInfo(
            form.firstName,
            form.lastName,
            form.email,
            form.avatar,
            form.background,
        );

        if (response.success) {
            console.log('Account updated successfully');
            const title = t('account.pages.settings.alerts.success.title');
            const text = t('account.pages.settings.alerts.success.update_success');
            const footer = t('account.pages.settings.alerts.success.footer');
            Swal.fire({
                icon: 'success',
                title: title,
                text: text,
                footer: footer,
                showConfirmButton: true,
            });
        } else {
            if (response.error_code == 'EMAIL_EXISTS') {
                const title = t('account.pages.settings.alerts.error.title');
                const text = t('account.pages.settings.alerts.error.email');
                const footer = t('account.pages.settings.alerts.error.footer');
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: text,
                    footer: footer,
                    showConfirmButton: true,
                });
                console.error('Error updating account:', response.error);
            } else {
                const title = t('account.pages.settings.alerts.error.title');
                const text = t('account.pages.settings.alerts.error.generic');
                const footer = t('account.pages.settings.alerts.error.footer');
                Swal.fire({
                    icon: 'error',
                    title: title,
                    text: text,
                    footer: footer,
                    showConfirmButton: true,
                });
                console.error('Error updating account:', response.error);
            }
        }
    } catch (error) {
        const title = t('account.pages.settings.alerts.error.title');
        const text = t('account.pages.settings.alerts.error.generic');
        const footer = t('account.pages.settings.alerts.error.footer');
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
    form.firstName = Session.getInfo('first_name');
    form.lastName = Session.getInfo('last_name');
    form.email = Session.getInfo('email');
    form.avatar = Session.getInfo('avatar');
    form.background = Session.getInfo('background');
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
        :cardTitle="t('account.pages.settings.page.title')"
        :cardDescription="t('account.pages.settings.page.subTitle')"
    >
        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
            <div class="space-y-4">
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.settings.page.form.firstName.label')
                        }}</span>
                        <TextInput v-model="form.firstName" name="firstName" id="firstName" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.settings.page.form.email.label')
                        }}</span>
                        <TextInput type="email" v-model="form.email" name="email" id="email" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.settings.page.form.background.label')
                        }}</span>
                        <TextInput type="url" v-model="form.background" name="background" id="background" />
                    </label>
                </div>
            </div>
            <div class="space-y-4">
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.settings.page.form.lastName.label')
                        }}</span>
                        <TextInput type="text" v-model="form.lastName" name="lastName" id="lastName" />
                    </label>
                </div>
                <div>
                    <label class="block">
                        <span class="block text-sm font-medium text-gray-400 mb-1.5">{{
                            t('account.pages.settings.page.form.avatar.label')
                        }}</span>
                        <TextInput type="url" v-model="form.avatar" name="avatar" id="avatar" />
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
                {{ t('account.pages.settings.page.form.update_button.label') }}
            </button>
            <button
                @click="resetFields"
                type="button"
                class="px-4 py-2 bg-gray-800 hover:bg-gray-700 text-gray-300 rounded text-sm font-medium transition-colors"
            >
                {{ t('account.pages.settings.page.form.update_button.reset') }}
            </button>
        </div>
    </CardComponent>
    <br />
    <CardComponent
        :cardTitle="t('account.pages.settings.page.delete.title')"
        :cardDescription="t('account.pages.settings.page.delete.subTitle')"
    >
        <div class="space-y-4">
            <p class="text-sm text-gray-300">
                {{ t('account.pages.settings.page.delete.lines.0') }}
            </p>
            <p class="text-sm text-gray-300">
                {{ t('account.pages.settings.page.delete.lines.1') }}
            </p>
            <p class="text-sm text-gray-300">
                {{ t('account.pages.settings.page.delete.lines.2') }}
            </p>
            <br />
            <button
                type="button"
                class="px-4 py-2 bg-red-500 hover:bg-red-600 text-white rounded text-sm font-medium transition-colors"
            >
                {{ t('account.pages.settings.page.delete.button.label') }}
            </button>
        </div>
    </CardComponent>
</template>
