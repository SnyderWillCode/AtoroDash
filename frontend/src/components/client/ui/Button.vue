<template>
    <button
        :type="type"
        :disabled="disabled || loading"
        :class="[
            'flex items-center gap-2 px-4 py-2 rounded-lg transition-colors font-medium',
            {
                'opacity-50 cursor-not-allowed': disabled || loading,
                // Primary variant
                'bg-purple-600 text-white hover:bg-purple-700': variant === 'primary',
                // Secondary variant
                'bg-gray-600/20 text-gray-300 hover:bg-gray-600/30': variant === 'secondary',
                // Danger variant
                'bg-red-500/20 text-red-400 hover:bg-red-500/30': variant === 'danger',
                // Success variant
                'bg-green-500/20 text-green-400 hover:bg-green-500/30': variant === 'success',
            },
            {
                'px-2 py-1 text-sm': small,
            },
            className,
        ]"
        @click="$emit('click', $event)"
    >
        <LoaderIcon v-if="loading" class="w-4 h-4 animate-spin" />
        <slot v-else name="icon"></slot>
        <slot>{{ text }}</slot>
    </button>
</template>

<script setup lang="ts">
import { Loader as LoaderIcon } from 'lucide-vue-next';

interface Props {
    text?: string;
    type?: 'button' | 'submit' | 'reset';
    variant?: 'primary' | 'secondary' | 'danger' | 'success';
    disabled?: boolean;
    loading?: boolean;
    small?: boolean;
    className?: string;
}

defineProps<Props>();
defineEmits<{
    (e: 'click', event: MouseEvent): void;
}>();
</script>
