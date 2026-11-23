<script setup>
import { ref } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  Users,
  FileText,
  CheckCircle2,
  Clock,
  TrendingUp,
  TrendingDown,
  Plus,
  Download,
  Activity,
  ArrowUpRight,
  Filter,
  Search,
  Eye,
  ShieldAlert,
  Calendar,
  Building2,
  X
} from 'lucide-vue-next';

const props = defineProps({
  widgets: Object,
  witelInfo: Object
});

const selectedInvoice = ref(null);
const isInvoiceModalOpen = ref(false);

const viewInvoice = (inv) => {
  selectedInvoice.value = inv;
  isInvoiceModalOpen.value = true;
};

const getStatusBadgeClass = (status) => {
  switch (status) {
    case 'Paid':
      return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30';
    case 'Pending':
    case 'Sent':
      return 'bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border-amber-200 dark:border-amber-500/30';
    case 'Overdue':
      return 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/30';
    default:
      return 'bg-slate-100 dark:bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-500/30';
  }
};

const getActivityBadgeClass = (action) => {
  if (action.includes('PAYMENT')) return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30';
  if (action.includes('TAX')) return 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-500/30';
  if (action.includes('INVOICE')) return 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30';
  return 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-500/30';
};
</script>

<template>
  <Head title="Enterprise Dashboard - Telkom Witel Lampung" />

  <DashboardLayout>
    <div class="space-y-8 font-sans">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Enterprise Operations Dashboard</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Real-time revenue monitoring & billing automation for {{ witelInfo.name }}</p>
        </div>
        <div class="flex items-center gap-3">
          <Link
            :href="route('invoices.create')"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white shadow-lg shadow-indigo-600/20 transition-all"
          >
            <Plus class="w-4 h-4" />
            <span>Create Invoice</span>
          </Link>
        </div>
      </div>

      <!-- WIDGETS 1-4: Key Financial Metrics Grid -->
      <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-5">
        <!-- Widget 1: Total Customer -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-all shadow-sm dark:shadow-lg">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ widgets.total_customer.title }}</span>
            <div class="p-2.5 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 border border-indigo-100 dark:border-indigo-500/20">
              <Users class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
            </div>
          </div>
          <div class="mt-4">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ widgets.total_customer.value }}</h2>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ widgets.total_customer.subvalue }}</span>
              <span class="flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                <TrendingUp class="w-3 h-3 mr-1" />
                {{ widgets.total_customer.change }}
              </span>
            </div>
          </div>
        </div>

        <!-- Widget 2: Total Invoice -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-all shadow-sm dark:shadow-lg">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ widgets.total_invoice.title }}</span>
            <div class="p-2.5 rounded-xl bg-blue-50 dark:bg-blue-500/10 border border-blue-100 dark:border-blue-500/20">
              <FileText class="w-5 h-5 text-blue-600 dark:text-blue-400" />
            </div>
          </div>
          <div class="mt-4">
            <h2 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">{{ widgets.total_invoice.value }}</h2>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ widgets.total_invoice.subvalue }}</span>
              <span class="flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                <TrendingUp class="w-3 h-3 mr-1" />
                {{ widgets.total_invoice.change }}
              </span>
            </div>
          </div>
        </div>

        <!-- Widget 3: Paid Invoice -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-all shadow-sm dark:shadow-lg">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ widgets.paid_invoice.title }}</span>
            <div class="p-2.5 rounded-xl bg-emerald-50 dark:bg-emerald-500/10 border border-emerald-100 dark:border-emerald-500/20">
              <CheckCircle2 class="w-5 h-5 text-emerald-600 dark:text-emerald-400" />
            </div>
          </div>
          <div class="mt-4">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight truncate">{{ widgets.paid_invoice.value }}</h2>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ widgets.paid_invoice.subvalue }}</span>
              <span class="flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                <TrendingUp class="w-3 h-3 mr-1" />
                {{ widgets.paid_invoice.change }}
              </span>
            </div>
          </div>
        </div>

        <!-- Widget 4: Pending Invoice -->
        <div class="p-5 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 transition-all shadow-sm dark:shadow-lg">
          <div class="flex items-center justify-between">
            <span class="text-xs font-medium text-slate-500 dark:text-slate-400">{{ widgets.pending_invoice.title }}</span>
            <div class="p-2.5 rounded-xl bg-amber-50 dark:bg-amber-500/10 border border-amber-100 dark:border-amber-500/20">
              <Clock class="w-5 h-5 text-amber-600 dark:text-amber-400" />
            </div>
          </div>
          <div class="mt-4">
            <h2 class="text-xl font-extrabold text-slate-900 dark:text-white tracking-tight truncate">{{ widgets.pending_invoice.value }}</h2>
            <div class="flex items-center justify-between mt-2">
              <span class="text-xs font-semibold text-slate-600 dark:text-slate-300">{{ widgets.pending_invoice.subvalue }}</span>
              <span class="flex items-center text-xs font-semibold px-2 py-0.5 rounded-full bg-amber-50 dark:bg-amber-500/10 text-amber-700 dark:text-amber-400 border border-amber-200 dark:border-amber-500/20">
                <TrendingDown class="w-3 h-3 mr-1" />
                {{ widgets.pending_invoice.change }}
              </span>
            </div>
          </div>
        </div>
      </div>

      <!-- WIDGET 5 & WIDGET 7: Monthly Revenue & Activity Timeline -->
      <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- WIDGET 5: Monthly Revenue Chart -->
        <div class="lg:col-span-2 p-6 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg flex flex-col justify-between">
          <div>
            <div class="flex items-center justify-between mb-6">
              <div>
                <h3 class="text-base font-bold text-slate-900 dark:text-white">Monthly Revenue Performance</h3>
                <p class="text-xs text-slate-500 dark:text-slate-400">Financial growth across B2B enterprise segments in {{ widgets.monthly_revenue.year }}</p>
              </div>
              <div class="flex items-center gap-2">
                <span class="text-xs font-semibold px-2.5 py-1 rounded-lg bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border border-emerald-200 dark:border-emerald-500/20">
                  Target: {{ widgets.monthly_revenue.target_completion }}
                </span>
              </div>
            </div>

            <!-- Bar Visualizer -->
            <div class="h-60 flex items-end justify-between gap-4 pt-8 px-4 border-b border-slate-200 dark:border-slate-800 pb-4">
              <div
                v-for="bar in widgets.monthly_revenue.chart"
                :key="bar.month"
                class="flex-1 flex flex-col items-center gap-2 group cursor-pointer"
              >
                <div class="text-[10px] font-mono text-slate-500 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors opacity-0 group-hover:opacity-100">
                  {{ bar.formatted }}
                </div>
                <div class="w-full bg-slate-100 dark:bg-slate-900 rounded-t-xl overflow-hidden h-40 flex items-end">
                  <div
                    class="w-full bg-gradient-to-t from-indigo-600 to-sky-500 rounded-t-xl group-hover:brightness-110 transition-all duration-300"
                    :style="{ height: (bar.revenue / 5000 * 100) + '%' }"
                  ></div>
                </div>
                <span class="text-xs font-medium text-slate-600 dark:text-slate-400 group-hover:text-slate-900 dark:group-hover:text-white transition-colors">{{ bar.month }}</span>
              </div>
            </div>
          </div>

          <div class="flex items-center justify-between text-xs text-slate-500 dark:text-slate-400 mt-4">
            <span>Target Benchmark: {{ widgets.monthly_revenue.target_monthly }} / month</span>
            <span class="font-mono">Witel Lampung Billing Unit</span>
          </div>
        </div>

        <!-- WIDGET 7: Activity Timeline -->
        <div class="p-6 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg flex flex-col">
          <div class="flex items-center justify-between mb-4">
            <div class="flex items-center gap-2">
              <Activity class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <h3 class="text-base font-bold text-slate-900 dark:text-white">Activity Timeline</h3>
            </div>
            <span class="text-[11px] text-slate-500 dark:text-slate-400">Live Audit Log</span>
          </div>

          <div class="space-y-4 overflow-y-auto max-h-80 pr-1">
            <div
              v-for="log in widgets.activity_timeline"
              :key="log.id"
              class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900/70 border border-slate-200 dark:border-slate-800/80 hover:border-slate-300 dark:hover:border-slate-700 transition-all space-y-1.5"
            >
              <div class="flex items-center justify-between">
                <span :class="['px-2 py-0.5 rounded text-[10px] font-bold border', getActivityBadgeClass(log.action)]">
                  {{ log.action }}
                </span>
                <span class="text-[10px] text-slate-400 font-mono">{{ log.time_ago }}</span>
              </div>
              <p class="text-xs text-slate-800 dark:text-slate-200 leading-snug">{{ log.description }}</p>
              <div class="flex items-center justify-between text-[10px] text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200 dark:border-slate-800/50">
                <span>By {{ log.user }}</span>
                <span class="font-mono text-slate-400">{{ log.ip }}</span>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- WIDGET 6: Latest Invoices Table -->
      <div class="p-6 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg space-y-4">
        <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
          <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Latest Enterprise Invoices</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Recent billing ledger for PT. Telkom Lampung B2B customers</p>
          </div>
          <div>
            <Link
              :href="route('invoices.index')"
              class="text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:underline"
            >
              View All Invoices &rarr;
            </Link>
          </div>
        </div>

        <!-- Table -->
        <div class="overflow-x-auto rounded-xl border border-slate-200 dark:border-slate-800">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-3 px-4">Invoice Number</th>
                <th class="py-3 px-4">Enterprise Client</th>
                <th class="py-3 px-4">Service Package</th>
                <th class="py-3 px-4">Total Billing (IDR)</th>
                <th class="py-3 px-4">Due Date</th>
                <th class="py-3 px-4">Status</th>
                <th class="py-3 px-4 text-right">Action</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
              <tr
                v-for="inv in widgets.latest_invoice"
                :key="inv.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors"
              >
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">{{ inv.id }}</td>
                <td class="py-3.5 px-4">
                  <p class="font-semibold text-slate-900 dark:text-slate-100">{{ inv.client_name }}</p>
                  <p class="text-[10px] text-slate-500 font-mono">{{ inv.client_code }}</p>
                </td>
                <td class="py-3.5 px-4 text-slate-600 dark:text-slate-300">{{ inv.service }}</td>
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">{{ inv.amount }}</td>
                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400">{{ inv.due_date }}</td>
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-[11px] font-semibold border',
                      getStatusBadgeClass(inv.status)
                    ]"
                  >
                    {{ inv.status }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <button
                    @click="viewInvoice(inv)"
                    class="inline-flex items-center gap-1 text-xs text-indigo-600 dark:text-indigo-400 hover:underline font-semibold"
                  >
                    <Eye class="w-3.5 h-3.5" />
                    <span>View</span>
                  </button>
                </td>
              </tr>
            </tbody>
          </table>
        </div>
      </div>
    </div>

    <!-- Quick View Invoice Modal -->
    <div
      v-if="isInvoiceModalOpen && selectedInvoice"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50"
    >
      <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-2xl p-6 space-y-5 relative shadow-2xl">
        <button
          @click="isInvoiceModalOpen = false"
          class="absolute top-4 right-4 p-1.5 rounded-lg text-slate-400 hover:text-slate-600 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800"
        >
          <X class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3">
          <div class="p-3 rounded-xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
            <FileText class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">{{ selectedInvoice.id }}</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Enterprise Invoice Details</p>
          </div>
        </div>

        <div class="space-y-3 text-xs divide-y divide-slate-200 dark:divide-slate-800">
          <div class="pt-2 flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">Client Name:</span>
            <span class="font-semibold text-slate-900 dark:text-white">{{ selectedInvoice.client_name }}</span>
          </div>
          <div class="pt-2 flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">Service:</span>
            <span class="font-semibold text-slate-700 dark:text-slate-200">{{ selectedInvoice.service }}</span>
          </div>
          <div class="pt-2 flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">Issue Date:</span>
            <span class="font-mono text-slate-600 dark:text-slate-300">{{ selectedInvoice.date }}</span>
          </div>
          <div class="pt-2 flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">Due Date:</span>
            <span class="font-mono text-slate-600 dark:text-slate-300">{{ selectedInvoice.due_date }}</span>
          </div>
          <div class="pt-2 flex justify-between">
            <span class="text-slate-500 dark:text-slate-400">Total Amount:</span>
            <span class="font-mono font-bold text-lg text-slate-900 dark:text-white">{{ selectedInvoice.amount }}</span>
          </div>
          <div class="pt-2 flex justify-between items-center">
            <span class="text-slate-500 dark:text-slate-400">Current Status:</span>
            <span :class="['px-2.5 py-0.5 rounded-full text-[11px] font-semibold border', getStatusBadgeClass(selectedInvoice.status)]">
              {{ selectedInvoice.status }}
            </span>
          </div>
        </div>

        <div class="pt-2 flex justify-end gap-3">
          <button
            @click="isInvoiceModalOpen = false"
            class="px-4 py-2 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
