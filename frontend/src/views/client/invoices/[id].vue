<script setup lang="ts">
import { ref, onMounted, watch } from 'vue';
import { useRoute, useRouter } from 'vue-router';
import { format } from 'date-fns';
import LayoutDashboard from '@/components/client/LayoutDashboard.vue';
import { AlertCircle, ArrowLeft, Download, CreditCard } from 'lucide-vue-next';
import { MythicalDOM } from '@/mythicalclient/MythicalDOM';
import { useSettingsStore } from '@/stores/settings';
const Settings = useSettingsStore();
import jsPDF from 'jspdf';
import html2canvas from 'html2canvas';
import Session from '@/mythicalclient/Session';
import OrderInvoices from '@/mythicalclient/Services/OrderInvoices';
import Swal from 'sweetalert2';
import { useI18n } from 'vue-i18n';

interface Category {
    id: number;
    name: string;
    uri: string;
    headline: string;
    tagline: string;
}

interface Service {
    id: number;
    category: Category;
    name: string;
    tagline: string;
    price: number;
    setup_fee: number;
}

interface Order {
    id: number;
    user: string;
    service: Service;
    provider: number;
    status: string;
    date: string;
}

interface Invoice {
    id: number;
    user: string;
    order: Order;
    status: string;
    paid_at: string | null;
    due_date: string | null;
    updated_at: string;
    created_at: string;
    payment_gateway: string;
}

const route = useRoute();
const router = useRouter();

const invoice = ref<Invoice | null>(null);
const companyInfo = ref({
    name: '',
    address: '',
    city: '',
    state: '',
    zip: '',
    country: '',
    vat: '',
});
const loading = ref(true);
const errorMessage = ref<string | null>(null);

const getStatusClasses = (status: string): string => {
    const classes: Record<'pending' | 'paid' | 'cancelled' | 'refunded', string> = {
        pending: 'bg-orange-500/20 text-orange-400 border-orange-500',
        paid: 'bg-green-500/20 text-green-400 border-green-500',
        cancelled: 'bg-red-500/20 text-red-400 border-red-500',
        refunded: 'bg-blue-500/20 text-blue-400 border-blue-500',
    };
    return (
        classes[status.toLowerCase() as 'pending' | 'paid' | 'cancelled' | 'refunded'] ||
        'bg-gray-500/20 text-gray-400 border-gray-500'
    );
};

const formatDate = (date: string | null) => {
    if (!date) return '-';
    return format(new Date(date), 'PPP');
};

const fetchData = async () => {
    try {
        // Fetch invoice data
        const response = await fetch(`/api/user/invoice/${route.params.id}`);
        const data = await response.json();

        if (data.success && data.invoice) {
            invoice.value = data.invoice;
            MythicalDOM.setPageTitle(`Invoice #${data.invoice.id}`);
        } else {
            throw new Error(data.error || 'Failed to fetch invoice');
        }

        // Fetch company information
        companyInfo.value = {
            name: await Settings.getSetting('company_name'),
            address: await Settings.getSetting('company_address'),
            city: await Settings.getSetting('company_city'),
            state: await Settings.getSetting('company_state'),
            zip: await Settings.getSetting('company_zip'),
            country: await Settings.getSetting('company_country'),
            vat: await Settings.getSetting('company_vat'),
        };
    } catch (err) {
        errorMessage.value = err instanceof Error ? err.message : 'Failed to load invoice';
    } finally {
        loading.value = false;
    }
};

onMounted(fetchData);

const props = defineProps<{
    error: string | null;
    field?: string;
}>();

const { t } = useI18n();

watch(
    () => props.error,
    (newError: string | null) => {
        if (newError) {
            showError(newError);
        }
    },
);

const showError = (errorCode: string) => {
    const errorMessage = getErrorMessage(errorCode);

    Swal.fire({
        title: t('components.services.order.errors.title'),
        text: errorMessage,
        icon: 'error',
        confirmButtonText: 'OK',
        customClass: {
            confirmButton: 'btn btn-primary',
            popup: 'rounded-lg shadow-lg',
        },
    });
};

const getErrorMessage = (errorCode: string): string => {
    switch (errorCode) {
        case 'ERR_INVALID_JSON':
            return t('components.services.order.errors.invalid_json');
        case 'ERR_MISSING_REQUIRED_FIELD':
            return t('components.services.order.errors.missing_field', { field: props.field });
        case 'ERR_SERVICE_NOT_FOUND':
            return t('components.services.order.errors.service_not_found');
        case 'ERR_CATEGORY_NOT_FOUND':
            return t('components.services.order.errors.category_not_found');
        case 'ERR_PROVIDER_NOT_FOUND':
            return t('components.services.order.errors.provider_not_found');
        case 'ERR_INVOICE_CREATION_FAILED':
            return t('components.services.order.errors.invoice_failed');
        case 'ERR_INTERNAL_SERVER_ERROR':
            return t('components.services.order.errors.order_failed');
        case 'ERR_INSUFFICIENT_CREDITS':
            return t('components.services.order.errors.insufficient_credits');
        default:
            return t('components.services.order.errors.generic');
    }
};

const handlePayment = async () => {
    // Implement payment logic
    const response = await OrderInvoices.payUserInvoice(route.params.id as string);
    if (response.success) {
        router.push('/invoices');
    } else {
        showError(response.error);
    }
};

const downloadInvoice = async () => {
    if (!invoice.value) return;

    const element = document.getElementById('invoice-content');
    if (!element) return;

    try {
        // Create canvas from the element
        const canvas = await html2canvas(element, {
            scale: 2,
            useCORS: true,
            logging: false,
            backgroundColor: '#ffffff',
        });

        // Calculate dimensions to fit A4
        const imgWidth = 210; // A4 width in mm
        const imgHeight = (canvas.height * imgWidth) / canvas.width;

        // Create PDF
        const pdf = new jsPDF('p', 'mm', 'a4');
        pdf.addImage(canvas.toDataURL('image/jpeg', 1.0), 'JPEG', 0, 0, imgWidth, imgHeight);

        // Download PDF
        pdf.save(`invoice-${invoice.value.id}.pdf`);
    } catch (err) {
        console.error('Failed to generate PDF:', err);
    }
};
</script>

<template>
    <LayoutDashboard>
        <div class="space-y-6 p-6">
            <!-- Header Actions -->
            <div class="flex items-center justify-between">
                <div class="flex items-center gap-4">
                    <button @click="router.back()" class="p-2 rounded-lg hover:bg-gray-700 transition-colors">
                        <ArrowLeft class="w-5 h-5 text-gray-400" />
                    </button>
                    <h1 class="text-3xl font-bold text-gray-100">Invoice #{{ route.params.id }}</h1>
                </div>
                <div class="flex gap-3">
                    <button
                        @click="downloadInvoice"
                        class="flex items-center gap-2 px-4 py-2 bg-gray-700 hover:bg-gray-600 text-white rounded-lg transition-colors"
                    >
                        <Download class="w-4 h-4" />
                        Download PDF
                    </button>
                    <button
                        v-if="invoice?.status === 'pending'"
                        @click="handlePayment"
                        class="flex items-center gap-2 px-4 py-2 bg-purple-600 hover:bg-purple-500 text-white rounded-lg transition-colors"
                    >
                        <CreditCard class="w-4 h-4" />
                        Pay Now
                    </button>
                </div>
            </div>

            <Transition name="fade" mode="out-in">
                <div v-if="loading" class="space-y-4" key="loading">
                    <div class="bg-gray-800 rounded-lg p-6 animate-pulse space-y-4">
                        <div class="h-8 bg-gray-700 rounded-sm w-1/4"></div>
                        <div class="h-4 bg-gray-700 rounded-sm w-1/2"></div>
                        <div class="h-4 bg-gray-700 rounded-sm w-1/3"></div>
                    </div>
                </div>

                <div
                    v-else-if="error"
                    class="bg-red-900 border border-red-700 text-red-100 px-4 py-3 rounded-lg flex items-center space-x-2"
                    role="alert"
                    key="error"
                >
                    <AlertCircle class="w-5 h-5 shrink-0" />
                    <p>{{ error }}</p>
                </div>

                <div v-else-if="invoice" id="invoice-content" class="bg-gray-800 rounded-lg p-8 max-w-5xl mx-auto">
                    <!-- Invoice Header -->
                    <div class="grid grid-cols-2 gap-8 border-b border-gray-700 pb-8">
                        <!-- Company Info -->
                        <div>
                            <h2 class="text-2xl font-bold text-white mb-4">{{ companyInfo.name }}</h2>
                            <div class="text-sm text-gray-400 space-y-1">
                                <p>{{ companyInfo.address }}</p>
                                <p>{{ companyInfo.city }}, {{ companyInfo.state }} {{ companyInfo.zip }}</p>
                                <p>{{ companyInfo.country }}</p>
                                <p v-if="companyInfo.vat">VAT: {{ companyInfo.vat }}</p>
                            </div>
                        </div>

                        <!-- Client Info -->
                        <div class="text-right">
                            <h3 class="text-lg font-semibold text-white mb-4">Bill To:</h3>
                            <div class="text-sm text-gray-400 space-y-1">
                                <p>
                                    {{
                                        Session.getInfo('company_name') ||
                                        `${Session.getInfo('first_name')} ${Session.getInfo('last_name')}`
                                    }}
                                </p>
                                <p v-if="Session.getInfo('vat_number')">VAT: {{ Session.getInfo('vat_number') }}</p>
                                <p>{{ Session.getInfo('address1') }}</p>
                                <p v-if="Session.getInfo('address2') != 'N/A'">{{ Session.getInfo('address2') }}</p>
                                <p>
                                    {{ Session.getInfo('city') }}, {{ Session.getInfo('state') }}
                                    {{ Session.getInfo('postcode') }}
                                </p>
                                <p>{{ Session.getInfo('country') }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Invoice Summary -->
                    <div class="grid grid-cols-2 gap-8 py-8 border-b border-gray-700">
                        <div class="space-y-4">
                            <div>
                                <span class="text-sm text-gray-400">Invoice Number:</span>
                                <p class="text-white font-medium">#{{ invoice.id }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-400">Invoice Date:</span>
                                <p class="text-white">{{ formatDate(invoice.created_at) }}</p>
                            </div>
                            <div>
                                <span class="text-sm text-gray-400">Due Date:</span>
                                <p class="text-white">{{ formatDate(invoice.due_date) }}</p>
                            </div>
                        </div>
                        <div class="text-right">
                            <span
                                :class="[
                                    'px-3 py-1 rounded-full text-sm font-medium inline-block',
                                    getStatusClasses(invoice.status),
                                ]"
                            >
                                {{ invoice.status.toUpperCase() }}
                            </span>
                        </div>
                    </div>

                    <!-- Service Details -->
                    <div class="py-8 border-b border-gray-700">
                        <table class="w-full">
                            <thead>
                                <tr class="text-left text-sm text-gray-400">
                                    <th class="pb-4">Description</th>
                                    <th class="pb-4 text-right">Amount</th>
                                </tr>
                            </thead>
                            <tbody class="text-white">
                                <tr>
                                    <td class="py-2">
                                        <p class="font-medium">{{ invoice.order.service.name }}</p>
                                        <p class="text-sm text-gray-400">{{ invoice.order.service.tagline }}</p>
                                        <p class="text-sm text-gray-400">
                                            Category: {{ invoice.order.service.category.name }}
                                        </p>
                                    </td>
                                    <td class="py-2 text-right">${{ invoice.order.service.price.toFixed(2) }}</td>
                                </tr>
                                <tr v-if="invoice.order.service.setup_fee > 0">
                                    <td class="py-2">Setup Fee</td>
                                    <td class="py-2 text-right">${{ invoice.order.service.setup_fee.toFixed(2) }}</td>
                                </tr>
                            </tbody>
                            <tfoot>
                                <tr class="text-white font-semibold">
                                    <td class="pt-4">Total</td>
                                    <td class="pt-4 text-right">
                                        ${{
                                            (invoice.order.service.price + invoice.order.service.setup_fee).toFixed(2)
                                        }}
                                    </td>
                                </tr>
                            </tfoot>
                        </table>
                    </div>
                </div>
            </Transition>
        </div>
    </LayoutDashboard>
</template>

<style scoped>
.fade-enter-active,
.fade-leave-active {
    transition: opacity 0.5s ease;
}

.fade-enter-from,
.fade-leave-to {
    opacity: 0;
}

@media print {
    /* Reset background colors for PDF */
    .bg-gray-800 {
        background-color: white !important;
    }

    /* Text colors */
    .text-white,
    .text-gray-100,
    h1,
    h2,
    h3,
    h4,
    h5,
    h6 {
        color: #000 !important;
    }

    .text-gray-400 {
        color: #666 !important;
    }

    /* Border colors */
    .border-gray-700 {
        border-color: #ddd !important;
    }

    /* Status badges */
    [class*='bg-'][class*='/20'] {
        background-color: transparent !important;
        border: 1px solid currentColor !important;
    }

    /* Hide non-printable elements */
    button,
    .no-print {
        display: none !important;
    }

    /* Ensure full width */
    #invoice-content {
        width: 100% !important;
        max-width: none !important;
        margin: 0 !important;
        padding: 2cm !important;
    }

    /* Table styles */
    table {
        border-collapse: collapse !important;
    }

    th,
    td {
        border-bottom: 1px solid #ddd !important;
    }
}
</style>
