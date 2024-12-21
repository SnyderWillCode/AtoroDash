<script setup lang="ts">
import { ref, onMounted, onErrorCaptured } from 'vue'
import axios from 'axios'
import { format } from 'date-fns'
import LayoutAccount from './Layout.vue'

interface Activity {
    id: number
    user: string
    action: string
    ip_address: string
    deleted: string
    locked: string
    date: string
}

interface ActivityResponse {
    code: number
    error: null | string
    message: string
    success: boolean
    activities: Activity[]
}

const activities = ref<Activity[]>([])
const loading = ref(true)
const error = ref<string | null>(null)

const fetchActivities = async () => {
    try {
        console.log('Fetching activities...')
        const response = await axios.get<ActivityResponse>('/api/user/session/activities')
        console.log('Response received:', response.data)
        if (response.data.success) {
            activities.value = response.data.activities
            console.log('Activities loaded:', activities.value)
        } else {
            throw new Error(response.data.error || 'Failed to fetch activities')
        }
    } catch (err) {
        console.error('Error fetching activities:', err)
        error.value = err instanceof Error ? err.message : 'An unknown error occurred'
    } finally {
        loading.value = false
    }
}

onMounted(() => {
    console.log('Component mounted')
    fetchActivities()
})

onErrorCaptured((err) => {
    console.error('Error captured:', err)
    error.value = 'An unexpected error occurred'
    return false
})

const columns = [
    { key: 'id', title: 'ID' },
    { key: 'action', title: 'Action' },
    { key: 'ip_address', title: 'IP Address' },
    { key: 'date', title: 'Date' },
    { key: 'deleted', title: 'Deleted' },
    { key: 'locked', title: 'Locked' },
]

const formatDate = (date: string) => format(new Date(date), 'MMM d, yyyy HH:mm:ss')
</script>

<template>
    <LayoutAccount>
        <div class="p-4">
            <h2 class="text-2xl font-bold mb-4">User Activities</h2>
            <div v-if="loading" class="text-center py-4">
                <div class="inline-block animate-spin rounded-full h-8 w-8 border-t-2 border-b-2 border-gray-900"></div>
                <p class="mt-2">Loading activities...</p>
            </div>
            <div v-else-if="error" class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                <p>{{ error }}</p>
            </div>
            <div v-else-if="activities.length === 0" class="text-center py-4">
                <p>No activities found.</p>
            </div>
            <div v-else class="overflow-x-auto">
                <table class="min-w-full bg-white border border-gray-300">
                    <thead>
                        <tr>
                            <th v-for="column in columns" :key="column.key"
                                class="px-6 py-3 bg-gray-100 text-left text-xs leading-4 font-medium text-gray-500 uppercase tracking-wider">
                                {{ column.title }}
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="activity in activities" :key="activity.id" class="border-b border-gray-200">
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{ activity.id }}
                            </td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-900">{{ activity.action
                                }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{
                                activity.ip_address }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{
                                formatDate(activity.date) }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{ activity.deleted
                                }}</td>
                            <td class="px-6 py-4 whitespace-no-wrap text-sm leading-5 text-gray-500">{{ activity.locked
                                }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </LayoutAccount>
</template>

<style scoped>
.overflow-x-auto::-webkit-scrollbar {
    display: none;
}

.overflow-x-auto {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>