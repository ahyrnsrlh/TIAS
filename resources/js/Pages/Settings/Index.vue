<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  Settings,
  Building2,
  Mail,
  FileText,
  Sliders,
  Save,
  CheckCircle2,
  Send,
  User,
  ShieldCheck,
  Globe,
  CreditCard
} from 'lucide-vue-next';

const props = defineProps({
  settings: Object,
  flash: Object,
});

const activeTab = ref('company');

const form = useForm({
  settings: {
    company_name: props.settings.company_name || 'PT. Telkom Indonesia (Persero) Tbk',
    witel_name: props.settings.witel_name || 'Witel Lampung',
    company_address: props.settings.company_address || 'Jl. Majapahit No.1, Enggal, Bandar Lampung, Lampung 35111',
    company_phone: props.settings.company_phone || '(0721) 147',
    company_email: props.settings.company_email || 'billing.lampung@telkom.co.id',
    company_npwp: props.settings.company_npwp || '01.000.013.1-093.000',
    company_virtual_account: props.settings.company_virtual_account || '8800108920192',
    default_tax_rate: props.settings.default_tax_rate || 11.0,
    currency_symbol: props.settings.currency_symbol || 'Rp',
    payment_terms_default: props.settings.payment_terms_default || 'Net 30 Days',
    invoice_number_prefix: props.settings.invoice_number_prefix || 'INV-TELKOM',
    smtp_host: props.settings.smtp_host || 'mail.telkom-lampung.co.id',
    smtp_port: props.settings.smtp_port || 587,
    smtp_encryption: props.settings.smtp_encryption || 'tls',
    smtp_username: props.settings.smtp_username || 'billing@telkom-lampung.co.id',
    smtp_password: props.settings.smtp_password || '********',
    smtp_sender_name: props.settings.smtp_sender_name || 'PT. Telkom Witel Lampung Billing System',
    invoice_header_title: props.settings.invoice_header_title || 'NOTA TAGIHAN & FAKTUR PAJAK ENTERPRISE',
    invoice_footer_notes: props.settings.invoice_footer_notes || 'Pembayaran dianggap sah apabila dana telah efektif diterima pada Rekening Virtual Account PT. Telkom Indonesia.',
    invoice_manager_title: props.settings.invoice_manager_title || 'Manager Finance & Billing Witel Lampung',
    invoice_digital_stamp_text: props.settings.invoice_digital_stamp_text || 'TELKOM INDONESIA WITEL LAMPUNG - OFFICIALLY VERIFIED DIGITAL INVOICE',
  },
});

const testSmtpForm = useForm({
  recipient: 'test@company.com',
});

const saveSettings = () => {
  form.post(route('settings.store'), {
    preserveScroll: true,
  });
};

const sendTestSmtp = () => {
  testSmtpForm.post(route('settings.test-smtp'), {
    preserveScroll: true,
  });
};
</script>

<template>
  <Head title="System Management & Settings - Telkom Witel Lampung" />

  <DashboardLayout>
    <div class="space-y-8 font-sans max-w-6xl mx-auto">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">System Management & Parameters</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure company profiles, general tax parameters, SMTP email servers, and invoice templates</p>
        </div>
        <div>
          <Link
            :href="route('profile.edit')"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 hover:border-slate-300 dark:hover:border-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300 transition-colors shadow-sm dark:shadow-md"
          >
            <User class="w-4 h-4 text-sky-500" />
            <span>Admin Profile Account</span>
          </Link>
        </div>
      </div>

      <!-- Settings Navigation Tabs -->
      <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-800">
        <button
          @click="activeTab = 'company'"
          :class="[
            'flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap',
            activeTab === 'company'
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
              : 'bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
          ]"
        >
          <Building2 class="w-4 h-4" />
          <span>Company Profile</span>
        </button>

        <button
          @click="activeTab = 'general'"
          :class="[
            'flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap',
            activeTab === 'general'
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
              : 'bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
          ]"
        >
          <Sliders class="w-4 h-4" />
          <span>General Settings</span>
        </button>

        <button
          @click="activeTab = 'smtp'"
          :class="[
            'flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap',
            activeTab === 'smtp'
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
              : 'bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
          ]"
        >
          <Mail class="w-4 h-4" />
          <span>SMTP Email Settings</span>
        </button>

        <button
          @click="activeTab = 'template'"
          :class="[
            'flex items-center gap-2 px-4 py-2.5 rounded-2xl text-xs font-bold transition-all whitespace-nowrap',
            activeTab === 'template'
              ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
              : 'bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
          ]"
        >
          <FileText class="w-4 h-4" />
          <span>Invoice Template</span>
        </button>
      </div>

      <!-- Settings Content Panels -->
      <form @submit.prevent="saveSettings" class="space-y-6">
        <!-- 1. COMPANY PROFILE TAB -->
        <div v-if="activeTab === 'company'" class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-6">
          <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Building2 class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <span>PT. Telkom Indonesia Witel Lampung Profile</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Official corporate details printed on B2B invoices and billing letters</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Company Legal Name *</label>
              <input
                v-model="form.settings.company_name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Witel Regional Unit *</label>
              <input
                v-model="form.settings.witel_name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Corporate NPWP Tax ID *</label>
              <input
                v-model="form.settings.company_npwp"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Default Bank Mandiri Virtual Account *</label>
              <input
                v-model="form.settings.company_virtual_account"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono text-sky-600 dark:text-sky-400"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Billing Support Email *</label>
              <input
                v-model="form.settings.company_email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Office Telephone</label>
              <input
                v-model="form.settings.company_phone"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5 text-xs">Witel Office Address *</label>
            <textarea
              v-model="form.settings.company_address"
              rows="2"
              required
              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none text-xs"
            ></textarea>
          </div>
        </div>

        <!-- 2. GENERAL SETTINGS TAB -->
        <div v-if="activeTab === 'general'" class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-6">
          <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Sliders class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <span>General Billing Operational Parameters</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure default PPN tax rates, payment terms, and invoice numbering formats</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Default PPN Tax Rate (%) *</label>
              <input
                v-model="form.settings.default_tax_rate"
                type="number"
                step="0.1"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Currency Symbol *</label>
              <input
                v-model="form.settings.currency_symbol"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Default Payment Terms *</label>
              <input
                v-model="form.settings.payment_terms_default"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Invoice Number Prefix *</label>
              <input
                v-model="form.settings.invoice_number_prefix"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
          </div>
        </div>

        <!-- 3. SMTP SETTINGS TAB -->
        <div v-if="activeTab === 'smtp'" class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-6">
          <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Mail class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <span>SMTP Mail Server Configuration</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Configure outbound mail server settings for automated PDF invoice email dispatches</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">SMTP Host Server *</label>
              <input
                v-model="form.settings.smtp_host"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">SMTP Port *</label>
              <input
                v-model="form.settings.smtp_port"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Encryption Standard</label>
              <select
                v-model="form.settings.smtp_encryption"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none uppercase"
              >
                <option value="tls">TLS</option>
                <option value="ssl">SSL</option>
                <option value="none">None</option>
              </select>
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">SMTP Username *</label>
              <input
                v-model="form.settings.smtp_username"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">SMTP Password</label>
              <input
                v-model="form.settings.smtp_password"
                type="password"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Sender Display Name *</label>
              <input
                v-model="form.settings.smtp_sender_name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <!-- Test SMTP Email Card -->
          <div class="p-5 rounded-2xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800/80 space-y-3 pt-4 text-xs">
            <h3 class="font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <Send class="w-4 h-4 text-sky-500" />
              <span>Test Outbound SMTP Delivery</span>
            </h3>
            <p class="text-[11px] text-slate-500 dark:text-slate-400">Trigger a live test email to verify your mail server configuration:</p>
            <div class="flex flex-col sm:flex-row items-center gap-3">
              <input
                v-model="testSmtpForm.recipient"
                type="email"
                placeholder="recipient@company.com"
                class="w-full sm:w-80 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
              <button
                type="button"
                @click="sendTestSmtp"
                :disabled="testSmtpForm.processing"
                class="w-full sm:w-auto px-4 py-2 rounded-xl bg-sky-600 hover:bg-sky-500 text-white font-bold shadow-md shadow-sky-600/30 disabled:opacity-50 flex items-center justify-center gap-1.5"
              >
                <Send class="w-3.5 h-3.5" />
                <span>Test Connection</span>
              </button>
            </div>
          </div>
        </div>

        <!-- 4. INVOICE TEMPLATE TAB -->
        <div v-if="activeTab === 'template'" class="p-6 sm:p-8 rounded-3xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-xl space-y-6">
          <div class="border-b border-slate-200 dark:border-slate-800 pb-4">
            <h2 class="text-base font-bold text-slate-900 dark:text-white flex items-center gap-2">
              <FileText class="w-5 h-5 text-indigo-600 dark:text-indigo-400" />
              <span>Invoice Printable Template Customization</span>
            </h2>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Customize header titles, digital stamp badges, and authorized manager signature titles</p>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-6 text-xs">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Printable Letterhead Title *</label>
              <input
                v-model="form.settings.invoice_header_title"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Authorized Manager Title *</label>
              <input
                v-model="form.settings.invoice_manager_title"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Digital Stamp Badge Text *</label>
              <input
                v-model="form.settings.invoice_digital_stamp_text"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Default Invoice Footer Notes *</label>
              <textarea
                v-model="form.settings.invoice_footer_notes"
                rows="3"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              ></textarea>
            </div>
          </div>
        </div>

        <!-- Submit Button Toolbar -->
        <div class="p-4 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg flex justify-end">
          <button
            type="submit"
            :disabled="form.processing"
            class="flex items-center gap-2 px-6 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-lg shadow-indigo-600/30 transition-all disabled:opacity-50"
          >
            <Save class="w-4 h-4" />
            <span>Save All Settings</span>
          </button>
        </div>
      </form>
    </div>
  </DashboardLayout>
</template>
