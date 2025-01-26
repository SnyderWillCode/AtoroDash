<script setup lang="ts">
import { ref, onMounted, watch, onUnmounted } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import Button from '@/components/client/ui/Button.vue';
import TextArea from '@/components/client/ui/TextForms/TextArea.vue';
import {
  User as UserIcon,
  Building as BuildingIcon,
  AlertTriangle as AlertTriangleIcon,
  Clock as ClockIcon,
  FileText as FileTextIcon,
  MessagesSquare as MessagesSquareIcon,
  Reply as ReplyIcon,
  Send as SendIcon,
  Loader as LoaderIcon,
  Lock as LockIcon,
  Unlock as UnlockIcon,
} from 'lucide-vue-next';
import Tickets from '@/mythicalclient/Tickets';
import Swal from 'sweetalert2';
import Settings from '@/mythicalclient/Settings';
import { useI18n } from 'vue-i18n';

const { t } = useI18n();

document.title = t('tickets.pages.ticket.title');

// Add these interfaces at the top of the script section
interface User {
  username: string;
  avatar: string;
  role: string;
  uuid: string;
}

interface Department {
  id: number;
  name: string;
  description: string;
  time_open: string;
  time_close: string;
  enabled: string;
  deleted: string;
  locked: string;
  date: string;
}

interface Attachment {
  id: number;
  ticket_id: number;
  file: string;
  date: string;
}

interface Ticket {
  id: number;
  user: User;
  department: Department;
  priority: string;
  status: string;
  service: string | null;
  title: string;
  description: string;
  deleted: string;
  locked: string;
  date: string;
  department_id: number;
}

interface Message {
  id: number;
  ticket: number;
  user: User;
  message: string;
  deleted: string;
  locked: string;
  date: string;
}

// Replace the current ticket and messages refs with:
const ticket = ref<Ticket>({
  id: 0,
  user: {
    username: '',
    avatar: '',
    role: '',
    uuid: '',
  },
  department: {
    id: 0,
    name: '',
    description: '',
    time_open: '',
    time_close: '',
    enabled: '',
    deleted: '',
    locked: '',
    date: '',
  },
  priority: '',
  status: '',
  service: null,
  title: '',
  description: '',
  deleted: '',
  locked: '',
  date: '',
  department_id: 0,
});

const router = useRouter();
const route = useRoute();
const messages = ref<Message[]>([]);
const isLoading = ref(true);
const newReply = ref('');
const attachments = ref<Attachment[]>([]);
const isSubmitting = ref(false);
const selectedFiles = ref<File[]>([]);
const previewUrls = ref<string[]>([]);

const formatDate = (dateString: string) => {
  return new Date(dateString).toLocaleString(undefined, {
    year: 'numeric',
    month: 'short',
    day: 'numeric',
    hour: '2-digit',
    minute: '2-digit',
  });
};

// Watch for loading completion to check if ticket exists
watch(isLoading, (newValue) => {
  if (!newValue && (!ticket.value || ticket.value.id === 0)) {
    // If loading is complete but ticket doesn't exist, redirect to home
    router.push({ name: 'Dashboard' });
    Swal.fire({
      icon: 'error',
      title: 'Oops...',
      text: t('tickets.pages.ticket.alerts.error.ticket_not_found'),
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
  }
});

// Fetch ticket data and messages
const fetchTicketData = async () => {
  isLoading.value = true;
  try {
    const ticketId = Number(route.params.id);
    if (isNaN(ticketId)) {
      throw new Error('Invalid ticket ID');
    }
    const response = await Tickets.getTicket(ticketId);
    ticket.value = response.ticket;
    messages.value = response.messages;
    attachments.value = response.attachments;
  } catch (error) {
    console.error('Failed to load ticket:', error);
    Swal.fire({
      icon: 'error',
      title: t('tickets.pages.ticket.alerts.error.title'),
      text: t('tickets.pages.ticket.alerts.error.ticket_not_found'),
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
    router.push({ name: 'Dashboard' });
  } finally {
    isLoading.value = false;
  }
};

// Refresh messages periodically
const refreshMessages = async () => {
  try {
    const ticketId = Number(route.params.id);
    if (isNaN(ticketId)) {
      throw new Error('Invalid ticket ID');
      return;
    }
    const response = await Tickets.getTicket(ticketId);
    messages.value = response.messages;
  } catch (error) {
    console.error('Failed to refresh messages:', error);
    Swal.fire({
      icon: 'error',
      title: t('tickets.pages.ticket.alerts.error.title'),
      text: t('tickets.pages.ticket.alerts.error.message_timeout'),
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
  }
};

// Replace the existing onMounted and onUnmounted sections with:
const pollInterval = ref<ReturnType<typeof setInterval> | null>(null);

onMounted(async () => {
  await fetchTicketData();

  // Only start polling if the component is still mounted
  if (ticket.value && ticket.value.id !== 0) {
    pollInterval.value = setInterval(refreshMessages, 10000);
  }
});

onUnmounted(() => {
  if (pollInterval.value) {
    clearInterval(pollInterval.value);
    pollInterval.value = null;
  }
});

const submitReply = async () => {
  if (!newReply.value.trim()) return;

  isSubmitting.value = true;
  try {
    const response = await Tickets.replyToTicket(ticket.value.id, newReply.value);
    messages.value.push(response.message);
    newReply.value = '';
  } catch (error) {
    console.error('Failed to submit reply:', error);
    Swal.fire({
      icon: 'error',
      title: t('tickets.pages.ticket.alerts.error.title'),
      text: t('tickets.pages.ticket.alerts.error.failed_reply'),
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
  } finally {
    await refreshMessages(); // Refresh messages after submitting reply
    await uploadAttachments();
    isSubmitting.value = false;
    await updateStatus('inprogress');
    window.location.reload();
    Swal.fire({
      icon: 'success',
      title: t('tickets.pages.ticket.alerts.success.reply_success'),
      text: t('tickets.pages.ticket.alerts.success.footer'),
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
  }
};

const updateStatus = async (newStatus: 'open' | 'closed' | 'waiting' | 'replied' | 'inprogress') => {
  try {
    await Tickets.updateTicketStatus(ticket.value.id, newStatus);
    ticket.value.status = newStatus;
  } catch (error) {
    Swal.fire({
      icon: 'error',
      title: t('tickets.pages.ticket.alerts.error.title'),
      text: t('tickets.pages.ticket.alerts.error.failed_status'),
      timer: 2000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
    console.error('Failed to update ticket status:', error);
  }
};

const uploadAttachments = async () => {
  for (const file of selectedFiles.value) {
    await Tickets.uploadAttachment(ticket.value.id, file);
  }
};

const handleFileUpload = (event: Event) => {
  const files = (event.target as HTMLInputElement).files;
  if (files) {
    const fileArray = Array.from(files);
    if (validateFiles(fileArray)) {
      selectedFiles.value = fileArray;
    }
  }
};
// Watch for changes in selectedFiles to generate preview URLs
watch(selectedFiles, (files: File[]) => {
  previewUrls.value = files.map((file: File) => URL.createObjectURL(file));
});

// Cleanup preview URLs when component is unmounted
onUnmounted(() => {
  previewUrls.value.forEach((url: string) => URL.revokeObjectURL(url));
});

const removeFile = (index: number) => {
  selectedFiles.value = selectedFiles.value.filter((_: File, i: number) => i !== index);
  URL.revokeObjectURL(previewUrls.value[index]);
  previewUrls.value = previewUrls.value.filter((_, i) => i !== index);
};

const validateFiles = (files: File[]): boolean => {
  const maxSize = 2 * 1024 * 1024; // 2MB
  const validTypes = ['image/png', 'image/jpeg', 'image/gif'];
  const maxFiles = 5;

  // Check total number of files including existing ones
  if (selectedFiles.value.length + files.length > maxFiles) {
    Swal.fire({
      icon: 'error',
      title: t('tickets.pages.ticket.alerts.error.title'),
      text: t('tickets.pages.ticket.alerts.error.maximum_files'),
      timer: 3000,
      showConfirmButton: false,
      toast: true,
      position: 'top-end',
    });
    return false;
  }

  // Rest of validation
  for (const file of files) {
    if (file.size > maxSize) {
      Swal.fire({
        icon: 'error',
        title: t('tickets.pages.ticket.alerts.error.file_too_large'),
        text: t('tickets.pages.ticket.alerts.error.file_too_large', { file: file.name }),
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
      });
      return false;
    }

    if (!validTypes.includes(file.type)) {
      Swal.fire({
        icon: 'error',
        title: t('tickets.pages.ticket.alerts.error.title'),
        text: t('tickets.pages.ticket.alerts.error.invalid_file_type', { file: file.name }),
        timer: 3000,
        showConfirmButton: false,
        toast: true,
        position: 'top-end',
      });
      return false;
    }
  }

  return true;
};


</script>

<style scoped>
.messages-enter-active,
.messages-leave-active {
  transition: all 0.3s ease;
}

.messages-enter-from,
.messages-leave-to {
  opacity: 0;
  transform: translateY(20px);
}
</style>
<template>
  <LayoutDashboard>
    <div class="min-h-screen text-gray-100">
      <div class="max-w-6xl mx-auto p-6">
        <div class="flex justify-between items-center">
          <h1 class="text-2xl font-semibold text-gray-100">{{ t('tickets.pages.ticket.title') }}</h1>
          <router-link to="/ticket">
            <button
              class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200">
              Back
            </button>
          </router-link>
        </div>
        <br />
        <h2 class="text-xl font-semibold flex items-center gap-2">
          <MessagesSquareIcon class="w-5 h-5 text-purple-400" />
          {{ t('tickets.pages.ticket.subTitle') }}
        </h2>
        <!-- Ticket Header -->
        <CardComponent class="rounded-lg p-6 mb-6">
          <div class="flex items-center justify-between mb-6">
            <div class="flex items-center gap-4">
              <h1 class="text-2xl font-bold">#{{ ticket.id }} - {{ ticket.title }}</h1>
              <span :class="[
                'px-3 py-1 rounded-full text-sm font-medium',
                {
                  'bg-blue-500/20 text-blue-400': ticket.status === 'inprogress',
                  'bg-red-500/20 text-red-400': ticket.status === 'closed',
                  'bg-green-500/20 text-green-400': ticket.status === 'open',
                  'bg-yellow-500/20 text-yellow-400': ticket.status === 'waiting',
                  'bg-purple-500/20 text-purple-400': ticket.status === 'replied',
                },
              ]">
                <span class="inline-block w-2 h-2 rounded-full mr-2" :class="{
                  'bg-blue-400': ticket.status === 'inprogress',
                  'bg-red-400': ticket.status === 'closed',
                  'bg-green-400': ticket.status === 'open',
                  'bg-yellow-400': ticket.status === 'waiting',
                  'bg-purple-400': ticket.status === 'replied',
                }"></span>
                {{ ticket.status === 'inprogress' ? 'In Progress' : ticket.status.charAt(0).toUpperCase() +
                  ticket.status.slice(1) }}
              </span>
            </div>
            <div class="flex gap-2">
              <button @click="updateStatus('closed')" v-if="ticket.status !== 'closed'"
                class="flex items-center gap-2 px-4 py-2 bg-red-500/20 text-red-400 rounded-lg hover:bg-red-500/30 transition-colors">
                <LockIcon class="w-4 h-4" />
                Close Ticket
              </button>
              <button @click="updateStatus('open')" v-if="ticket.status === 'closed'"
                class="flex items-center gap-2 px-4 py-2 bg-green-500/20 text-green-400 rounded-lg hover:bg-green-500/30 transition-colors">
                <UnlockIcon class="w-4 h-4" />
                Reopen Ticket
              </button>
            </div>
          </div>

          <div class="grid grid-cols-2 gap-6 mb-6">
            <div class="flex items-start gap-3">
              <UserIcon class="w-5 h-5 text-purple-400 mt-1" />
              <div>
                <p class="text-sm text-gray-400">Created by</p>
                <div class="flex items-center mt-1">
                  <img :src="ticket.user.avatar" :alt="ticket.user.username" class="w-8 h-8 rounded-full mr-2" />
                  <div>
                    <p class="font-medium">{{ ticket.user.username }}</p>
                    <p class="text-sm text-gray-400">({{ ticket.user.role }})</p>
                  </div>
                </div>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <BuildingIcon class="w-5 h-5 text-purple-400 mt-1" />
              <div>
                <p class="text-sm text-gray-400">Department</p>
                <p class="font-medium mt-1">{{ ticket.department.name }}</p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <AlertTriangleIcon class="w-5 h-5 text-purple-400 mt-1" />
              <div>
                <p class="text-sm text-gray-400">Priority</p>
                <p :class="[
                  'font-medium mt-1',
                  {
                    'text-red-400': ticket.priority === 'high',
                    'text-yellow-400': ticket.priority === 'medium',
                    'text-green-400': ticket.priority === 'low',
                  },
                ]">
                  {{ ticket.priority === 'high' ? 'High' : ticket.priority === 'medium' ? 'Medium' : 'Low' }}
                </p>
              </div>
            </div>

            <div class="flex items-start gap-3">
              <ClockIcon class="w-5 h-5 text-purple-400 mt-1" />
              <div>
                <p class="text-sm text-gray-400">Created</p>
                <p class="font-medium mt-1">{{ formatDate(ticket.date) }}</p>
              </div>
            </div>
          </div>

          <div class="border-t border-gray-700 pt-6">
            <h2 class="text-lg font-semibold mb-2 flex items-center gap-2">
              <FileTextIcon class="w-5 h-5 text-purple-400" />
              Description
            </h2>
            <p class="text-gray-300 whitespace-pre-wrap">{{ ticket.description }}</p>
          </div>
          <br />
          <div v-if="attachments.length > 0">
            <h2 class="text-lg font-semibold mb-2 flex items-center gap-2">
              <FileTextIcon class="w-5 h-5 text-purple-400" />
              Attachments
            </h2>
            <div v-for="attachment in attachments" :key="attachment.id" class="flex items-center gap-2">
              <a :href="'https://'+Settings.getSetting('app_url')+'/attachments/' + attachment.file" target="_blank"
                class="text-blue-400 hover:underline">https://{{Settings.getSetting('app_url')}}/attachments/{{ attachment.file }}</a>
            </div>
          </div>
        </CardComponent>

        <!-- Messages -->
        <div class="space-y-6">
          <h2 class="text-xl font-semibold flex items-center gap-2">
            <MessagesSquareIcon class="w-5 h-5 text-purple-400" />
            Messages
          </h2>

          <TransitionGroup name="messages" tag="div" class="space-y-4">
            <CardComponent v-for="message in messages" :key="message.id" class="rounded-lg p-4">
              <div class="flex items-start justify-between mb-3">
                <div class="flex items-center gap-3">
                  <img :src="message.user.avatar" :alt="message.user.username" class="w-10 h-10 rounded-full" />
                  <div>
                    <div class="flex items-center gap-2">
                      <span class="font-medium">{{ message.user.username }}</span>
                      <span class="text-sm text-gray-400">({{ message.user.role }})</span>
                    </div>
                    <p class="text-sm text-gray-400">{{ formatDate(message.date) }}</p>
                  </div>
                </div>
              </div>
              <p class="text-gray-300 whitespace-pre-wrap">{{ message.message }}</p>
            </CardComponent>
          </TransitionGroup>

          <h2 class="text-xl font-semibold flex items-center gap-2">
            <MessagesSquareIcon class="w-5 h-5 text-purple-400" />
            Response
          </h2>

          <!-- Reply Form -->
          <CardComponent class="rounded-lg p-6" v-if="ticket.status !== 'closed'">
            <h3 class="text-lg font-semibold mb-4 flex items-center gap-2">
              <ReplyIcon class="w-5 h-5 text-purple-400" />
              Reply to Ticket
            </h3>

            <p class="text-sm text-gray-400 mb-4">
              Please provide as much detail as possible in your reply to help us assist you better.
              Include any relevant information or screenshots that might help resolve your issue.
            </p>

            <form @submit.prevent="submitReply" class="space-y-4">
              <div>
                <TextArea v-model="newReply" rows="4" placeholder="Type your reply here..."></TextArea>
              </div>

              <div class="mb-4">
                <label class="block text-sm font-medium text-gray-300 mb-2">
                  Attachments (Optional)
                </label>
                <div class="flex items-center justify-center w-full">
                  <label
                    class="flex flex-col items-center justify-center w-full h-32 border-2 border-gray-600 border-dashed rounded-lg cursor-pointer bg-gray-800/50 hover:bg-gray-800 transition-colors">
                    <div class="flex flex-col items-center justify-center pt-5 pb-6">
                      <svg class="w-8 h-8 mb-3 text-gray-500" aria-hidden="true" xmlns="http://www.w3.org/2000/svg"
                        fill="none" viewBox="0 0 20 16">
                        <path stroke="currentColor" stroke-linecap="round" stroke-linejoin="round" stroke-width="2"
                          d="M13 13h3a3 3 0 0 0 0-6h-.025A5.56 5.56 0 0 0 16 6.5 5.5 5.5 0 0 0 5.207 5.021C5.137 5.017 5.071 5 5 5a4 4 0 0 0 0 8h2.167M10 15V6m0 0L8 8m2-2 2 2" />
                      </svg>
                      <p class="mb-2 text-sm text-gray-400">
                        <span class="font-semibold">Click to upload</span> or drag and drop
                      </p>
                      <p class="text-xs text-gray-500">PNG, JPG or GIF (MAX. 2MB)</p>
                    </div>
                    <input type="file" class="hidden" accept="image/png,image/jpeg,image/gif" multiple
                      @change="handleFileUpload" />
                  </label>
                </div>
                <!-- Preview Images -->
                <div v-if="selectedFiles.length > 0" class="mt-4 flex flex-wrap gap-4">
                  <div v-for="(file, index) in selectedFiles" :key="index" class="relative">
                    <img :src="previewUrls[index]" class="w-24 h-24 object-cover rounded-lg" alt="Preview" />
                    <button @click="removeFile(index)"
                      class="absolute -top-2 -right-2 bg-red-500 text-white rounded-full p-1 hover:bg-red-600 transition-colors">
                      <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4" viewBox="0 0 20 20" fill="currentColor">
                        <path fill-rule="evenodd"
                          d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z"
                          clip-rule="evenodd" />
                      </svg>
                    </button>
                  </div>
                </div>
              </div>

              <div class="flex justify-end">
                <Button type="submit" :disabled="isSubmitting || !newReply.trim()" variant="primary">
                  <SendIcon class="w-4 h-4" v-if="!isSubmitting" />
                  <LoaderIcon class="w-4 h-4 animate-spin" v-else />
                  {{ isSubmitting ? 'Sending...' : 'Send Reply' }}
                </Button>
              </div>
            </form>
          </CardComponent>

          <div v-else class="bg-[#1A1825] rounded-lg p-6 text-center">
            <LockIcon class="w-6 h-6 text-gray-400 mx-auto mb-2" />
            <p class="text-gray-400">This ticket is closed. Reopen it to add replies.</p>
          </div>
        </div>
      </div>
    </div>
  </LayoutDashboard>
</template>
