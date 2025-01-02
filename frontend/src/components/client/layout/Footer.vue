<template>
    <footer class="fixed bottom-0 left-0 right-0 bg-gray-900/50 backdrop-blur-sm border-t border-gray-700/50 py-4 z-50">
        <div class="flex justify-between items-center text-gray-400 text-sm px-6">
            <!-- Left side -->
            <div class="flex items-center gap-4">
                <span>Copyright &copy; 2020-{{ currentYear }} MythicalSystems</span>
            </div>
            <!-- Right side -->
            <div class="flex gap-4">
                <RouterLink to="/terms" class="hover:text-purple-400 transition-colors">Terms</RouterLink>
                <RouterLink to="/privacy" class="hover:text-purple-400 transition-colors">Privacy</RouterLink>
                <RouterLink to="/support" class="hover:text-purple-400 transition-colors">Support</RouterLink>
            </div>
        </div>
    </footer>
</template>

<script setup lang="ts">
import { computed } from 'vue';

const currentYear = computed(() => new Date().getFullYear());
</script>
