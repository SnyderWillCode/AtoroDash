<template>
    <router-view v-slot="{ Component }">
        <component :is="Component" />
    </router-view>
</template>

<script lang="ts">
import { defineComponent } from 'vue';
import { useRouter } from 'vue-router';

export default defineComponent({
    name: 'App',
    setup() {
        const router = useRouter();

        router.beforeEach((to, from, next) => {
            window.scrollTo(0, 0);
            next();
        });
    },
});
</script>
