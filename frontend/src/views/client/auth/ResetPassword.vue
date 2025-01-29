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
import Settings from '@/mythicalclient/Settings';
import { useRouter } from 'vue-router';
import Auth from '@/mythicalclient/Auth';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
const { play: playError } = useSound(failedAlertSfx);
const { play: playSuccess } = useSound(successAlertSfx);
const router = useRouter();
const { t } = useI18n();

const loading = ref(false);
const form = reactive({
    password: '',
    confirmPassword: '',
    turnstileResponse: '',
});

MythicalDOM.setPageTitle(t('auth.pages.reset_password.page.title'));

const checkResetCode = async (code: string) => {
    try {
        const response = await Auth.isLoginVerifyTokenValid(code);
        if (!response.success) {
            location.href = '/auth/login?invalid_code';
        }
    } catch (error) {
        console.error('Error checking reset code:', error);
    }
};

const init = async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const resetCode = urlParams.get('token');

    if (resetCode) {
        await checkResetCode(resetCode);
    } else {
        alert('Missing reset code');
    }
};

init();

// This function is called when the form is submitted
const handleSubmit = async () => {
    const urlParams = new URLSearchParams(window.location.search);
    const resetCode = urlParams.get('token');
    loading.value = true;

    const response = await Auth.resetPassword(
        form.confirmPassword,
        form.password,
        resetCode || '',
        form.turnstileResponse,
    );

    try {
        if (!response.success) {
            const error_code = response.error_code as keyof typeof errorMessages;

            const errorMessages = {
                TURNSTILE_FAILED: t('auth.pages.reset_password.alerts.error.cloudflare_error'),
                PASSWORDS_DO_NOT_MATCH: t('auth.pages.reset_password.alerts.error.passwords_mismatch'),
                INVALID_CODE: t('auth.pages.reset_password.alerts.error.invalid_code'),
            };

            if (errorMessages[error_code]) {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.reset_password.alerts.error.title'),
                    text: errorMessages[error_code],
                    footer: t('auth.pages.reset_password.alerts.error.footer'),
                    showConfirmButton: true,
                });
                loading.value = false;
                throw new Error('Forgot Password failed');
            }
        } else {
            playSuccess();
            Swal.fire({
                icon: 'success',
                title: t('auth.pages.reset_password.alerts.success.title'),
                text: t('auth.pages.reset_password.alerts.success.reset_success'),
                footer: t('auth.pages.reset_password.alerts.success.footer'),
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
        <FormCard :title="t('auth.pages.reset_password.page.subTitle')" @submit="handleSubmit">
            <FormInput
                id="password"
                :label="$t('auth.pages.reset_password.page.form.password_new.label')"
                v-model="form.password"
                type="password"
                :placeholder="t('auth.pages.reset_password.page.form.password_new.placeholder')"
                required
            />
            <FormInput
                id="confirmPassword"
                :label="$t('auth.pages.reset_password.page.form.password_confirm.label')"
                v-model="form.confirmPassword"
                type="password"
                :placeholder="t('auth.pages.reset_password.page.form.password_confirm.placeholder')"
                required
            />

            <button
                type="submit"
                class="w-full mt-6 px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white rounded-lg transition-colors"
                :disabled="loading"
            >
                {{
                    loading
                        ? t('auth.pages.reset_password.page.form.reset_button.loading')
                        : t('auth.pages.reset_password.page.form.reset_button.label')
                }}
            </button>

            <div
                v-if="Settings.getSetting('turnstile_enabled') == 'true'"
                style="display: flex; justify-content: center; margin-top: 20px"
            >
                <Turnstile :site-key="Settings.getSetting('turnstile_key_pub')" v-model="form.turnstileResponse" />
            </div>

            <p class="mt-4 text-center text-sm text-gray-400">
                {{ t('auth.pages.reset_password.page.form.login.label') }}
                <router-link to="/auth/login" class="text-purple-400 hover:text-purple-300">
                    {{ t('auth.pages.reset_password.page.form.login.link') }}
                </router-link>
            </p>
        </FormCard>
    </Layout>
</template>
