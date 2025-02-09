<script setup lang="ts">
import { ref, onMounted, computed, onBeforeUnmount, watch, nextTick } from 'vue';
import TextInput from './TextInput.vue';

const props = defineProps({
    modelValue: String,
    options: {
        type: Array as () => Array<{ value: string; label: string }>,
        default: () => [],
    },
    inputClass: {
        type: String,
        default:
            'w-full bg-gray-800/50 border border-gray-700/50 rounded-lg pl-4 pr-10 py-2 text-sm text-gray-100 focus:border-purple-500/50 focus:ring-1 focus:ring-purple-500/50 focus:outline-none',
    },
});

const emit = defineEmits(['update:modelValue']);

const inputValue = computed({
    get: () => props.modelValue,
    set: (value) => emit('update:modelValue', value),
});

const searchQuery = ref('');
const isDropdownOpen = ref(false);
const searchInputRef = ref<HTMLInputElement | null>(null);
const optionsRef = ref<HTMLUListElement | null>(null);
const selectedIndex = ref(-1);

const filteredOptions = computed(() => {
    return props.options.filter((option) => option.label.toLowerCase().includes(searchQuery.value.toLowerCase()));
});

onMounted(() => {
    if (!props.modelValue && props.options.length > 0) {
        emit('update:modelValue', props.options[0].value);
    }

    document.addEventListener('click', handleClickOutside);
});

onBeforeUnmount(() => {
    document.removeEventListener('click', handleClickOutside);
});

const handleClickOutside = (event: MouseEvent) => {
    const target = event.target as HTMLElement;
    if (!target.closest('.dropdown-container')) {
        isDropdownOpen.value = false;
    }
};

const selectOption = (value: string) => {
    inputValue.value = value;
    isDropdownOpen.value = false;
};

const handleKeydown = (event: KeyboardEvent) => {
    if (event.key === 'ArrowDown') {
        event.preventDefault();
        selectedIndex.value = Math.min(selectedIndex.value + 1, filteredOptions.value.length - 1);
        scrollToSelectedOption();
    } else if (event.key === 'ArrowUp') {
        event.preventDefault();
        selectedIndex.value = Math.max(selectedIndex.value - 1, -1);
        scrollToSelectedOption();
    } else if (event.key === 'Enter') {
        event.preventDefault();
        if (selectedIndex.value >= 0 && selectedIndex.value < filteredOptions.value.length) {
            selectOption(filteredOptions.value[selectedIndex.value].value);
        }
    }
};

const scrollToSelectedOption = () => {
    nextTick(() => {
        const selectedElement = optionsRef.value?.children[selectedIndex.value] as HTMLElement;
        if (selectedElement) {
            selectedElement.scrollIntoView({ block: 'nearest' });
        }
    });
};

watch(isDropdownOpen, async (newValue) => {
    if (newValue) {
        await nextTick();
        searchInputRef.value?.focus();
        selectedIndex.value = -1;
    } else {
        searchQuery.value = '';
    }
});

watch(filteredOptions, () => {
    selectedIndex.value = -1;
});
</script>

<template>
    <div class="relative dropdown-container">
        <div :class="inputClass" @click="isDropdownOpen = !isDropdownOpen">
            <span>{{ props.options.find((option) => option.value === inputValue)?.label || 'Select an option' }}</span>
        </div>
        <div v-if="isDropdownOpen" class="absolute z-10 w-full bg-gray-800 border border-gray-700 rounded-lg mt-1">
            <TextInput
                v-model="searchQuery"
                :placeholder="$t('components.search.placeholder')"
                class="mb-2"
                ref="searchInputRef"
                @keydown="handleKeydown"
            />
            <ul ref="optionsRef">
                <li
                    v-for="(option, index) in filteredOptions"
                    :key="option.value"
                    @click="selectOption(option.value)"
                    @mouseover="selectedIndex = index"
                    :class="['px-4 py-2 cursor-pointer hover:bg-gray-700', { 'bg-gray-700': index === selectedIndex }]"
                >
                    {{ option.label }}
                </li>
            </ul>
        </div>
    </div>
</template>

<style scoped>
.dropdown-container {
    position: relative;
}

.dropdown-container > div {
    cursor: pointer;
}

.dropdown-container ul {
    max-height: 200px;
    overflow-y: auto;
}

.dropdown-container li {
    transition: background-color 0.2s;
}
</style>
