<script setup lang="ts">
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import CardComponent from '@/components/client/ui/Card/CardComponent.vue';
import SelectInput from '@/components/client/ui/TextForms/SelectInput.vue';
import { ref, onMounted } from 'vue';
import TextInput from '@/components/client/ui/TextForms/TextInput.vue';
import TextArea from '@/components/client/ui/TextForms/TextArea.vue';
import Tickets from '@/mythicalclient/Tickets';
import Swal from 'sweetalert2';
import failedAlertSfx from '@/assets/sounds/error.mp3';
import successAlertSfx from '@/assets/sounds/success.mp3';
import { useI18n } from 'vue-i18n';
import { useSound } from '@vueuse/sound';
import { useRouter } from 'vue-router';

const { t } = useI18n();
const { play: playError } = useSound(failedAlertSfx);
const { play: playSuccess } = useSound(successAlertSfx);
const router = useRouter();

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

interface Service {
  id: number;
  name: string;
  active: boolean;
}

interface TicketCreateInfo {
  departments: Department[];
  services: Service[];
}

const ticketCreateInfo = ref<TicketCreateInfo | null>(null);
const loading = ref(false);

const fetchTicketCreateInfo = async () => {
  try {
    const response = await Tickets.getTicketCreateInfo();
    if (response.success) {
      ticketCreateInfo.value = {
        departments: response.departments,
        services: response.services
      };
    } else {
      console.error('Failed to fetch ticket create info:', response.error);
    }
  } catch (error) {
    console.error('Error fetching ticket create info:', error);
  }
};

onMounted(() => {
  fetchTicketCreateInfo();
});

const ticket = ref({
  service: '',
  department: '',
  priority: 'medium',
  subject: '',
  message: ''
});

const priorities = [
  { value: 'low', label: 'Low' },
  { value: 'medium', label: 'Medium' },
  { value: 'high', label: 'High' },
  { value: 'urgent', label: 'Urgent' }
];

const submitTicket = async () => {
  loading.value = true;
  try {
    // Handle ticket submission
    console.log('Submitting ticket:', ticket.value);

    const response = await Tickets.createTicket(Number(ticket.value.department), ticket.value.subject, ticket.value.message, ticket.value.priority, Number(ticket.value.service));

    if (!response.success) {
      const error_code = response.error_code as keyof typeof errorMessages;

      const errorMessages = {

      };

      if (errorMessages[error_code]) {
        playError();
        Swal.fire({
          icon: 'error',
          title: "Error while creating ticket!",
          text: errorMessages[error_code],
          footer: "Kinda funny since you tried to create a ticket",
          showConfirmButton: true,
        });
        loading.value = false;
        throw new Error('Login failed');
      } else {
        playError();
        Swal.fire({
          icon: 'error',
          title: "Error while creating ticket!",
          text: "We never expected this to happen",
          footer: "Kinda funny since you tried to create a ticket",
          showConfirmButton: true,
        });
        loading.value = false;
        throw new Error('Ticket creation failed');
      }
    } else {
      playSuccess();
      Swal.fire({
        icon: 'success',
        title: "Ticket submitted successfully!",
        text: "Your ticket has been submitted successfully",
        footer: "You will be redirected to the ticket page",
        showConfirmButton: true,
      });
      loading.value = false;
      setTimeout(() => {
        router.push('/ticket');
      }, 1500);
      console.log('Ticket submitted successfully:', response.ticket);
    }

    await new Promise(resolve => setTimeout(resolve, 2000));
  } catch (error) {
    console.error('Error submitting ticket:', error);
  } finally {
    loading.value = false;
  }
};
</script>

<template>
  <LayoutDashboard>
    <div class="space-y-6">
      <div class="flex justify-between items-center">
        <h1 class="text-2xl font-semibold text-gray-100">Support Tickets</h1>
        <router-link to="/ticket">
          <button
            class="bg-gray-600 hover:bg-gray-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200">
            Back
          </button>
        </router-link>
      </div>
      <CardComponent cardTitle="Create a Ticket" cardDescription="Submit a new ticket to our support team.">
        <form @submit.prevent="submitTicket" class="space-y-6">
          <!-- Service Selection -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Related Service
            </label>
            <SelectInput v-model="ticket.service" :options="ticketCreateInfo?.services.map(service => ({
              value: service.id.toString(),
              label: service.name
            })) || []" />
          </div>

          <!-- Department -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Department
            </label>
            <SelectInput v-model="ticket.department" :options="ticketCreateInfo?.departments
              .filter(dept => dept.enabled === 'true')
              .map(dept => ({
                value: dept.id.toString(),
                label: `${dept.name} (${dept.time_open} - ${dept.time_close}) - ${dept.description}`
              })) || []" />
          </div>

          <!-- Priority -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Priority
            </label>
            <SelectInput v-model="ticket.priority" :options="priorities" />
          </div>

          <!-- Subject -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Subject
            </label>
            <TextInput v-model="ticket.subject" type="text" required placeholder="Enter ticket subject" />
          </div>

          <!-- Message -->
          <div>
            <label class="block text-sm font-medium text-gray-300 mb-2">
              Message
            </label>
            <TextArea v-model="ticket.message" rows="6" required placeholder="Describe your issue..." />
          </div>

          <!-- Submit Button -->
          <div class="flex justify-end">
            <button type="submit" :disabled="loading"
              class="bg-purple-600 hover:bg-purple-700 text-white px-6 py-2.5 rounded-lg font-medium transition-colors duration-200">
              <span v-if="loading">Submitting...</span>
              <span v-else>Submit Ticket</span>
            </button>
          </div>
        </form>
      </CardComponent>
    </div>
  </LayoutDashboard>
</template>
