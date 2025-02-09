<template>
    <div class="fixed inset-0 z-50 flex items-center justify-center bg-gradient">
        <div class="text-center">
            <div class="w-16 h-16 mb-4 mx-auto">
                <svg class="animate-spin" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle>
                    <path
                        class="opacity-75"
                        fill="currentColor"
                        d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"
                    ></path>
                </svg>
            </div>
            <div class="text-xl font-bold bg-gradient-to-r from-purple-400 to-purple-600 bg-clip-text text-transparent">
                {{ $t('components.loading.title') }}
            </div>
        </div>
    </div>
</template>

<script setup lang="ts"></script>

<style scoped>
.bg-gradient {
    background: radial-gradient(circle at center, #2d1b69 0%, #1a103f 50%, #0f0a24 100%);
}

@keyframes spin {
    to {
        transform: rotate(360deg);
    }
}

.animate-spin {
    animation: spin 1s linear infinite;
}
</style>
