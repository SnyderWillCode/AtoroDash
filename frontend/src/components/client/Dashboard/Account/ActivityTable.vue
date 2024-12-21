<script setup lang="ts">
import { format } from 'date-fns'

interface Activity {
    id: number
    user: string
    action: string
    ip_address: string
    deleted: string
    locked: string
    date: string
}

defineProps<{
    activities: Activity[]
}>()
</script>

<template>
    <div class="overflow-x-auto bg-gray-900 rounded-lg shadow">
        <table class="min-w-full divide-y divide-gray-800">
            <thead class="bg-gray-800">
                <tr>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                        Action
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                        IP Address
                    </th>
                    <th scope="col"
                        class="px-6 py-3 text-left text-xs font-medium text-gray-300 uppercase tracking-wider">
                        Date
                    </th>
                </tr>
            </thead>
            <tbody class="bg-gray-900 divide-y divide-gray-800">
                <tr v-for="activity in activities" :key="activity.id" class="hover:bg-gray-800 transition-colors">
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ activity.action }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">{{ activity.ip_address }}</td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-300">
                        {{ format(new Date(activity.date), 'MMM d, yyyy HH:mm:ss') }}
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</template>