<script setup lang="ts">
import { ref, reactive } from 'vue';
import Layout from '@/components/client/Layout.vue';
import FormCard from '@/components/client/Auth/FormCard.vue';
import FormInput from '@/components/client/Auth/FormInput.vue';
import { useRouter } from 'vue-router';
import Turnstile from 'vue-turnstile';
import { useSettingsStore } from '@/stores/settings';
const Settings = useSettingsStore();
import { useI18n } from 'vue-i18n';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';

const { t } = useI18n();
const router = useRouter();

MythicalDOM.setPageTitle(t('auth.pages.sso.page.title'));

const loading = ref(false);
const form = reactive({
    name: '',
    turnstileResponse: '',
});

const handleSubmit = async () => {
    loading.value = true;
    try {
        localStorage.setItem('domain_name', form.name);
        await new Promise((resolve) => setTimeout(resolve, 2000));
        router.push('/auth/login');
    } catch (error) {
        console.error(error);
    } finally {
        loading.value = false;
    }
};
</script>
<template>
    <Layout>
        <FormCard :title="$t('auth.pages.sso.page.subTitle')" @submit="handleSubmit">
            <FormInput
                id="text"
                :label="$t('auth.pages.sso.page.form.name.label')"
                v-model="form.name"
                :placeholder="$t('auth.pages.sso.page.form.name.placeholder')"
                required
            />
            <p class="mt-4 text-center text-sm text-gray-400">
                {{ $t('auth.pages.sso.page.form.name.description') }}
            </p>
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
                        ? $t('auth.pages.sso.page.form.login_button.loading')
                        : $t('auth.pages.sso.page.form.login_button.label')
                }}
            </button>

            <p class="mt-4 text-center text-sm text-gray-400">
                {{ $t('auth.pages.sso.page.form.normal_login.label') }}
                <router-link to="/auth/login" class="text-purple-400 hover:text-purple-300">
                    {{ $t('auth.pages.sso.page.form.normal_login.label') }}
                </router-link>
            </p>
        </FormCard>
    </Layout>
</template>
