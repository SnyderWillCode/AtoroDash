<script setup lang="ts">
import { reactive, ref, onMounted } from 'vue';
import LayoutAccount from './Layout.vue';
import TextInput from '@/components/client/ui/TextForms/TextInput.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Session from '@/mythicalclient/Session';
import { useI18n } from 'vue-i18n';
import Swal from 'sweetalert2';
import Auth from '@/mythicalclient/Auth';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
import Button from '@/components/client/ui/Button.vue';

const { t } = useI18n();
MythicalDOM.setPageTitle(t('account.pages.settings.page.title'));

const form = reactive({
    firstName: Session.getInfo('first_name'),
    lastName: Session.getInfo('last_name'),
    email: Session.getInfo('email'),
    avatar: Session.getInfo('avatar'),
    background: Session.getInfo('background'),
});

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

const formatBytes = (bytes: number): string => {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
};

const getTotalStorageSize = (): number => {
    try {
        let total = 0;

        // Calculate localStorage size
        try {
            for (let i = 0; i < localStorage.length; i++) {
                const key = localStorage.key(i);
                if (key) {
                    const value = localStorage.getItem(key);
                    total += new Blob([value || '']).size;
                }
            }
        } catch {
            return 0;
        }

        // Calculate sessionStorage size
        try {
            for (let i = 0; i < sessionStorage.length; i++) {
                const key = sessionStorage.key(i);
                if (key) {
                    const value = sessionStorage.getItem(key);
                    total += new Blob([value || '']).size;
                }
            }
        } catch {
            return 0;
        }

        return total;
    } catch {
        return 0;
    }
};

const clearAllData = async () => {
    const result = await Swal.fire({
        title: t('account.pages.settings.page.clear.cache.title'),
        text: t('account.pages.settings.page.clear.cache.description'),
        icon: 'warning',
        showCancelButton: true,
        confirmButtonText: t('account.pages.settings.page.clear.cache.confirm'),
        cancelButtonText: t('account.pages.settings.page.clear.cache.cancel'),
        confirmButtonColor: '#dc2626',
        background: '#1f2937',
        color: '#fff',
    });

    if (result.isConfirmed) {
        localStorage.clear();
        sessionStorage.clear();
        window.location.reload();
    }
};

onMounted(() => {
    totalSize.value = getTotalStorageSize();
});

const totalSize = ref(getTotalStorageSize());
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
            <Button @click="saveChanges" variant="primary" type="submit">
                {{ t('account.pages.settings.page.form.update_button.label') }}
            </Button>
            <Button @click="resetFields" variant="secondary" type="submit">
                {{ t('account.pages.settings.page.form.update_button.reset') }}
            </Button>
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
            <Button type="button" variant="danger">
                {{ t('account.pages.settings.page.delete.button.label') }}
            </Button>
        </div>
    </CardComponent>
    <br />
    <CardComponent
        :cardTitle="t('account.pages.settings.page.clear.title')"
        :cardDescription="t('account.pages.settings.page.clear.subTitle')"
    >
        <div class="space-y-6">
            <div class="p-4 bg-gray-800/50 rounded-lg">
                <div class="flex justify-between items-center mb-4">
                    <div>
                        <h3 class="text-lg font-medium">{{ t('account.pages.settings.page.clear.cache.title') }}</h3>
                        <p class="text-sm text-gray-400 mt-1">
                            {{
                                t('account.pages.settings.page.clear.cache.total', {
                                    totalSize: formatBytes(totalSize),
                                })
                            }}
                        </p>
                    </div>
                    <Button @click="clearAllData" variant="danger" class="px-4 py-2">
                        {{ t('account.pages.settings.page.clear.cache.button') }}
                    </Button>
                </div>
                <p class="text-sm text-gray-300 mt-4">
                    {{ t('account.pages.settings.page.clear.cache.description') }}
                </p>
            </div>
        </div>
    </CardComponent>
</template>
