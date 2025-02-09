<script setup lang="ts">
import { ref, onMounted, onUnmounted, computed } from 'vue';
import { useRouter } from 'vue-router';
import LoadingScreen from '@/components/client/ui/LoadingScreen.vue';
import TopNavBar from '@/components/client/layout/TopNavBar.vue';
import Sidebar from '@/components/client/layout/Sidebar.vue';
import SearchModal from '@/components/client/layout/SearchModal.vue';
import NotificationsDropdown from '@/components/client/layout/NotificationsDropdown.vue';
import ProfileDropdown from '@/components/client/layout/ProfileDropdown.vue';
import { SettingsIcon, UsersIcon } from 'lucide-vue-next';
import Session from '@/mythicalclient/Session';
import StorageMonitor from '@/mythicalclient/StorageMonitor';
import MythicalClient from '@/mythicalclient/MythicalClient';
import { LicenseServer } from '@/mythicalclient/LicenseServer';
MythicalClient.download();

new StorageMonitor();

const router = useRouter();

if (!Session.isSessionValid()) {
  router.push('/auth/login');
}

try {
  Session.startSession();
} catch (error) {
  console.error('Session failed:', error);
}

const loading = ref(true);
const isSidebarOpen = ref(false);
const isSearchOpen = ref(false);
const isNotificationsOpen = ref(false);
const isProfileOpen = ref(false);

// Toggle functions
const toggleSidebar = () => {
  isSidebarOpen.value = !isSidebarOpen.value;
};

const closeSidebar = () => {
  isSidebarOpen.value = false;
};

const toggleSearch = () => {
  isSearchOpen.value = true;
  isNotificationsOpen.value = false;
  isProfileOpen.value = false;
};

const toggleNotifications = () => {
  isNotificationsOpen.value = !isNotificationsOpen.value;
  isProfileOpen.value = false;
  isSearchOpen.value = false;
};

const toggleProfile = () => {
  isProfileOpen.value = !isProfileOpen.value;
  isNotificationsOpen.value = false;
  isSearchOpen.value = false;
};

const closeSearch = () => {
  isSearchOpen.value = false;
};

const navigateToResult = (href: string) => {
  closeSearch();
  router.push(href);
};

const closeDropdowns = () => {
  isNotificationsOpen.value = false;
  isProfileOpen.value = false;
};

// Event handlers
const handleClickOutside = (event: MouseEvent) => {
  const target = event.target as HTMLElement | null;
  if (target && !target.closest('.dropdown') && !target.closest('button')) {
    closeDropdowns();
  }
};

const handleKeydown = (event: KeyboardEvent) => {
  if (event.ctrlKey && event.key === 'S') {
    event.preventDefault();
    toggleSearch();
  }
  if (event.key === 'Escape') {
    closeSearch();
    closeDropdowns();
    closeSidebar();
  }

  if ((event.ctrlKey || event.metaKey) && event.key === 'k') {
    event.preventDefault();
    toggleSearch();
  }
  // Ctrl + D to open search
  if ((event.ctrlKey || event.metaKey) && event.key === 'd') {
    event.preventDefault();
    toggleSearch();
  }
};

const handleVisibilityChange = () => {
  document.title = document.hidden ? `${document.title} - Inactive` : document.title.replace(' - Inactive', '');
};

// Lifecycle hooks
onMounted(() => {
  document.addEventListener('click', handleClickOutside);
  document.addEventListener('keydown', handleKeydown);
  document.addEventListener('visibilitychange', handleVisibilityChange);

  if (sessionStorage.getItem('firstLoad') === null) {
    loading.value = true;
    setTimeout(() => {
      loading.value = false;
      sessionStorage.setItem('firstLoad', 'false');
    }, 2000);
  } else {
    loading.value = false;
  }
});

onUnmounted(() => {
  document.removeEventListener('click', handleClickOutside);
  document.removeEventListener('keydown', handleKeydown);
  document.removeEventListener('visibilitychange', handleVisibilityChange);
});

// Computed properties
const profileMenu = computed(() => {
  const menu = [{ name: 'Settings', icon: SettingsIcon, href: '/account' }];
  const role = Session.getInfo('role_real_name') ?? '';
  if (['admin', 'administrator', 'support', 'supportbuddy'].includes(role)) {
    menu.splice(1, 0, { name: 'Admin Area', icon: UsersIcon, href: '/mc-admin' });
  }

  return menu;
});

const userInfo = computed(() => ({
  firstName: Session.getInfo('first_name'),
  lastName: Session.getInfo('last_name'),
  roleName: Session.getInfo('role_name'),
  email: Session.getInfo('email'),
  avatar: Session.getInfo('avatar'),
}));

const showFooter = ref(true);

onMounted(async () => {
  try {
    const isValid = await LicenseServer.isLicenseValid();
    showFooter.value = !isValid;
  } catch (error) {
    console.error('Error checking license:', error);
    showFooter.value = true;
  }
});
</script>
<template>
  <div class="min-h-screen bg-[#0a0a1f] relative overflow-hidden">
    <!-- Background elements -->
    <div class="absolute inset-0 bg-gradient-to-b from-[#0a0a1f] via-[#1a0b2e] to-[#0a0a1f]">
      <div class="stars"></div>
      <div class="mountains"></div>
    </div>

    <!-- Content wrapper -->
    <div class="relative z-10 min-h-screen">
      <LoadingScreen v-if="loading" />

      <template v-if="!loading">
        <!-- Backdrop for mobile sidebar -->
        <div v-if="isSidebarOpen" class="fixed inset-0 bg-black/50 backdrop-blur-sm z-40 lg:hidden"
          @click="closeSidebar"></div>

        <TopNavBar :isSidebarOpen="isSidebarOpen" @toggle-sidebar="toggleSidebar" @toggle-search="toggleSearch"
          @toggle-notifications="toggleNotifications" @toggle-profile="toggleProfile"
          class="bg-[#0a0a1f]/80 backdrop-blur-sm border-b border-purple-900/50" />

        <!-- Sidebar with updated styling -->
        <Sidebar :isSidebarOpen="isSidebarOpen"
          class="bg-[#0a0a1f]/90 backdrop-blur-md border-r border-purple-900/50" />

        <!-- Main Content -->
        <main class="pt-16 lg:pl-64 min-h-screen relative">
          <div class="p-4 md:p-6 max-w-7xl mx-auto">
            <slot></slot>
          </div>
        </main>

        <!-- Modals and dropdowns -->
        <SearchModal :isSearchOpen="isSearchOpen" @close="closeSearch" @navigate="navigateToResult"
          class="bg-[#0a0a1f]/95 backdrop-blur-lg" />

        <NotificationsDropdown :isOpen="isNotificationsOpen"
          class="bg-[#0a0a1f]/95 backdrop-blur-lg border border-purple-900/50" />

        <ProfileDropdown :isOpen="isProfileOpen" :profileMenu="profileMenu" :stats="{
          tickets: 0,
          services: 0,
          invoices: 0,
        }" :userInfo="{
                      firstName: userInfo.firstName || '',
                      lastName: userInfo.lastName || '',
                      roleName: userInfo.roleName || '',
                      email: userInfo.email || '',
                      avatar: userInfo.avatar || '',
                    }" class="bg-[#0a0a1f]/95 backdrop-blur-lg border border-purple-900/50" />

        <!-- Footer -->
        <footer v-if="showFooter" class="relative z-10 py-4 px-6 text-center text-sm text-gray-400">
          <a href="https://mythical.systems" class="hover:text-purple-400 transition-colors">
            MythicalSystems
          </a>
          <p>LTD 2020 - {{ new Date().getFullYear() }}</p>
        </footer>
      </template>
    </div>
  </div>
</template>

<style scoped>
.stars {
  position: absolute;
  inset: 0;
  background-image: radial-gradient(2px 2px at calc(random() * 100%) calc(random() * 100%), white, transparent);
  background-size: 200px 200px;
  animation: twinkle 8s infinite;
}

.mountains {
  position: absolute;
  bottom: 0;
  left: 0;
  right: 0;
  height: 30vh;
  background-image: linear-gradient(170deg, transparent 0%, #0a0a1f 80%),
    linear-gradient(150deg, #1a0b2e 0%, transparent 100%);
  clip-path: polygon(0 100%, 20% 65%, 40% 90%, 60% 60%, 80% 85%, 100% 50%, 100% 100%);
}

@keyframes twinkle {

  0%,
  100% {
    opacity: 0.8;
  }

  50% {
    opacity: 0.4;
  }
}

/* Mobile optimizations */
@media (max-width: 768px) {
  .mountains {
    height: 20vh;
  }
}
</style>
