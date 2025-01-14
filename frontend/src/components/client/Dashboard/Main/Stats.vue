<template>
  <div class="grid grid-cols-3 gap-4">
    <CardComponent v-for="(stat, index) in stats" :key="index">
      <component :is="stat.icon" class="w-6 h-6 text-purple-500 mb-2" />
      <div class="text-3xl font-bold text-white mb-1">{{ stat.value }}</div>
      <div class="text-purple-200 text-sm">{{ stat.label }}</div>
    </CardComponent>
  </div>
</template>
<script lang="ts" setup>
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import {
  Server as ServerIcon,
  FileText as FileTextIcon,
  Ticket as TicketIcon,
} from 'lucide-vue-next';
const stats = [
  { icon: ServerIcon, value: '2', label: 'Services' },
  { icon: FileTextIcon, value: '0', label: 'Unpaid Invoices' },
  { icon: TicketIcon, value: '1', label: 'Tickets' },
];
</script>
