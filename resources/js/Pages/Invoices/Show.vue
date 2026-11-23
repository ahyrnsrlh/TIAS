<script setup>
import { Head, Link } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  ArrowLeft,
  Printer,
  Download,
  Mail,
  Edit2,
  CheckCircle2,
  Building2,
  CreditCard,
  Calendar,
  FileText
} from 'lucide-vue-next';

const props = defineProps({
  invoice: Object,
});

const printInvoice = () => {
  window.print();
};

const getStatusBadgeClass = (status) => {
  switch (status?.toLowerCase()) {
    case 'paid':
      return 'bg-emerald-500/10 text-emerald-600 dark:text-emerald-400 border-emerald-500/30';
    case 'pending':
    case 'sent':
      return 'bg-amber-500/10 text-amber-600 dark:text-amber-400 border-amber-500/30';
    case 'overdue':
      return 'bg-rose-500/10 text-rose-600 dark:text-rose-400 border-rose-500/30';
    default:
      return 'bg-slate-500/10 text-slate-600 dark:text-slate-400 border-slate-500/30';
  }
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
  <Head :title="`Invoice #${invoice.invoice_number} - Telkom Witel Lampung`" />

  <DashboardLayout>
    <div class="space-y-8 font-sans max-w-5xl mx-auto">
      <!-- Header Bar Controls -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4 print:hidden">
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
            <Link :href="route('invoices.index')" class="hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-1">
              <ArrowLeft class="w-3.5 h-3.5" />
              <span>Invoices Ledger</span>
            </Link>
            <span>/</span>
            <span class="text-slate-900 dark:text-white font-medium">{{ invoice.invoice_number }}</span>
          </div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Invoice Details</h1>
        </div>

        <div class="flex flex-wrap items-center gap-2.5">
          <!-- Download Buttons -->
          <a
            :href="route('invoices.pdf', invoice.id)"
            target="_blank"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-indigo-600 dark:text-indigo-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Download class="w-3.5 h-3.5" />
            <span>Invoice PDF</span>
          </a>
          <a
            :href="route('invoices.billing-detail.pdf', invoice.id)"
            target="_blank"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-sky-600 dark:text-sky-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Download class="w-3.5 h-3.5" />
            <span>Billing Detail</span>
          </a>
          <a
            :href="route('invoices.receipt.pdf', invoice.id)"
            target="_blank"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-emerald-600 dark:text-emerald-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Download class="w-3.5 h-3.5" />
            <span>Official Receipt</span>
          </a>
          <a
            :href="route('invoices.tax-invoice.pdf', invoice.id)"
            target="_blank"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-purple-600 dark:text-purple-400 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Download class="w-3.5 h-3.5" />
            <span>Tax Invoice</span>
          </a>
          <button
            @click="printInvoice"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Printer class="w-3.5 h-3.5 text-slate-400" />
            <span>Print</span>
          </button>
          <Link
            :href="route('invoices.edit', invoice.id)"
            class="flex items-center gap-1.5 px-3 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Edit2 class="w-3.5 h-3.5 text-slate-400" />
            <span>Edit</span>
          </Link>
        </div>
      </div>

      <!-- PRINTABLE INVOICE SHEET -->
      <div class="p-8 sm:p-12 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-2xl space-y-8 print:p-0 print:border-none print:shadow-none print:bg-white print:text-black">
        <!-- Header Letterhead -->
        <div class="flex flex-col sm:flex-row items-start justify-between gap-6 border-b border-slate-200 dark:border-slate-800 pb-8 print:border-gray-200">
          <div class="flex items-center gap-4">
            <img src="/logo-telkom.webp" alt="PT. Telkom Indonesia Logo" class="h-12 w-auto object-contain" />
            <div>
              <h2 class="text-lg font-extrabold text-slate-900 dark:text-white print:text-black tracking-tight">PT. TELKOM INDONESIA (PERSERO) Tbk</h2>
              <p class="text-xs font-semibold text-telkom-red">WITEL LAMPUNG</p>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 print:text-gray-600 mt-0.5">Jl. Majapahit No. 1, Enggal, Bandar Lampung, Lampung 35118</p>
              <p class="text-[10px] text-slate-400 dark:text-slate-500 print:text-gray-500 font-mono">NPWP: 01.000.013.1-093.000</p>
            </div>
          </div>

          <div class="text-left sm:text-right">
            <span :class="['px-3 py-1 rounded-full text-xs font-bold border capitalize inline-block mb-2 print:border-gray-300 print:text-black', getStatusBadgeClass(invoice.status)]">
              {{ invoice.status }}
            </span>
            <h1 class="text-xl font-extrabold text-slate-900 dark:text-white print:text-black font-mono tracking-tight">{{ invoice.invoice_number }}</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 print:text-gray-600">Enterprise B2B Billing Document</p>
          </div>
        </div>

        <!-- Meta Information Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 gap-8 text-xs">
          <!-- Billed To -->
          <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 print:border-gray-200 print:bg-gray-50 space-y-2">
            <span class="text-[10px] font-bold tracking-wider text-telkom-red uppercase">Billed To (Enterprise Client)</span>
            <h3 class="text-sm font-extrabold text-slate-900 dark:text-white print:text-black">{{ invoice.customer?.company_name }}</h3>
            <p class="text-slate-700 dark:text-slate-300 print:text-gray-700">Attn: {{ invoice.customer?.name }}</p>
            <p class="text-slate-500 dark:text-slate-400 print:text-gray-600">{{ invoice.customer?.address }}, {{ invoice.customer?.city }}</p>
            <div class="pt-2 border-t border-slate-200 dark:border-slate-800/60 print:border-gray-200 space-y-1 font-mono text-[11px]">
              <p class="text-slate-500 dark:text-slate-400 print:text-gray-600">NPWP: <span class="text-slate-900 dark:text-white print:text-black font-semibold">{{ invoice.customer?.npwp || 'N/A' }}</span></p>
              <p class="text-slate-500 dark:text-slate-400 print:text-gray-600">Virtual Account: <span class="text-sky-600 dark:text-sky-400 print:text-black font-bold">{{ invoice.customer?.virtual_account || 'N/A' }}</span></p>
            </div>
          </div>

          <!-- Transaction Summary -->
          <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 print:border-gray-200 print:bg-gray-50 space-y-2">
            <span class="text-[10px] font-bold tracking-wider text-telkom-red uppercase">Billing Metadata</span>
            <div class="space-y-1.5 pt-1">
              <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 print:text-gray-600">Issue Date:</span>
                <span class="font-mono font-semibold text-slate-900 dark:text-white print:text-black">{{ invoice.issue_date }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 print:text-gray-600">Payment Due Date:</span>
                <span class="font-mono font-bold text-telkom-red">{{ invoice.due_date }}</span>
              </div>
              <div class="flex justify-between" v-if="invoice.period_start">
                <span class="text-slate-500 dark:text-slate-400 print:text-gray-600">Service Period:</span>
                <span class="font-mono text-slate-700 dark:text-slate-300 print:text-gray-800">{{ invoice.period_start }} to {{ invoice.period_end }}</span>
              </div>
              <div class="flex justify-between">
                <span class="text-slate-500 dark:text-slate-400 print:text-gray-600">Payment Terms:</span>
                <span class="font-semibold text-slate-700 dark:text-slate-300 print:text-gray-800">{{ invoice.payment_terms || 'Net 14 Days' }}</span>
              </div>
            </div>
          </div>
        </div>

        <!-- Line Items Table -->
        <div class="overflow-x-auto rounded-2xl border border-slate-200 dark:border-slate-800 print:border-gray-200">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900 text-slate-600 dark:text-slate-400 print:bg-gray-100 print:text-black font-semibold border-b border-slate-200 dark:border-slate-800 print:border-gray-200">
              <tr>
                <th class="py-3 px-4 w-12">#</th>
                <th class="py-3 px-4">Service Package & Description</th>
                <th class="py-3 px-4 text-center w-20">Qty</th>
                <th class="py-3 px-4 text-right w-36">Unit Price</th>
                <th class="py-3 px-4 text-right w-40">Amount (IDR)</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 print:divide-gray-200 text-slate-700 dark:text-slate-300 print:text-black">
              <tr v-for="(item, idx) in invoice.items" :key="item.id">
                <td class="py-3.5 px-4 font-mono font-bold text-slate-400 dark:text-slate-500">{{ idx + 1 }}</td>
                <td class="py-3.5 px-4">
                  <p class="font-bold text-slate-900 dark:text-white print:text-black">{{ item.service_name }}</p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 print:text-gray-600 mt-0.5" v-if="item.description">{{ item.description }}</p>
                  <p class="text-[10px] text-slate-400 dark:text-slate-500 font-mono mt-0.5" v-if="item.service_code">Code: {{ item.service_code }}</p>
                </td>
                <td class="py-3.5 px-4 text-center font-mono font-semibold">{{ item.quantity }}</td>
                <td class="py-3.5 px-4 text-right font-mono">{{ formatCurrency(item.unit_price) }}</td>
                <td class="py-3.5 px-4 text-right font-mono font-bold text-slate-900 dark:text-white print:text-black">{{ formatCurrency(item.amount) }}</td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Totals & Payment Instructions Grid -->
        <div class="grid grid-cols-1 lg:grid-cols-2 gap-8 text-xs pt-4">
          <!-- Bank Transfer Instructions -->
          <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 print:border-gray-200 space-y-2">
            <span class="text-[10px] font-bold tracking-wider text-telkom-red uppercase">Payment Transfer Instructions</span>
            <p class="text-slate-700 dark:text-slate-300 print:text-gray-700">Please remit payment to PT. Telkom Indonesia Witel Lampung designated Bank Virtual Account:</p>
            <div class="p-3 rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 print:bg-white print:border-gray-300 space-y-1 font-mono text-[11px]">
              <p class="text-slate-500 dark:text-slate-400 print:text-gray-600">Bank Name: <span class="text-slate-900 dark:text-white print:text-black font-semibold">Bank Mandiri (Cabang Bandar Lampung)</span></p>
              <p class="text-slate-500 dark:text-slate-400 print:text-gray-600">Account Name: <span class="text-slate-900 dark:text-white print:text-black font-semibold">PT TELKOM INDONESIA WITEL LAMPUNG</span></p>
              <p class="text-slate-500 dark:text-slate-400 print:text-gray-600">Virtual Account: <span class="text-sky-600 dark:text-sky-400 print:text-black font-extrabold text-sm">{{ invoice.customer?.virtual_account || '9881140000000001' }}</span></p>
            </div>
            <p class="text-[11px] text-slate-500 dark:text-slate-400 print:text-gray-600 italic mt-2">{{ invoice.notes }}</p>
          </div>

          <!-- Financial Totals -->
          <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900/60 border border-slate-200 dark:border-slate-800/80 print:border-gray-200 space-y-3 font-mono">
            <div class="flex justify-between text-slate-500 dark:text-slate-400 print:text-gray-600">
              <span>Subtotal Amount:</span>
              <span class="font-bold text-slate-900 dark:text-white print:text-black">{{ formatCurrency(invoice.subtotal) }}</span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400 print:text-gray-600">
              <span>PPN Tax ({{ invoice.tax_percentage }}%):</span>
              <span class="font-bold text-slate-900 dark:text-white print:text-black">{{ formatCurrency(invoice.tax_amount) }}</span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400 print:text-gray-600" v-if="invoice.discount_amount > 0">
              <span>Discount Deduction:</span>
              <span class="font-bold text-rose-600 dark:text-rose-400">-{{ formatCurrency(invoice.discount_amount) }}</span>
            </div>
            <div class="flex justify-between text-base font-extrabold text-slate-900 dark:text-white print:text-black pt-3 border-t border-slate-200 dark:border-slate-800 print:border-gray-300">
              <span>Total Billing Amount:</span>
              <span class="text-telkom-red print:text-black text-lg">{{ formatCurrency(invoice.total_amount) }}</span>
            </div>
          </div>
        </div>

        <!-- Letterhead Signature Footer -->
        <div class="pt-12 border-t border-slate-200 dark:border-slate-800 print:border-gray-300 flex justify-between items-end text-xs text-slate-500 dark:text-slate-400 print:text-gray-600">
          <div>
            <p class="font-semibold text-slate-700 dark:text-slate-300 print:text-black">PT. Telkom Indonesia Witel Lampung</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500">Finance & Billing Unit</p>
          </div>
          <div class="text-right">
            <p class="mb-12">Authorized Signature</p>
            <p class="font-bold text-slate-900 dark:text-white print:text-black underline">Manager Finance & Treasury</p>
            <p class="text-[10px] text-slate-400 dark:text-slate-500">PT. Telkom Indonesia (Persero) Tbk</p>
          </div>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
