<template>
<CardComponent>
  <div class="flex items-center justify-between mb-4">
    <h2 class="text-lg font-semibold text-white">Your Active Products/Services</h2>
    <button class="text-purple-500 hover:text-white transition-colors">
      <MenuIcon class="w-5 h-5" />
    </button>
  </div>
  <div class="space-y-4">
    <div v-for="(server, index) in servers" :key="index"
      class="flex items-center justify-between py-3 border-b border-purple-700 last:border-0">
      <div>
        <div class="font-medium text-white">{{ server.name }}</div>
        <div class="text-sm text-purple-500">{{ server.hostname }}</div>
      </div>
      <div class="flex items-center gap-3">
        <span class="px-2 py-1 bg-emerald-500/20 text-emerald-400 rounded text-xs font-medium">
          Active
        </span>
        <button
          class="px-3 py-1 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-sm">
          Manage
        </button>
      </div>
    </div>
  </div>
</CardComponent>

</template>
<script setup lang="ts">
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import { ref } from 'vue';
import { MenuIcon } from 'lucide-vue-next';

const servers = ref([
  {
    name: 'Storage Root Server Frankfurt - Storage KVM S',
    hostname: 'backup2.mythical.systems',
  },
  {
    name: 'Storage Root Server Frankfurt - Storage KVM S',
    hostname: 'backup.mythical.systems',
  },
]);
</script>
