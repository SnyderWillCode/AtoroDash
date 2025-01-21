<script setup lang="ts">
import { ref, nextTick, onMounted, onUnmounted } from 'vue';
import { XIcon, ChevronLeftIcon, ChevronRightIcon } from 'lucide-vue-next';
import Announcements from '@/mythicalclient/Announcements';
import { useI18n } from 'vue-i18n';
const { t } = useI18n();

interface Announcement {
    id: number;
    title: string;
    shortDescription: string;
    description: string;
    images: string[];
    date: Date;
    tags: string[];
}

const announcements = ref<Announcement[]>([]);
const loading = ref(true);

const fetchAnnouncements = async () => {
    try {
        const data = await Announcements.fetchAnnouncements();
        announcements.value = data.map(
            (item: {
                id: number;
                title: string;
                shortDescription: string;
                description: string;
                assets: { images: string[] }[];
                date: string;
                tags: { tag: string }[];
            }) => ({
                id: item.id,
                title: item.title,
                shortDescription: item.shortDescription,
                description: item.description,
                images: item.assets.map((asset: { images: string[] }) => asset.images),
                date: new Date(item.date),
                tags: item.tags.map((tag: { tag: string }) => tag.tag),
            }),
        );
    } catch (err) {
        console.error('Error captured:', err);
        return false;
    } finally {
        loading.value = false;
    }
};

onMounted(() => {
    fetchAnnouncements();
});

// Modal state and other functions remain the same
const isModalOpen = ref(false);
const isModalMounted = ref(false);
const selectedAnnouncement = ref<Announcement | null>(null);
const currentImageIndex = ref(0);
const scrollContainer = ref<HTMLElement | null>(null);

// Utility functions
function formatDate(date: Date | null): string {
    if (!date) return '';

    const now = new Date();
    const diff = now.getTime() - date.getTime();
    const minutes = Math.floor(diff / (1000 * 60));
    const hours = Math.floor(diff / (1000 * 60 * 60));
    const days = Math.floor(diff / (1000 * 60 * 60 * 24));

    if (minutes < 60) return `${minutes}m ago`;
    if (hours < 24) return `${hours}h ago`;
    if (days === 0) return 'Today';
    if (days === 1) return 'Yesterday';
    if (days < 7) return `${days} days ago`;
    if (days < 30) return `${Math.floor(days / 7)} weeks ago`;

    return date.toLocaleDateString('en-US', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
    });
}

// Scroll functions
function scroll(direction: 'left' | 'right') {
    if (!scrollContainer.value) return;

    const scrollAmount = 400; // Width of one card
    const currentScroll = scrollContainer.value.scrollLeft;
    const targetScroll = direction === 'left' ? currentScroll - scrollAmount : currentScroll + scrollAmount;

    scrollContainer.value.scrollTo({
        left: targetScroll,
        behavior: 'smooth',
    });
}

function scrollToEnd() {
    if (!scrollContainer.value) return;
    scrollContainer.value.scrollTo({
        left: scrollContainer.value.scrollWidth,
        behavior: 'smooth',
    });
}

// Modal functions
async function openAnnouncement(announcement: Announcement) {
    selectedAnnouncement.value = announcement;
    currentImageIndex.value = 0; // Reset image index
    isModalOpen.value = true;
    await nextTick();
    isModalMounted.value = true;
}

function closeModal() {
    isModalMounted.value = false;
    setTimeout(() => {
        isModalOpen.value = false;
        selectedAnnouncement.value = null;
    }, 300);
}

// Keyboard event listener
function handleEscKey(e: KeyboardEvent) {
    if (e.key === 'Escape' && isModalOpen.value) {
        closeModal();
    }
}

onMounted(() => {
    document.addEventListener('keydown', handleEscKey);
});

onUnmounted(() => {
    document.removeEventListener('keydown', handleEscKey);
});
</script>
<template>
    <div class="space-y-6">
        <div class="flex items-center justify-between mb-6">
            <h2 class="text-2xl font-semibold text-white">{{ t('Components.Announcements.Title') }}</h2>
            <button
                @click="scrollToEnd"
                class="text-[#a855f7] hover:text-[#9333ea] transition-colors duration-200 text-sm"
            >
                {{ t('Components.Announcements.Card.ReadMore') }}
            </button>
        </div>

        <!-- Loading spinner -->
        <div v-if="loading" class="flex justify-center items-center h-64">
            <div class="loader"></div>
        </div>

        <!-- Horizontal scrolling container -->
        <div v-else class="relative group">
            <div ref="scrollContainer" class="flex space-x-6 overflow-x-auto pb-4 snap-x snap-mandatory scrollbar-hide">
                <div
                    v-for="announcement in announcements"
                    :key="announcement.id"
                    class="flex-none w-[400px] snap-start group/card bg-gray-900/50 backdrop-blur-sm border border-gray-700/50 rounded-xl overflow-hidden transition-all duration-300 hover:border-[#a855f7]/50"
                >
                    <!-- Image container -->
                    <div class="aspect-video relative overflow-hidden">
                        <img
                            :src="announcement.images[0]"
                            :alt="announcement.title"
                            class="w-full h-full object-cover transition-transform duration-300 group-hover/card:scale-105"
                        />
                    </div>

                    <!-- Content -->
                    <div class="p-6 space-y-4">
                        <div class="space-y-2">
                            <div class="flex items-center justify-between">
                                <h3
                                    class="text-lg font-semibold text-white group-hover/card:text-[#a855f7] transition-colors duration-300"
                                >
                                    {{ announcement.title }}
                                </h3>
                                <span class="text-xs text-gray-400">
                                    {{ formatDate(announcement.date) }}
                                </span>
                            </div>
                            <p class="text-gray-400 text-sm line-clamp-2">
                                {{ announcement.shortDescription }}
                            </p>
                        </div>

                        <!-- Tags -->
                        <div class="flex flex-wrap gap-2">
                            <span
                                v-for="tag in announcement.tags"
                                :key="tag"
                                class="px-2 py-1 rounded-md text-xs bg-gray-800 text-gray-300"
                            >
                                {{ tag }}
                            </span>
                        </div>

                        <button
                            @click="openAnnouncement(announcement)"
                            class="mt-4 block w-full px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded transition-colors text-center text-sm"
                        >
                            {{ t('Components.Announcements.Card.ReadMore') }}
                        </button>
                    </div>
                </div>
            </div>

            <!-- Scroll buttons -->
            <button
                @click="scroll('left')"
                class="absolute left-0 top-1/2 -translate-y-1/2 -translate-x-4 opacity-0 group-hover:opacity-100 group-hover:translate-x-2 transition-all duration-300 p-2 rounded-full bg-gray-900/90 text-white hover:bg-gray-800"
            >
                <ChevronLeftIcon class="w-6 h-6" />
            </button>
            <button
                @click="scroll('right')"
                class="absolute right-0 top-1/2 -translate-y-1/2 translate-x-4 opacity-0 group-hover:opacity-100 group-hover:-translate-x-2 transition-all duration-300 p-2 rounded-full bg-gray-900/90 text-white hover:bg-gray-800"
            >
                <ChevronRightIcon class="w-6 h-6" />
            </button>
        </div>
    </div>

    <!-- Enhanced Modal -->
    <Teleport to="body">
        <div v-if="isModalOpen" class="fixed inset-0 z-50 flex items-center justify-center p-4" @click="closeModal">
            <div
                class="fixed inset-0 bg-black/70 backdrop-blur-sm transition-opacity"
                :class="{ 'opacity-0': !isModalMounted, 'opacity-100': isModalMounted }"
            ></div>

            <div
                class="relative w-full max-w-3xl max-h-[90vh] bg-[#12121f] rounded-xl overflow-hidden shadow-xl transform transition-all"
                :class="{ 'opacity-0 scale-95': !isModalMounted, 'opacity-100 scale-100': isModalMounted }"
                @click.stop
            >
                <button
                    @click="closeModal"
                    class="absolute top-4 right-4 z-10 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors duration-300"
                >
                    <XIcon class="w-5 h-5" />
                </button>

                <div class="overflow-y-auto h-full max-h-[90vh]">
                    <div v-if="selectedAnnouncement" class="relative">
                        <!-- Image carousel -->
                        <div class="relative aspect-video">
                            <div
                                class="flex transition-transform duration-300 ease-out"
                                :style="{ transform: `translateX(-${currentImageIndex * 100}%)` }"
                            >
                                <img
                                    v-for="(image, index) in selectedAnnouncement.images"
                                    :key="index"
                                    :src="image"
                                    :alt="`${selectedAnnouncement.title} - Image ${index + 1}`"
                                    class="w-full h-full object-cover flex-shrink-0"
                                />
                            </div>

                            <!-- Image navigation -->
                            <div
                                v-if="selectedAnnouncement.images.length > 1"
                                class="absolute bottom-4 left-1/2 -translate-x-1/2 flex space-x-2"
                            >
                                <button
                                    v-for="(_, index) in selectedAnnouncement.images"
                                    :key="index"
                                    @click="currentImageIndex = index"
                                    class="w-2 h-2 rounded-full transition-colors duration-200"
                                    :class="index === currentImageIndex ? 'bg-white' : 'bg-white/50'"
                                ></button>
                            </div>

                            <!-- Arrow navigation -->
                            <button
                                v-if="selectedAnnouncement.images.length > 1 && currentImageIndex > 0"
                                @click="currentImageIndex--"
                                class="absolute left-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors duration-200"
                            >
                                <ChevronLeftIcon class="w-5 h-5" />
                            </button>
                            <button
                                v-if="
                                    selectedAnnouncement.images.length > 1 &&
                                    currentImageIndex < selectedAnnouncement.images.length - 1
                                "
                                @click="currentImageIndex++"
                                class="absolute right-4 top-1/2 -translate-y-1/2 p-2 rounded-full bg-black/50 text-white hover:bg-black/70 transition-colors duration-200"
                            >
                                <ChevronRightIcon class="w-5 h-5" />
                            </button>
                        </div>

                        <div class="p-8 space-y-6">
                            <div class="space-y-4">
                                <div class="flex items-center justify-between">
                                    <h3 class="text-3xl font-bold text-white">
                                        {{ selectedAnnouncement.title }}
                                    </h3>
                                    <span class="text-sm text-gray-400">
                                        {{ formatDate(selectedAnnouncement.date) }}
                                    </span>
                                </div>

                                <!-- Tags -->
                                <div class="flex flex-wrap gap-2">
                                    <span
                                        v-for="tag in selectedAnnouncement.tags"
                                        :key="tag"
                                        class="px-3 py-1 rounded-full text-sm bg-gray-800 text-gray-300"
                                    >
                                        {{ tag }}
                                    </span>
                                </div>
                            </div>

                            <!-- Full description -->
                            <div class="prose prose-invert prose-lg max-w-none">
                                <p class="text-gray-300 whitespace-pre-line leading-relaxed">
                                    {{ selectedAnnouncement.description }}
                                </p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </Teleport>
</template>
<style scoped>
.scrollbar-hide {
    -ms-overflow-style: none;
    scrollbar-width: none;
}

.scrollbar-hide::-webkit-scrollbar {
    display: none;
}

.line-clamp-2 {
    display: -webkit-box;
    -webkit-line-clamp: 2;
    -webkit-box-orient: vertical;
    overflow: hidden;
    line-clamp: 2;
    /* Standard property */
}

/* Smooth transition for hover effects */
.transition-all {
    transition-property: all;
    transition-timing-function: cubic-bezier(0.4, 0, 0.2, 1);
    transition-duration: 300ms;
}

/* Hide scrollbar for all elements */
* {
    scrollbar-width: none;
    /* Firefox */
}

*::-webkit-scrollbar {
    display: none;
    /* WebKit */
}

/* Loader styles */
.loader {
    border: 4px solid rgba(255, 255, 255, 0.3);
    border-left-color: #ffffff;
    border-radius: 50%;
    width: 40px;
    height: 40px;
    animation: spin 1s linear infinite;
}

@keyframes spin {
    0% {
        transform: rotate(0deg);
    }

    100% {
        transform: rotate(360deg);
    }
}
</style>
