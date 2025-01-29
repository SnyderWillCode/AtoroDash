<script setup lang="ts">
import { FileQuestionIcon } from 'lucide-vue-next';
import ErrorPage from '@/components/client/Errors/ErrorPage.vue';
import Translation from '@/mythicalclient/Translation';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();
MythicalDOM.setPageTitle(t('errors.notfound.title'));
</script>
<template>
    <ErrorPage
        :icon="FileQuestionIcon"
        :title="Translation.getTranslation('errors.notfound.title')"
        :message="Translation.getTranslation('errors.notfound.message')"
    />
</template>
