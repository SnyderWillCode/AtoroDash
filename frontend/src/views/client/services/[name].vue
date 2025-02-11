<template>
    <LayoutDashboard>
        <div v-if="category">
            <!-- Content will go here once we have the category -->
            <div class="flex items-center gap-3">
                <h1 class="text-2xl font-bold">{{ category.name }}</h1>
                <span
                    class="px-2 py-1 text-sm font-medium rounded-full bg-blue-100 text-blue-800 dark:bg-blue-900 dark:text-blue-200"
                >
                    {{ category.tagline }}
                </span>
            </div>
            <p class="text-gray-500 dark:text-gray-400 mb-4">{{ category.headline }}</p>

            <ServicesList :categoryUri="category.uri" />

            <CardComponent
                cardTitle="What's available?"
                cardDescription="Here are the services available in this category."
                v-if="futures.length > 0"
                class="mt-4"
            >
                <div class="divide-y divide-gray-200 dark:divide-gray-700">
                    <div v-for="future in futures" :key="future.id" class="flex items-start py-4 first:pt-0 last:pb-0">
                        <CheckCircleIcon class="h-5 w-5 text-green-500 mt-0.5 shrink-0" aria-hidden="true" />
                        <div class="ml-3">
                            <h3 class="text-lg font-medium">{{ future.name }}</h3>
                            <p class="mt-1 text-gray-600 dark:text-gray-300">
                                {{ future.description }}
                            </p>
                        </div>
                    </div>
                </div>
            </CardComponent>
        </div>
    </LayoutDashboard>
</template>

<script setup lang="ts">
import { onMounted, ref } from 'vue';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import ServiceCategories from '@/mythicalclient/Services/ServiceCategories';
import { useRoute, useRouter } from 'vue-router';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import { CheckCircleIcon } from 'lucide-vue-next';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
import Services from '@/mythicalclient/Services/Services';
import ServicesList from '@/components/client/Services/ServicesList.vue';
import Swal from 'sweetalert2';
interface Future {
    id: number;
    name: string;
    category: number;
    description: string;
    enabled: string;
    deleted: string;
    locked: string;
    date: string;
}

interface Category {
    id: number;
    name: string;
    uri: string;
    headline: string;
    tagline: string;
    enabled: string;
    date: string;
    futures: Future[];
}

const route = useRoute();
const router = useRouter();
const category = ref<Category | null>(null);
const futures = ref<Future[]>([]);

onMounted(async () => {
    try {
        const categoryName = route.params.name as string;
        if (!categoryName) {
            console.error('Category name is required');
            Swal.fire({
                title: 'Error',
                text: 'Category name is required',
                icon: 'error',
            });
            await router.push('/');
            return;
        }

        // First check if the URI exists
        if (!(await ServiceCategories.doesUriExist(categoryName))) {
            console.error('Category URI does not exist: ' + categoryName);
            Swal.fire({
                title: 'Error',
                text: 'Category URI does not exist: ' + categoryName,
                icon: 'error',
            });
            await router.push('/');
            return;
        }

        // Get basic category data
        const categoryData = await ServiceCategories.getCategoryByUri(categoryName);
        if (!categoryData) {
            console.error('Category data not found');
            Swal.fire({
                title: 'Error',
                text: 'Category data not found',
                icon: 'error',
            });
            await router.push('/');
            return;
        }

        // Get detailed category info including futures
        const detailedCategory = await ServiceCategories.getCategoryInfo(categoryData.id);
        if (!detailedCategory) {
            console.error('Detailed category info not found');
            Swal.fire({
                title: 'Error',
                text: 'Detailed category info not found',
                icon: 'error',
            });
            await router.push('/');
            return;
        }
        MythicalDOM.setPageTitle('Services | ' + categoryData.name);

        const services = await Services.getServicesByCategory(categoryName);
        console.log(services);
        category.value = categoryData;
        futures.value = detailedCategory.futures;
    } catch (error) {
        console.error('Error fetching category:', error);
        Swal.fire({
            title: 'Error',
            text: 'Error fetching category: ' + error,
            icon: 'error',
        });
        await router.push('/');
    }
});
</script>
