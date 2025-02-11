<script setup lang="ts">
defineProps({
    link: String,
});
</script>

<template>
    <a :href="`${link}`" class="bg-purple-600 text-white py-2 px-4 rounded-sm hover:bg-purple-500">{{
        $t('components.table.view')
    }}</a>
</template>
