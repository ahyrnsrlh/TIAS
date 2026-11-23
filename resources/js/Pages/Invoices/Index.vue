<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  FileText,
  Plus,
  Search,
  Filter,
  Download,
  Upload,
  Mail,
  Eye,
  Edit2,
  Trash2,
  CheckCircle2,
  FileSpreadsheet,
  X,
  Building2,
  Calendar,
  AlertCircle
} from 'lucide-vue-next';

const props = defineProps({
  invoices: Object,
  filters: Object,
  flash_stats: Object,
});

const search = ref(props.filters.search || '');
const currentStatus = ref(props.filters.status || '');
const selectedInvoiceIds = ref([]);

const isPreviewModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isImportModalOpen = ref(false);
const selectedInvoice = ref(null);

const importForm = useForm({
  file: null,
});

watch([search, currentStatus], () => {
  router.get(
    route('invoices.index'),
    { search: search.value, status: currentStatus.value },
    { preserveState: true, replace: true }
  );
});

const toggleSelectAll = (e) => {
  if (e.target.checked) {
    selectedInvoiceIds.value = props.invoices.data.map((inv) => inv.id);
  } else {
    selectedInvoiceIds.value = [];
  }
};

const openPreviewModal = (inv) => {
  selectedInvoice.value = inv;
  isPreviewModalOpen.value = true;
};

const openDeleteModal = (inv) => {
  selectedInvoice.value = inv;
  isDeleteModalOpen.value = true;
};

const confirmDelete = () => {
  if (!selectedInvoice.value) return;
  router.delete(route('invoices.destroy', selectedInvoice.value.id), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      selectedInvoice.value = null;
    },
  });
};

const sendBulkEmails = () => {
  if (selectedInvoiceIds.value.length === 0) return;
  router.post(
    route('invoices.send-bulk'),
    { invoice_ids: selectedInvoiceIds.value },
    {
      onSuccess: () => {
        selectedInvoiceIds.value = [];
      },
    }
  );
};

const downloadTemplate = () => {
  window.location.href = route('invoices.export-template');
};

const submitImport = () => {
  importForm.post(route('invoices.import'), {
    onSuccess: () => {
      isImportModalOpen.value = false;
      importForm.reset();
    },
  });
};

const handleFileSelect = (e) => {
  importForm.file = e.target.files[0];
};

const getStatusBadgeClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'paid':
      return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30';
    case 'pending':
    case 'sent':
      return 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/30';
    case 'overdue':
      return 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/30';
    default:
      return 'bg-slate-100 dark:bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-500/30';
  }
};

const formatDate = (dateStr) => {
  if (!dateStr) return '—';
  if (typeof dateStr === 'string' && dateStr.length >= 10) {
    return dateStr.substring(0, 10);
  }
  return dateStr;
};

function formatCurrency(val) {
  let number = parseFloat(val) || 0;
  return 'Rp ' + number_format(number, 0, ',', '.');
}

function number_format(number, decimals, dec_point, thousands_sep) {
  number = (number + '').replace(/[^0-9+\-Ee.]/g, '');
  var n = !isFinite(+number) ? 0 : +number,
    prec = !isFinite(+decimals) ? 0 : Math.abs(decimals),
    sep = typeof thousands_sep === 'undefined' ? '.' : thousands_sep,
    dec = typeof dec_point === 'undefined' ? ',' : dec_point,
    s = '',
    toFixedFix = function (n, prec) {
      var k = Math.pow(10, prec);
      return '' + Math.round(n * k) / k;
    };
  s = (prec ? toFixedFix(n, prec) : '' + Math.round(n)).split('.');
  if (s[0].length > 3) {
    s[0] = s[0].replace(/\B(?=(?:\d{3})+(?!\d))/g, sep);
  }
  if ((s[1] || '').length < prec) {
    s[1] = s[1] || '';
    s[1] += new Array(prec - s[1].length + 1).join('0');
  }
  return s.join(dec);
}
</script>

<template>
  <Head title="Invoice Management - Telkom Witel Lampung" />

  <DashboardLayout>
    <div class="space-y-8 font-sans">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Invoice Management Ledger</h1>
          <p class="text-xs text-slate-600 dark:text-slate-400 mt-1">Generate, edit, track, and manage B2B billing invoices for PT. Telkom Witel Lampung</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <button
            v-if="selectedInvoiceIds.length > 0"
            @click="sendBulkEmails"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white shadow-lg shadow-sky-600/30 transition-all animate-pulse"
          >
            <Mail class="w-4 h-4" />
            <span>Send Selected Emails ({{ selectedInvoiceIds.length }})</span>
          </button>

          <button
            @click="downloadTemplate"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Download class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            <span>Excel Template</span>
          </button>

          <button
            @click="isImportModalOpen = true"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Upload class="w-4 h-4 text-sky-600 dark:text-sky-400" />
            <span>Import Billing Excel</span>
          </button>

          <Link
            :href="route('invoices.create')"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white shadow-lg shadow-indigo-600/20 transition-all"
          >
            <Plus class="w-4 h-4" />
            <span>Create New Invoice</span>
          </Link>
        </div>
      </div>

      <!-- Flash Stats Banner -->
      <div v-if="flash_stats" class="p-4 rounded-2xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-200 dark:border-emerald-500/30 text-emerald-700 dark:text-emerald-400 text-xs flex items-center justify-between">
        <div class="flex items-center gap-2">
          <CheckCircle2 class="w-5 h-5 text-emerald-600 dark:text-emerald-400 shrink-0" />
          <div>
            <p class="font-bold text-slate-900 dark:text-white">Bulk Billing Import Successful!</p>
            <p class="text-[11px] text-emerald-700 dark:text-emerald-300/80">
              Imported: <strong class="text-slate-900 dark:text-white">{{ flash_stats.imported }}</strong> invoices |
              Duplicates Skipped: <strong class="text-slate-900 dark:text-white">{{ flash_stats.duplicates_skipped }}</strong> |
              Volume Created: <strong class="text-slate-900 dark:text-white font-mono">{{ formatCurrency(flash_stats.total_volume) }}</strong>
            </p>
          </div>
        </div>
      </div>

      <!-- Status Filter Tabs & Search -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg space-y-4">
        <!-- Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 sm:pb-0 border-b border-slate-200 dark:border-slate-800/80">
          <button
            @click="currentStatus = ''"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              currentStatus === ''
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            All Invoices
          </button>
          <button
            @click="currentStatus = 'draft'"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              currentStatus === 'draft'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            Draft
          </button>
          <button
            @click="currentStatus = 'generated'"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              currentStatus === 'generated'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            Generated
          </button>
          <button
            @click="currentStatus = 'sent'"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              currentStatus === 'sent'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            Sent
          </button>
          <button
            @click="currentStatus = 'paid'"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              currentStatus === 'paid'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            Paid
          </button>
          <button
            @click="currentStatus = 'overdue'"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              currentStatus === 'overdue'
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            Overdue
          </button>
        </div>

        <!-- Search Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-1">
          <div class="relative w-full sm:w-96">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
            <input
              v-model="search"
              type="text"
              placeholder="Search by invoice number or client company..."
              class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all"
            />
          </div>
          <div class="text-xs text-slate-600 dark:text-slate-400 font-medium">
            Total Invoices: <span class="font-bold text-slate-900 dark:text-white">{{ invoices.total }}</span>
          </div>
        </div>
      </div>

      <!-- Invoices Data Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-4 w-10 text-center">
                  <input
                    type="checkbox"
                    @change="toggleSelectAll"
                    :checked="selectedInvoiceIds.length > 0 && selectedInvoiceIds.length === invoices.data.length"
                    class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-600"
                  />
                </th>
                <th class="py-3.5 px-4">Invoice Number</th>
                <th class="py-3.5 px-4">Enterprise Client</th>
                <th class="py-3.5 px-4">Service Details</th>
                <th class="py-3.5 px-4">Issue & Due Date</th>
                <th class="py-3.5 px-4">Total Amount (IDR)</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
              <tr
                v-for="inv in invoices.data"
                :key="inv.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors"
              >
                <td class="py-3.5 px-4 text-center">
                  <input
                    type="checkbox"
                    :value="inv.id"
                    v-model="selectedInvoiceIds"
                    class="rounded border-slate-300 dark:border-slate-700 bg-white dark:bg-slate-900 text-indigo-600 focus:ring-indigo-600"
                  />
                </td>
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                  <Link :href="route('invoices.show', inv.id)" class="hover:text-indigo-600 dark:hover:text-indigo-400 transition-colors">
                    {{ inv.invoice_number }}
                  </Link>
                </td>
                <td class="py-3.5 px-4">
                  <p class="font-semibold text-slate-900 dark:text-slate-100">{{ inv.customer?.company_name || 'Client Corporate' }}</p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">{{ inv.customer?.customer_code || 'CUST-000' }}</p>
                </td>
                <td class="py-3.5 px-4 text-slate-700 dark:text-slate-300">
                  <span>{{ inv.items && inv.items.length ? inv.items[0].service_name : 'Telkom Service' }}</span>
                  <span v-if="inv.items && inv.items.length > 1" class="text-[10px] ml-1 px-1.5 py-0.5 rounded bg-slate-100 dark:bg-slate-900 text-slate-600 dark:text-slate-400 border border-slate-200 dark:border-slate-800">
                    +{{ inv.items.length - 1 }} more
                  </span>
                </td>
                <td class="py-3.5 px-4">
                  <p class="text-slate-800 dark:text-slate-300 font-mono">{{ formatDate(inv.issue_date) }}</p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 font-mono">Due: {{ formatDate(inv.due_date) }}</p>
                </td>
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">
                  {{ formatCurrency(inv.total_amount) }}
                </td>
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-[11px] font-semibold border capitalize',
                      getStatusBadgeClass(inv.status)
                    ]"
                  >
                    {{ inv.status }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="openPreviewModal(inv)"
                      class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                      title="Quick Preview"
                    >
                      <Eye class="w-3.5 h-3.5" />
                    </button>
                    <Link
                      :href="route('invoices.show', inv.id)"
                      class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-indigo-600 dark:hover:text-indigo-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                      title="View Detail Page"
                    >
                      <FileText class="w-3.5 h-3.5" />
                    </Link>
                    <Link
                      :href="route('invoices.edit', inv.id)"
                      class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                      title="Edit Invoice"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </Link>
                    <button
                      @click="openDeleteModal(inv)"
                      class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                      title="Delete Invoice"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="invoices.data.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-500 dark:text-slate-400">
                  <FileText class="w-8 h-8 mx-auto text-slate-400 mb-2" />
                  <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">No invoice records found</p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-500 mt-1">Try adjusting your status filter or search parameters</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
          <div>
            Showing <span class="font-semibold text-slate-900 dark:text-white">{{ invoices.from || 0 }}</span> to <span class="font-semibold text-slate-900 dark:text-white">{{ invoices.to || 0 }}</span> of <span class="font-semibold text-slate-900 dark:text-white">{{ invoices.total }}</span> invoices
          </div>
          <div class="flex items-center gap-1.5">
            <Link
              v-for="link in invoices.links"
              :key="link.label"
              :href="link.url || '#'"
              v-html="link.label"
              :class="[
                'px-3 py-1.5 rounded-lg text-xs font-semibold transition-colors',
                link.active
                  ? 'bg-indigo-600 text-white'
                  : link.url
                  ? 'bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800'
                  : 'text-slate-400 dark:text-slate-600 cursor-not-allowed'
              ]"
            />
          </div>
        </div>
      </div>
    </div>

    <!-- QUICK PREVIEW MODAL -->
    <div
      v-if="isPreviewModalOpen && selectedInvoice"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto"
    >
      <div class="w-full max-w-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 space-y-5 relative shadow-2xl my-8">
        <button
          @click="isPreviewModalOpen = false"
          class="absolute top-5 right-5 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800"
        >
          <X class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
          <img src="/logo-telkom.webp" alt="PT. Telkom Logo" class="h-9 w-auto object-contain" />
          <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ selectedInvoice.invoice_number }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">PT. Telkom Indonesia Witel Lampung Billing</p>
          </div>
        </div>

        <div class="space-y-3 text-xs">
          <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 space-y-1">
            <p class="font-bold text-slate-900 dark:text-white">{{ selectedInvoice.customer?.company_name }}</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">NPWP: {{ selectedInvoice.customer?.npwp || 'N/A' }}</p>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">VA: {{ selectedInvoice.customer?.virtual_account || 'N/A' }}</p>
          </div>

          <div class="divide-y divide-slate-200 dark:divide-slate-800/80 border-t border-b border-slate-200 dark:border-slate-800 py-2">
            <div
              v-for="item in selectedInvoice.items"
              :key="item.id"
              class="py-2 flex justify-between"
            >
              <div>
                <p class="font-semibold text-slate-800 dark:text-slate-200">{{ item.service_name }}</p>
                <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">{{ item.quantity }} x {{ formatCurrency(item.unit_price) }}</p>
              </div>
              <p class="font-mono font-bold text-slate-900 dark:text-white">{{ formatCurrency(item.amount) }}</p>
            </div>
          </div>

          <div class="space-y-1.5 pt-1 text-right">
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
              <span>Subtotal:</span>
              <span class="font-mono text-slate-900 dark:text-white">{{ formatCurrency(selectedInvoice.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
              <span>PPN ({{ selectedInvoice.tax_percentage }}%):</span>
              <span class="font-mono text-slate-900 dark:text-white">{{ formatCurrency(selectedInvoice.tax_amount) }}</span>
            </div>
            <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white pt-2 border-t border-slate-200 dark:border-slate-800">
              <span>Total Billing:</span>
              <span class="font-mono text-indigo-600 dark:text-indigo-400">{{ formatCurrency(selectedInvoice.total_amount) }}</span>
            </div>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
          <button
            @click="isPreviewModalOpen = false"
            class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
          >
            Close
          </button>
          <Link
            :href="route('invoices.show', selectedInvoice.id)"
            class="px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-lg shadow-indigo-600/20"
          >
            Open Full Invoice Page
          </Link>
        </div>
      </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div
      v-if="isDeleteModalOpen"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50"
    >
      <div class="w-full max-w-md bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 space-y-5 relative shadow-2xl text-center">
        <div class="w-12 h-12 rounded-2xl bg-rose-500/10 text-rose-500 flex items-center justify-center mx-auto">
          <Trash2 class="w-6 h-6" />
        </div>
        <div>
          <h3 class="text-base font-bold text-slate-900 dark:text-white">Delete Invoice Record?</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Are you sure you want to soft-delete <span class="font-bold text-slate-900 dark:text-white">{{ selectedInvoice?.invoice_number }}</span>? Audit trail logs will be recorded.
          </p>
        </div>
        <div class="flex justify-center gap-3 pt-2">
          <button
            @click="isDeleteModalOpen = false"
            class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
          >
            Cancel
          </button>
          <button
            @click="confirmDelete"
            class="px-5 py-2.5 rounded-xl bg-rose-600 hover:bg-rose-500 text-xs font-bold text-white shadow-lg shadow-rose-600/30"
          >
            Confirm Delete
          </button>
        </div>
      </div>
    </div>

    <!-- BULK BILLING IMPORT EXCEL MODAL -->
    <div
      v-if="isImportModalOpen"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50"
    >
      <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 relative shadow-2xl">
        <button
          @click="isImportModalOpen = false"
          class="absolute top-5 right-5 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800"
        >
          <X class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
          <div class="p-3 rounded-2xl bg-sky-500/10 text-sky-500">
            <FileSpreadsheet class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Bulk Billing Excel Import</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Upload `.xlsx` or `.csv` client billing spreadsheet</p>
          </div>
        </div>

        <form @submit.prevent="submitImport" class="space-y-5 text-xs">
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center hover:border-sky-500/50 transition-colors">
            <Upload class="w-8 h-8 text-sky-500 mx-auto mb-2" />
            <p class="text-xs font-semibold text-slate-900 dark:text-white mb-1">Select Excel File to Upload</p>
            <p class="text-[11px] text-slate-500 mb-3">Supported formats: .xlsx, .xls, .csv (Max 10MB)</p>
            <input
              type="file"
              accept=".xlsx,.xls,.csv"
              required
              @change="handleFileSelect"
              class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-600 dark:file:text-sky-400 hover:file:bg-sky-500/20"
            />
          </div>

          <div class="p-3.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800/80 space-y-2">
            <div class="flex items-center justify-between">
              <p class="font-bold text-slate-700 dark:text-slate-300 text-[11px]">Required Spreadsheet Columns:</p>
              <button
                type="button"
                @click="downloadTemplate"
                class="text-sky-600 dark:text-sky-400 hover:underline font-semibold flex items-center gap-1 text-[10px]"
              >
                <Download class="w-3 h-3" />
                <span>Get Sample Template</span>
              </button>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono leading-relaxed">
              customer_code | company_name | email | invoice_number | issue_date | due_date | service_name | quantity | unit_price
            </p>
          </div>

          <div class="pt-3 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
            <button
              type="button"
              @click="isImportModalOpen = false"
              class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="importForm.processing || !importForm.file"
              class="px-5 py-2.5 rounded-xl bg-sky-600 hover:bg-sky-500 text-xs font-bold text-white shadow-lg shadow-sky-600/30 disabled:opacity-50"
            >
              Start Bulk Import
            </button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>
