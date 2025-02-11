<script setup lang="ts">
import { ref, reactive, watch } from 'vue';
import { useRouter, useRoute } from 'vue-router';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import TextInput from '@/components/client/ui/TextForms/TextInput.vue';
import TextArea from '@/components/client/ui/TextForms/TextArea.vue';
import Button from '@/components/client/ui/Button.vue';
import Order from '@/mythicalclient/Services/Order';
import { ServerIcon, TagIcon, ShoppingCartIcon, WrenchIcon, PackageIcon } from 'lucide-vue-next';
import type { OrderResponse } from '@/mythicalclient/Services/Order';
import Swal from 'sweetalert2';
const route = useRoute();
const router = useRouter();
const category = route.params.category as string;
const service = route.params.service as string;

const loading = ref(true);
const error = ref<string | null>(null);
const submitting = ref(false);

const order = ref<OrderResponse | null>(null);
const formData = reactive<Record<string, string>>({});

const formatFieldName = (field: string) => {
    return field
        .split('_')
        .map((word) => word.charAt(0).toUpperCase() + word.slice(1))
        .join(' ');
};

const formatPrice = (price: number): string => {
    if (price === 0) return 'Free';
    return new Intl.NumberFormat('en-US', {
        style: 'currency',
        currency: 'EUR',
    }).format(price);
};

const loadOrderDetails = async () => {
    try {
        const response = await Order.getOrder(category, service);
        console.log('Order response:', response);
        order.value = response;
        loading.value = false;
    } catch (err) {
        console.error('Failed to load order details:', err);
        error.value = err instanceof Error ? err.message : 'Failed to load order details. Please try again.';
        loading.value = false;
    }
};

const submitOrder = async () => {
    try {
        submitting.value = true;
        await Order.submitOrder(category, service, formData);
        Swal.fire({
            title: 'Success',
            text: 'Order submitted successfully',
            icon: 'success',
        });
        router.push('/invoices');
    } catch (err) {
        console.error('Failed to submit order:', err);
        error.value = err instanceof Error ? err.message : 'Failed to submit order. Please try again.';
    } finally {
        submitting.value = false;
    }
};

// Watch for changes in the order and handle null case
watch(order, (newOrder) => {
    if (newOrder === null) {
        console.error('Order is null, redirecting to services page');
        router.push(`/services/${category}`);
    }
});

// Call the function when component mounts
loadOrderDetails();
</script>
<template>
    <LayoutDashboard>
        <div class="space-y-6">
            <!-- Header -->
            <div class="flex items-center justify-between">
                <h1 class="text-2xl font-semibold text-gray-100">Order Service</h1>
                <router-link :to="`/services/${category}`">
                    <button
                        class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200"
                    >
                        Back to Services
                    </button>
                </router-link>
            </div>

            <div v-if="loading" class="flex justify-center items-center py-8">
                <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-purple-500"></div>
            </div>

            <div v-else-if="error" class="bg-red-500/10 border border-red-500/50 rounded-lg p-4 text-red-400">
                {{ error }}
            </div>

            <div v-else class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Service Details -->
                <div class="lg:col-span-2 space-y-6">
                    <CardComponent>
                        <div class="space-y-6">
                            <!-- Service Header -->
                            <div class="border-b border-gray-800 pb-6">
                                <div class="flex items-center gap-3 mb-3">
                                    <ServerIcon class="w-5 h-5 text-purple-500" />
                                    <h3 class="text-xl font-bold text-white">{{ order?.service?.service?.name }}</h3>
                                </div>
                                <div class="flex items-center gap-2 text-sm text-purple-500 mb-4">
                                    <TagIcon class="w-4 h-4" />
                                    {{ order?.service?.service?.tagline }}
                                </div>
                                <p class="text-gray-400">{{ order?.service?.service?.description }}</p>
                            </div>

                            <!-- Requirements Form -->
                            <form @submit.prevent="submitOrder" class="space-y-6">
                                <div
                                    v-for="(requirement, field) in order?.service?.requirements"
                                    :key="field"
                                    class="space-y-2"
                                >
                                    <label :for="field" class="block text-sm font-medium text-gray-300">
                                        {{ formatFieldName(field) }}
                                    </label>

                                    <template v-if="requirement.type === 'text'">
                                        <TextInput
                                            v-model="formData[field]"
                                            :id="field"
                                            :required="requirement.required"
                                            :placeholder="`Enter ${formatFieldName(field).toLowerCase()}`"
                                        />
                                    </template>

                                    <template v-else-if="requirement.type === 'textarea'">
                                        <TextArea
                                            v-model="formData[field]"
                                            :id="field"
                                            :required="requirement.required"
                                            rows="3"
                                            :placeholder="`Enter ${formatFieldName(field).toLowerCase()}`"
                                        />
                                    </template>

                                    <template v-else-if="requirement.type === 'select'">
                                        <select
                                            v-model="formData[field]"
                                            :id="field"
                                            :required="requirement.required"
                                            class="block w-full px-3 py-2 bg-gray-800/50 border border-gray-800/50 rounded-lg text-gray-300 focus:outline-hidden focus:border-purple-500 focus:ring-1 focus:ring-purple-500"
                                        >
                                            <option value="" disabled selected>Select an option</option>
                                            <option
                                                v-for="option in requirement.options"
                                                :key="option.value"
                                                :value="option.value"
                                            >
                                                {{ option }}
                                            </option>
                                        </select>
                                    </template>

                                    <template v-else-if="requirement.type === 'checkbox'">
                                        <div class="flex items-center gap-2">
                                            <input
                                                type="checkbox"
                                                v-model="formData[field]"
                                                :id="field"
                                                :required="requirement.required"
                                                class="text-purple-500 focus:ring-purple-500 h-4 w-4"
                                            />
                                            <label :for="field" class="text-gray-300">{{ requirement.label }}</label>
                                        </div>
                                    </template>

                                    <template v-else-if="requirement.type === 'number'">
                                        <TextInput
                                            v-model="formData[field]"
                                            :id="field"
                                            type="number"
                                            :required="requirement.required"
                                            :placeholder="`Enter ${formatFieldName(field).toLowerCase()}`"
                                        />
                                    </template>

                                    <template v-else-if="requirement.type === 'password'">
                                        <TextInput
                                            v-model="formData[field]"
                                            :id="field"
                                            type="password"
                                            :required="requirement.required"
                                            :placeholder="`Enter ${formatFieldName(field).toLowerCase()}`"
                                        />
                                    </template>

                                    <template v-else-if="requirement.type === 'email'">
                                        <TextInput
                                            v-model="formData[field]"
                                            :id="field"
                                            type="email"
                                            :required="requirement.required"
                                            :placeholder="`Enter ${formatFieldName(field).toLowerCase()}`"
                                        />
                                    </template>
                                </div>

                                <Button type="submit" variant="primary" class="w-full" :loading="submitting">
                                    <template #icon>
                                        <ShoppingCartIcon class="w-4 h-4" />
                                    </template>
                                    Place Order
                                </Button>
                            </form>
                        </div>
                    </CardComponent>
                </div>

                <!-- Order Summary -->
                <div class="space-y-6">
                    <CardComponent cardTitle="Order Summary">
                        <div class="space-y-4">
                            <!-- Price -->
                            <div
                                class="flex justify-between items-center p-4 bg-gray-800/50 rounded-lg border border-purple-900/50"
                            >
                                <div class="flex items-center gap-2">
                                    <PackageIcon class="w-4 h-4 text-purple-500" />
                                    <span class="text-gray-300">{{
                                        (order?.service?.service as any)?.price === 0 ? 'Free' : 'Monthly'
                                    }}</span>
                                </div>
                                <span
                                    class="text-lg font-bold"
                                    :class="
                                        (order?.service?.service as any)?.price === 0
                                            ? 'text-emerald-400'
                                            : 'text-white'
                                    "
                                >
                                    {{ formatPrice((order?.service?.service as any)?.price || 0) }}
                                </span>
                            </div>

                            <!-- Setup Fee -->
                            <div
                                v-if="(order?.service?.service?.setup_fee || 0) > 0"
                                class="flex justify-between items-center p-4 bg-gray-800/50 rounded-lg border border-purple-900/50"
                            >
                                <div class="flex items-center gap-2">
                                    <WrenchIcon class="w-4 h-4 text-purple-500" />
                                    <span class="text-gray-300">Setup Fee</span>
                                </div>
                                <span class="text-lg font-bold text-white">
                                    {{ formatPrice(order?.service?.service?.setup_fee || 0) }}
                                </span>
                            </div>
                        </div>
                    </CardComponent>
                </div>
            </div>
        </div>
    </LayoutDashboard>
</template>
