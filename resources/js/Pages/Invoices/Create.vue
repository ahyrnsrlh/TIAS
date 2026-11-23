<script setup>
import { ref, computed } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  ArrowLeft,
  Plus,
  Trash2,
  Building2,
  FileText,
  Calendar,
  CreditCard,
  Save,
  Calculator
} from 'lucide-vue-next';

const props = defineProps({
  customers: Array,
});

const presetServices = [
  { name: 'Astinet Enterprise 1 Gbps', code: 'AST-1G', unit_price: 25000000, description: 'High speed dedicated internet connection' },
  { name: 'IndiBiz Pro 500 Mbps', code: 'IBIZ-500', unit_price: 8500000, description: 'Business broadband package with static IP' },
  { name: 'Metro Ethernet 100 Mbps', code: 'METRO-100', unit_price: 15000000, description: 'Point to point private data circuit' },
  { name: 'SIP Trunking 30 Channels', code: 'SIP-30', unit_price: 6000000, description: 'Digital voice telephony trunking' },
];

const form = useForm({
  customer_id: props.customers[0]?.id || '',
  invoice_number: '',
  issue_date: new Date().toISOString().substring(0, 10),
  due_date: new Date(Date.now() + 30 * 86400000).toISOString().substring(0, 10),
  period_start: new Date().toISOString().substring(0, 7) + '-01',
  period_end: new Date().toISOString().substring(0, 10),
  status: 'draft',
  notes: 'Pembayaran dapat dilakukan melalui Virtual Account Bank Mandiri yang tertera.',
  tax_percentage: 11.0,
  discount_amount: 0,
  items: [
    {
      service_name: 'Astinet Dedicated 1 Gbps',
      service_code: 'AST-1G',
      description: 'Layanan Dedicated Internet Enterprise Witel Lampung Period July 2026',
      quantity: 1,
      unit_price: 25000000,
      amount: 25000000,
    },
  ],
});

const addItem = () => {
  form.items.push({
    service_name: '',
    service_code: '',
    description: '',
    quantity: 1,
    unit_price: 0,
    amount: 0,
  });
};

const removeItem = (index) => {
  if (form.items.length > 1) {
    form.items.splice(index, 1);
  }
};

const applyPreset = (index, preset) => {
  form.items[index].service_name = preset.name;
  form.items[index].service_code = preset.code;
  form.items[index].description = preset.description;
  form.items[index].unit_price = preset.unit_price;
  calculateRow(index);
};

const calculateRow = (index) => {
  const item = form.items[index];
  item.amount = (parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0);
};

const subtotal = computed(() => {
  return form.items.reduce((sum, item) => sum + ((parseFloat(item.quantity) || 0) * (parseFloat(item.unit_price) || 0)), 0);
});

const taxAmount = computed(() => {
  return (subtotal.value * (parseFloat(form.tax_percentage) || 0)) / 100;
});

const grandTotal = computed(() => {
  return Math.max(0, subtotal.value + taxAmount.value - (parseFloat(form.discount_amount) || 0));
});

const submitInvoice = () => {
  form.post(route('invoices.store'));
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
  <Head title="Create Invoice - Telkom Witel Lampung" />

  <DashboardLayout>
    <div class="space-y-8 font-sans max-w-6xl mx-auto">
      <!-- Header Bar -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <div class="flex items-center gap-2 text-xs text-slate-500 dark:text-slate-400 mb-1">
            <Link :href="route('invoices.index')" class="hover:text-slate-900 dark:hover:text-white transition-colors flex items-center gap-1">
              <ArrowLeft class="w-3.5 h-3.5" />
              <span>Invoices Ledger</span>
            </Link>
            <span>/</span>
            <span class="text-slate-900 dark:text-white font-medium">Create Invoice</span>
          </div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Create Enterprise Invoice</h1>
        </div>
      </div>

      <form @submit.prevent="submitInvoice" class="space-y-8">
        <!-- Section 1: Customer & Invoice Details -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-6">
          <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-3">
            <Building2 class="w-5 h-5 text-telkom-red" />
            <span>Customer & Transaction Overview</span>
          </h2>

          <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 text-xs">
            <!-- Customer Picker -->
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Select Enterprise Client *</label>
              <select
                v-model="form.customer_id"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none"
              >
                <option v-for="c in customers" :key="c.id" :value="c.id">
                  {{ c.company_name }} ({{ c.customer_code }})
                </option>
              </select>
              <p v-if="form.errors.customer_id" class="text-rose-600 dark:text-rose-400 text-[11px] mt-1">{{ form.errors.customer_id }}</p>
            </div>

            <!-- Custom Invoice Number -->
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Invoice Number (Auto if Empty)</label>
              <input
                v-model="form.invoice_number"
                type="text"
                placeholder="e.g. INV/TLK-LPG/2026/07/0005"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white placeholder-slate-400 focus:border-telkom-red focus:outline-none font-mono"
              />
            </div>

            <!-- Status Picker -->
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Invoice Status</label>
              <select
                v-model="form.status"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none capitalize"
              >
                <option value="draft">Draft</option>
                <option value="generated">Generated</option>
                <option value="sent">Sent</option>
              </select>
            </div>
          </div>

          <!-- Dates Grid -->
          <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-4 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Issue Date *</label>
              <input
                v-model="form.issue_date"
                type="date"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Due Date *</label>
              <input
                v-model="form.due_date"
                type="date"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Billing Period Start</label>
              <input
                v-model="form.period_start"
                type="date"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Billing Period End</label>
              <input
                v-model="form.period_end"
                type="date"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none font-mono"
              />
            </div>
          </div>
        </div>

        <!-- Section 2: Dynamic Line Items Table -->
        <div class="p-6 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-4">
          <div class="flex items-center justify-between border-b border-slate-200 dark:border-slate-800 pb-3">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <FileText class="w-5 h-5 text-telkom-red" />
              <span>Service Line Items</span>
            </h2>
            <button
              type="button"
              @click="addItem"
              class="flex items-center gap-1.5 px-3 py-1.5 rounded-xl bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 text-xs font-semibold text-slate-900 dark:text-white transition-colors"
            >
              <Plus class="w-4 h-4 text-telkom-red" />
              <span>Add Item Row</span>
            </button>
          </div>

          <!-- Items Table -->
          <div class="overflow-x-auto">
            <table class="w-full text-left text-xs">
              <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
                <tr>
                  <th class="py-3 px-3 w-64">Service Name & Preset</th>
                  <th class="py-3 px-3">Service Code & Description</th>
                  <th class="py-3 px-3 w-24">Qty</th>
                  <th class="py-3 px-3 w-40">Unit Price (IDR)</th>
                  <th class="py-3 px-3 w-44">Total Amount</th>
                  <th class="py-3 px-3 text-center w-12">Action</th>
                </tr>
              </thead>
              <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
                <tr v-for="(item, idx) in form.items" :key="idx" class="hover:bg-slate-50 dark:hover:bg-slate-900/40">
                  <td class="py-3 px-3 space-y-1.5 align-top">
                    <input
                      v-model="item.service_name"
                      type="text"
                      required
                      placeholder="e.g. Astinet Enterprise"
                      class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none"
                    />
                    <!-- Preset Selector -->
                    <select
                      @change="(e) => {
                        const sel = presetServices.find(p => p.code === e.target.value);
                        if (sel) applyPreset(idx, sel);
                      }"
                      class="w-full px-2 py-1 rounded-lg bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-[10px] text-slate-500 dark:text-slate-400"
                    >
                      <option value="">-- Apply Service Preset --</option>
                      <option v-for="p in presetServices" :key="p.code" :value="p.code">
                        {{ p.name }}
                      </option>
                    </select>
                  </td>
                  <td class="py-3 px-3 space-y-1.5 align-top">
                    <input
                      v-model="item.service_code"
                      type="text"
                      placeholder="Code e.g. AST-1G"
                      class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none font-mono text-[11px]"
                    />
                    <input
                      v-model="item.description"
                      type="text"
                      placeholder="Description details..."
                      class="w-full px-3 py-1.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-700 dark:text-slate-300 focus:border-telkom-red focus:outline-none text-[11px]"
                    />
                  </td>
                  <td class="py-3 px-3 align-top">
                    <input
                      v-model="item.quantity"
                      type="number"
                      step="0.01"
                      min="0.01"
                      required
                      @input="calculateRow(idx)"
                      class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none text-center font-mono"
                    />
                  </td>
                  <td class="py-3 px-3 align-top">
                    <input
                      v-model="item.unit_price"
                      type="number"
                      step="1000"
                      min="0"
                      required
                      @input="calculateRow(idx)"
                      class="w-full px-3 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none font-mono"
                    />
                  </td>
                  <td class="py-3 px-3 align-top font-mono font-bold text-slate-900 dark:text-white pt-4">
                    {{ formatCurrency(item.quantity * item.unit_price) }}
                  </td>
                  <td class="py-3 px-3 align-top text-center pt-3">
                    <button
                      type="button"
                      @click="removeItem(idx)"
                      :disabled="form.items.length === 1"
                      class="p-1.5 rounded-lg text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-900 disabled:opacity-30"
                    >
                      <Trash2 class="w-4 h-4" />
                    </button>
                  </td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Section 3: Summary & Total Calculation Box -->
        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
          <div class="lg:col-span-2 p-6 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-3 text-xs">
            <label class="block font-semibold text-slate-700 dark:text-slate-300">Payment Notes / Terms & Conditions</label>
            <textarea
              v-model="form.notes"
              rows="4"
              class="w-full px-4 py-3 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-telkom-red focus:outline-none"
            ></textarea>
          </div>

          <!-- Total Calculation Card -->
          <div class="p-6 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-4 text-xs">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2 border-b border-slate-200 dark:border-slate-800 pb-2">
              <Calculator class="w-4 h-4 text-telkom-red" />
              <span>Billing Summary</span>
            </h3>

            <div class="space-y-2.5">
              <div class="flex justify-between text-slate-500 dark:text-slate-400">
                <span>Subtotal:</span>
                <span class="font-mono text-slate-900 dark:text-white font-semibold">{{ formatCurrency(subtotal) }}</span>
              </div>
              <div class="flex justify-between items-center text-slate-500 dark:text-slate-400">
                <span>PPN Rate (%):</span>
                <input
                  v-model="form.tax_percentage"
                  type="number"
                  step="0.1"
                  class="w-20 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-right font-mono"
                />
              </div>
              <div class="flex justify-between text-slate-500 dark:text-slate-400">
                <span>PPN Tax Amount:</span>
                <span class="font-mono text-slate-900 dark:text-white font-semibold">{{ formatCurrency(taxAmount) }}</span>
              </div>
              <div class="flex justify-between items-center text-slate-500 dark:text-slate-400 pt-1 border-t border-slate-200 dark:border-slate-800/80">
                <span>Discount (IDR):</span>
                <input
                  v-model="form.discount_amount"
                  type="number"
                  step="1000"
                  class="w-28 px-2.5 py-1 rounded-lg bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white text-right font-mono"
                />
              </div>
              <div class="flex justify-between items-center text-base font-extrabold text-slate-900 dark:text-white pt-3 border-t border-slate-200 dark:border-slate-800">
                <span>Total Billing:</span>
                <span class="font-mono text-telkom-red text-lg">{{ formatCurrency(grandTotal) }}</span>
              </div>
            </div>

            <button
              type="submit"
              :disabled="form.processing"
              class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-lg shadow-indigo-600/30 disabled:opacity-50 mt-4 transition-all"
            >
              <Save class="w-4 h-4" />
              <span>Save & Generate Invoice</span>
            </button>
          </div>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>
