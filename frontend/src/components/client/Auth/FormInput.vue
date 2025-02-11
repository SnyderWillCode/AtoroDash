<template>
    <div class="mb-4">
        <label v-if="label" :for="id" class="block text-sm text-gray-400 mb-1">
            {{ label }}
        </label>
        <div class="relative">
            <input
                :id="id"
                :type="showPassword ? 'text' : type"
                :value="modelValue"
                @input="$emit('update:modelValue', ($event.target as HTMLInputElement)?.value || '')"
                class="w-full px-4 py-2 bg-[#1a1a2e] border border-gray-700 rounded-lg text-white placeholder-gray-500 focus:outline-hidden focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                :placeholder="placeholder"
                :required="required"
                :maxlength="maxChar"
                :disabled="locked"
            />
            <button
                v-if="type === 'password'"
                @click="showPassword = !showPassword"
                type="button"
                class="absolute right-3 top-1/2 -translate-y-1/2 text-gray-400 hover:text-gray-300"
            >
                <EyeIcon v-if="showPassword" class="w-5 h-5" />
                <EyeOffIcon v-else class="w-5 h-5" />
            </button>
        </div>
    </div>
</template>

<script setup lang="ts">
import { ref } from 'vue';
import { EyeIcon, EyeOffIcon } from 'lucide-vue-next';

defineProps({
    id: String,
    label: String,
    modelValue: String,
    type: {
        type: String,
        default: 'text',
    },
    placeholder: String,
    required: Boolean,
    maxChar: {
        type: Number,
        default: null,
    },
    locked: {
        type: Boolean,
        default: false,
    },
});

defineEmits(['update:modelValue']);

const showPassword = ref(false);
</script>
