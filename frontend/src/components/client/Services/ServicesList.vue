<script setup lang="ts">
import { ref, onMounted } from 'vue';
import type { Service } from '@/mythicalclient/Services/Services';
import Services from '@/mythicalclient/Services/Services';
import CardComponent from '../ui/Card/CardComponent.vue';
import Button from '../ui/Button.vue';
import { ServerIcon, PackageIcon, TagIcon, BoxIcon } from 'lucide-vue-next';
import { useRouter } from 'vue-router';

const props = defineProps<{
    categoryUri: string;
}>();

const services = ref<Service[]>([]);
const loading = ref(true);
const error = ref<string | null>(null);
const router = useRouter();

const formatPrice = (price: string | null): string => {
    if (!price) return 'N/A';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
    }).format(Number(price));
};

const loadServices = async () => {
    try {
        loading.value = true;
        services.value = await Services.getServicesByCategory(props.categoryUri);
    } catch (err) {
        error.value = 'Failed to load services';
        console.error(err);
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    loadServices();
});
</script>

<template>
    <div class="services-grid">
        <!-- Loading State -->
        <div v-if="loading" class="flex justify-center items-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
        </div>

        <!-- Error State -->
        <div v-else-if="error" class="text-red-500 text-center py-4">
            {{ error }}
        </div>

        <!-- Services Grid -->
        <div v-else class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
            <CardComponent v-for="service in services" :key="service.id" class="relative overflow-hidden">
                <!-- Service Status Badge -->
                <div class="absolute top-4 right-4">
                    <span
                        :class="[
                            'px-2.5 py-1 rounded-full text-xs font-medium',
                            service.enabled === 'true'
                                ? 'bg-emerald-500/20 text-emerald-400'
                                : 'bg-red-500/20 text-red-400',
                        ]"
                    >
                        {{ service.enabled === 'true' ? 'Available' : 'Unavailable' }}
                    </span>
                </div>

                <!-- Service Header -->
                <div class="p-6 border-b border-gray-800">
                    <div class="flex items-center gap-3 mb-3">
                        <ServerIcon class="w-5 h-5 text-purple-500" />
                        <h3 class="text-xl font-bold text-white">
                            {{ service.name }}
                        </h3>
                    </div>
                    <div class="flex items-center gap-2 text-sm text-purple-500 mb-4">
                        <TagIcon class="w-4 h-4" />
                        {{ service.tagline }}
                    </div>
                    <p class="text-gray-400">
                        {{ service.shortdescription }}
                    </p>
                </div>

                <!-- Service Details -->
                <div class="p-6">
                    <!-- Stock Info -->
                    <div v-if="service.stock_enabled === 'true'" class="flex items-center gap-2 mb-4 text-gray-400">
                        <BoxIcon class="w-4 h-4" />
                        <span>{{ service.stock }}/{{ service.quantity }} available</span>
                    </div>

                    <!-- Pricing -->
                    <div class="space-y-3">
                        <div class="flex items-center justify-between">
                            <div class="flex items-center gap-2">
                                <PackageIcon class="w-4 h-4 text-purple-500" />
                                <span class="text-gray-300">Monthly</span>
                            </div>
                            <span class="text-lg font-bold text-white" v-if="service.price !== 0">
                                {{ formatPrice(service.price.toString()) }}
                            </span>
                            <span class="text-lg font-bold text-white" v-else> Free </span>
                        </div>

                        <!-- Setup Fee -->
                        <div v-if="service.setup_fee > 0" class="mt-2 text-sm text-gray-500 flex items-center gap-2">
                            <span
                                class="w-4 h-4 rounded-full bg-purple-500/20 flex items-center justify-center text-purple-400"
                                >+</span
                            >
                            {{ formatPrice(service.setup_fee.toString()) }} setup fee
                        </div>
                    </div>

                    <!-- Action Button -->
                    <div class="mt-6">
                        <Button
                            :disabled="service.enabled !== 'true'"
                            variant="primary"
                            class="w-full"
                            @click="router.push(`/order/${props.categoryUri}/${service.uri}`)"
                        >
                            <template #icon>
                                <ServerIcon class="w-4 h-4" />
                            </template>
                            Order Now
                        </Button>
                    </div>
                </div>
            </CardComponent>
        </div>
    </div>
</template>
