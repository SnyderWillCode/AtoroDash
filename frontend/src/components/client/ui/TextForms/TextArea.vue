<template>
    <textarea v-model="inputValue" :class="inputClass" :placeholder="placeholder"></textarea>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const props = defineProps({
    modelValue: String,
    inputClass: {
        type: String,
        default:
            'w-full bg-gray-800/50 border border-gray-700/50 rounded-lg pl-4 pr-10 py-2 text-sm text-gray-100 placeholder-gray-500 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 focus:outline-none',
    },
    placeholder: {
        type: String,
        default: '',
    },
});

const emit = defineEmits(['update:modelValue']);

const inputValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});
</script>
