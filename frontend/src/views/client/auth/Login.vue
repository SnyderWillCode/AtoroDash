<script setup lang="ts">
import { ref, reactive } from 'vue';
import Layout from '@/components/client/Layout.vue';
import FormCard from '@/components/client/Auth/FormCard.vue';
import FormInput from '@/components/client/Auth/FormInput.vue';
import Swal from 'sweetalert2';
import { useRouter } from 'vue-router';
import Turnstile from 'vue-turnstile';
import Settings from '@/mythicalclient/Settings';
import { useSound } from '@vueuse/sound';
import failedAlertSfx from '@/assets/sounds/error.mp3';
import successAlertSfx from '@/assets/sounds/success.mp3';
import Auth from '@/mythicalclient/Auth';
import Cookies from 'js-cookie';
import { useI18n } from 'vue-i18n';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

const { t } = useI18n();
const { play: playError } = useSound(failedAlertSfx);
const { play: playSuccess } = useSound(successAlertSfx);
const router = useRouter();
Cookies.remove('user_token');
localStorage.clear();
sessionStorage.clear();
MythicalDOM.setPageTitle(t('auth.pages.login.page.title'));

const loading = ref(false);
const form = reactive({
    email: '',
    password: '',
    turnstileResponse: '',
});
const domainName = localStorage.getItem('domain_name');

const handleSubmit = async () => {
    try {
        loading.value = true;
        const response = await Auth.login(form.email, form.password, form.turnstileResponse);
        if (!response.success) {
            const error_code = response.error_code as keyof typeof errorMessages;

            const errorMessages = {
                TURNSTILE_FAILED: t('auth.pages.login.alerts.error.cloudflare_error'),
                INVALID_CREDENTIALS: t('auth.pages.login.alerts.error.invalid_credentials'),
                ACCOUNT_NOT_VERIFIED: t('auth.pages.login.alerts.error.not_verified'),
                ACCOUNT_BANNED: t('auth.pages.login.alerts.error.banned'),
                ACCOUNT_DELETED: t('auth.pages.login.alerts.error.deleted'),
            };

            if (errorMessages[error_code]) {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.login.alerts.error.title'),
                    text: errorMessages[error_code],
                    footer: t('auth.pages.login.alerts.error.footer'),
                    showConfirmButton: true,
                });
                loading.value = false;
                throw new Error('Login failed');
            } else {
                playError();
                Swal.fire({
                    icon: 'error',
                    title: t('auth.pages.login.alerts.error.title'),
                    text: t('auth.pages.login.alerts.error.generic'),
                    footer: t('auth.pages.login.alerts.error.footer'),
                    showConfirmButton: true,
                });
                loading.value = false;
                throw new Error('Login failed');
            }
        } else {
            playSuccess();
            Swal.fire({
                icon: 'success',
                title: t('auth.pages.login.alerts.success.title'),
                text: t('auth.pages.login.alerts.success.login_success'),
                footer: t('auth.pages.login.alerts.success.footer'),
                showConfirmButton: true,
            });
            loading.value = false;
            setTimeout(() => {
                router.push('/');
            }, 1500);
        }
    } catch (error) {
        console.error('Login failed:', error);
    } finally {
        loading.value = false;
    }
};

const isEnterpriseLogin = localStorage.getItem('domain_name') !== null;

if (isEnterpriseLogin) {
    document.title = `${t('auth.pages.login.page.subTitle')} - ${localStorage.getItem('domain_name')}`;
}
</script>
<template>
    <Layout>
        <FormCard
            :title="`${$t('auth.pages.login.page.subTitle')} ${domainName ? ` - ${domainName}` : ''}`"
            @submit="handleSubmit"
        >
            <FormInput
                id="email"
                :label="$t('auth.pages.login.page.form.email.label')"
                v-model="form.email"
                :placeholder="$t('auth.pages.login.page.form.email.placeholder')"
                required
            />
            <div class="flex items-center justify-between mb-2">
                <label class="block text-sm text-gray-400">{{ $t('auth.pages.login.page.form.password.label') }}</label>
                <router-link to="/auth/forgot-password" class="text-sm text-purple-400 hover:text-purple-300">
                    {{ $t('auth.pages.login.page.form.forgot_password') }}
                </router-link>
            </div>

            <FormInput
                id="password"
                type="password"
                v-model="form.password"
                :placeholder="t('auth.pages.login.page.form.password.placeholder')"
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
                        ? $t('auth.pages.login.page.form.login_button.loading')
                        : $t('auth.pages.login.page.form.login_button.label')
                }}
            </button>

            <p class="mt-4 text-center text-sm text-gray-400">
                {{ $t('auth.pages.login.page.form.register.label') }}
                <router-link to="/auth/register" class="text-purple-400 hover:text-purple-300">
                    {{ $t('auth.pages.login.page.form.register.link') }}
                </router-link>
            </p>
        </FormCard>
    </Layout>
</template>
