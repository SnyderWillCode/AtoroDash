<script setup lang="ts">
import { ref, reactive } from 'vue';
import Layout from '@/components/client/Layout.vue';
import FormCard from '@/components/client/Auth/FormCard.vue';
import FormInput from '@/components/client/Auth/FormInput.vue';
import { useI18n } from 'vue-i18n';
import { useSound } from '@vueuse/sound';
import failedAlertSfx from '@/assets/sounds/error.mp3';
import successAlertSfx from '@/assets/sounds/success.mp3';
import Swal from 'sweetalert2';
import Turnstile from 'vue-turnstile';
import { useSettingsStore } from '@/stores/settings';
const Settings = useSettingsStore();
import { useRouter } from 'vue-router';
import Auth from '@/mythicalclient/Auth';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
const { play: playError } = useSound(failedAlertSfx);
const { play: playSuccess } = useSound(successAlertSfx);
const router = useRouter();
const { t } = useI18n();

const loading = ref(false);
const form = reactive({
    email: '',
    turnstileResponse: '',
});

MythicalDOM.setPageTitle(t('auth.pages.forgot_password.page.title'));

const handleSubmit = async () => {
    loading.value = true;
    const response = await Auth.forgotPassword(form.email, form.turnstileResponse);
    try {
        if (!response.success) {
            const error_code = response.error_code as keyof typeof errorMessages;

            const errorMessages = {
                TURNSTILE_FAILED: t('auth.pages.forgot_password.alerts.error.cloudflare_error'),
                EMAIL_DOES_NOT_EXIST: t('auth.pages.forgot_password.alerts.error.email_not_found'),
                FAILED_TO_SEND_EMAIL: t('auth.pages.forgot_password.alerts.error.generic'),
            };

            if (errorMessages[error_code]) {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.forgot_password.alerts.error.title'),
                    text: errorMessages[error_code],
                    footer: t('auth.pages.forgot_password.alerts.error.footer'),
                    showConfirmButton: true,
                });
                loading.value = false;
                throw new Error('Forgot Password failed');
            } else {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.forgot_password.alerts.error.title'),
                    text: t('auth.pages.forgot_password.alerts.error.generic'),
                    footer: t('auth.pages.forgot_password.alerts.error.footer'),
                    showConfirmButton: true,
                });
                loading.value = false;
                throw new Error('Failed');
            }
        } else {
            playSuccess();
            Swal.fire({
                icon: 'success',
                title: t('auth.pages.forgot_password.alerts.success.title'),
                text: t('auth.pages.forgot_password.alerts.success.reset_success'),
                footer: t('auth.pages.forgot_password.alerts.success.footer'),
                showConfirmButton: true,
            });
            setTimeout(() => {
                router.push('/auth/login');
            }, 1500);
        }
        console.log('Forgot password submitted:', form);
    } catch (error) {
        console.error('Forgot password failed:', error);
    } finally {
        loading.value = false;
    }
};
</script>

<template>
    <Layout>
        <FormCard :title="$t('auth.pages.forgot_password.page.subTitle')" @submit="handleSubmit">
            <FormInput
                id="email"
                :label="$t('auth.pages.forgot_password.page.form.email.label')"
                v-model="form.email"
                type="email"
                :placeholder="$t('auth.pages.forgot_password.page.form.email.placeholder')"
                required
            />
            <div
                v-if="Settings.getSetting('turnstile_enabled') == 'true'"
                style="display: flex; justify-content: center; margin-top: 20px"
            >
                <Turnstile :site-key="Settings.getSetting('turnstile_key_pub')" v-model="form.turnstileResponse" />
            </div>
            <button
                type="submit"
                class="w-full mt-6 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
                :disabled="loading"
            >
                {{
                    loading
                        ? t('auth.pages.forgot_password.page.form.reset_button.loading')
                        : t('auth.pages.forgot_password.page.form.reset_button.label')
                }}
            </button>

            <p class="mt-4 text-center text-sm text-gray-400">
                {{ t('auth.pages.forgot_password.page.form.login.label') }}
                <router-link to="/auth/login" class="text-purple-400 hover:text-purple-300">
                    {{ t('auth.pages.forgot_password.page.form.login.link') }}
                </router-link>
            </p>
        </FormCard>
    </Layout>
</template>
