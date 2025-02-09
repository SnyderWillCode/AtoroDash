<script setup lang="ts">
import { ref, reactive } from 'vue';
import Layout from '@/components/client/Layout.vue';
import FormCard from '@/components/client/Auth/FormCard.vue';
import FormInput from '@/components/client/Auth/FormInput.vue';
import Swal from 'sweetalert2';
import Settings from '@/mythicalclient/Settings';
import Turnstile from 'vue-turnstile';
import { useI18n } from 'vue-i18n';
import { useRouter } from 'vue-router';
import successAlertSfx from '@/assets/sounds/success.mp3';
import failedAlertSfx from '@/assets/sounds/error.mp3';
import { useSound } from '@vueuse/sound';
import Auth from '@/mythicalclient/Auth';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

const { play: playError } = useSound(failedAlertSfx);
const { play: playSuccess } = useSound(successAlertSfx);
const router = useRouter();
const { t } = useI18n();

const loading = ref(false);
const form = reactive({
    firstName: '',
    lastName: '',
    username: '',
    email: '',
    password: '',
    turnstileResponse: '',
});

MythicalDOM.setPageTitle(t('auth.pages.register.page.title'));

const handleSubmit = async () => {
    loading.value = true;
    try {
        const response = await Auth.register(
            form.firstName,
            form.lastName,
            form.email,
            form.username,
            form.password,
            form.turnstileResponse,
        );

        if (!response.success) {
            const error_code = response.error_code as keyof typeof errorMessages;

            const errorMessages = {
                TURNSTILE_FAILED: t('auth.pages.register.alerts.error.cloudflare_error'),
                USERNAME_ALREADY_IN_USE: t('auth.pages.register.alerts.error.username_exists'),
                EMAIL_ALREADY_IN_USE: t('auth.pages.register.alerts.error.email_exists'),
                DATABASE_ERROR: t('auth.pages.register.alerts.error.generic'),
            };

            if (errorMessages[error_code]) {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.register.alerts.error.title'),
                    text: errorMessages[error_code],
                    footer: t('auth.pages.register.alerts.error.footer'),
                    showConfirmButton: true,
                });
                throw new Error('Registration failed');
            } else {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.register.alerts.error.title'),
                    text: t('auth.pages.register.alerts.error.generic'),
                    showConfirmButton: true,
                    footer: t('auth.pages.register.alerts.error.footer'),
                });
                throw new Error('Registration failed');
            }
        }
        playSuccess();
        Swal.fire({
            icon: 'success',
            title: t('auth.pages.register.alerts.success.title'),
            text: t('auth.pages.register.alerts.success.register_success'),
            footer: t('auth.pages.register.alerts.success.footer'),
            showConfirmButton: true,
        });
        setTimeout(() => {
            router.push('/auth/login');
        }, 1500);
    } catch (error) {
        console.error('Register failed:', error);
    } finally {
        loading.value = false;
    }
};
</script>
<template>
    <Layout>
        <FormCard :title="t('auth.pages.register.page.subTitle')" @submit="handleSubmit">
            <div class="flex space-x-4">
                <FormInput
                    id="firstName"
                    :label="t('auth.pages.register.page.form.firstName.label')"
                    v-model="form.firstName"
                    :placeholder="t('auth.pages.register.page.form.firstName.placeholder')"
                    required
                />
                <FormInput
                    id="lastName"
                    :label="t('auth.pages.register.page.form.lastName.label')"
                    v-model="form.lastName"
                    :placeholder="t('auth.pages.register.page.form.lastName.placeholder')"
                    required
                />
            </div>
            <FormInput
                id="username"
                :label="t('auth.pages.register.page.form.username.label')"
                v-model="form.username"
                :placeholder="t('auth.pages.register.page.form.username.placeholder')"
                required
            />
            <FormInput
                id="email"
                :label="t('auth.pages.register.page.form.email.label')"
                v-model="form.email"
                :placeholder="t('auth.pages.register.page.form.email.placeholder')"
                type="email"
                required
            />

            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm text-gray-400">{{
                    t('auth.pages.register.page.form.password.label')
                }}</label>
            </div>

            <FormInput
                id="password"
                type="password"
                v-model="form.password"
                :placeholder="t('auth.pages.register.page.form.password.placeholder')"
                required
            />
            <button
                type="submit"
                class="w-full mt-6 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
                :disabled="loading"
            >
                {{
                    loading
                        ? t('auth.pages.register.page.form.register_button.loading')
                        : t('auth.pages.register.page.form.register_button.label')
                }}
            </button>

            <div
                v-if="Settings.getSetting('turnstile_enabled') == 'true'"
                style="display: flex; justify-content: center; margin-top: 20px"
            >
                <Turnstile :site-key="Settings.getSetting('turnstile_key_pub')" v-model="form.turnstileResponse" />
            </div>

            <p class="mt-4 text-center text-sm text-gray-400">
                {{ t('auth.pages.register.page.form.login.label') }}
                <router-link to="/auth/login" class="text-purple-400 hover:text-purple-300">
                    {{ t('auth.pages.register.page.form.login.link') }}
                </router-link>
            </p>
        </FormCard>
    </Layout>
</template>
