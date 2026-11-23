<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router, useForm } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  Users,
  Plus,
  Search,
  Download,
  Upload,
  Edit2,
  Trash2,
  Building2,
  Mail,
  Phone,
  MapPin,
  FileSpreadsheet,
  CreditCard,
  X,
  UserCheck
} from 'lucide-vue-next';

const props = defineProps({
  customers: Object,
  filters: Object,
});

const search = ref(props.filters.search || '');
const isCreateModalOpen = ref(false);
const isEditModalOpen = ref(false);
const isDeleteModalOpen = ref(false);
const isImportModalOpen = ref(false);
const selectedCustomer = ref(null);

watch(search, (val) => {
  router.get(
    route('customers.index'),
    { search: val },
    { preserveState: true, replace: true }
  );
});

const createForm = useForm({
  customer_code: '',
  name: '',
  company_name: '',
  npwp: '',
  virtual_account: '',
  email: '',
  phone: '',
  address: '',
  city: 'Bandar Lampung',
  status: 'active',
});

const editForm = useForm({
  id: '',
  customer_code: '',
  name: '',
  company_name: '',
  npwp: '',
  virtual_account: '',
  email: '',
  phone: '',
  address: '',
  city: '',
  status: 'active',
});

const importForm = useForm({
  file: null,
});

const submitCreate = () => {
  createForm.post(route('customers.store'), {
    onSuccess: () => {
      isCreateModalOpen.value = false;
      createForm.reset();
    },
  });
};

const openEditModal = (customer) => {
  selectedCustomer.value = customer;
  editForm.id = customer.id;
  editForm.customer_code = customer.customer_code;
  editForm.name = customer.name;
  editForm.company_name = customer.company_name;
  editForm.npwp = customer.npwp || '';
  editForm.virtual_account = customer.virtual_account || '';
  editForm.email = customer.email;
  editForm.phone = customer.phone || '';
  editForm.address = customer.address || '';
  editForm.city = customer.city || 'Bandar Lampung';
  editForm.status = customer.status || 'active';
  isEditModalOpen.value = true;
};

const submitEdit = () => {
  editForm.put(route('customers.update', editForm.id), {
    onSuccess: () => {
      isEditModalOpen.value = false;
    },
  });
};

const openDeleteModal = (customer) => {
  selectedCustomer.value = customer;
  isDeleteModalOpen.value = true;
};

const confirmDelete = () => {
  if (!selectedCustomer.value) return;
  router.delete(route('customers.destroy', selectedCustomer.value.id), {
    onSuccess: () => {
      isDeleteModalOpen.value = false;
      selectedCustomer.value = null;
    },
  });
};

const submitImport = () => {
  importForm.post(route('customers.import'), {
    onSuccess: () => {
      isImportModalOpen.value = false;
      importForm.reset();
    },
  });
};

const handleFileSelect = (event) => {
  importForm.file = event.target.files[0];
};

const exportCustomers = () => {
  window.location.href = route('customers.export');
};
</script>

<template>
  <Head title="Customer Management - Telkom Witel Lampung" />

  <DashboardLayout>
    <div class="space-y-8 font-sans">
      <!-- Top Section Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">Customer Management</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Manage B2B enterprise client accounts, NPWP tax IDs, and Virtual Accounts for PT. Telkom Lampung</p>
        </div>
        <div class="flex flex-wrap items-center gap-3">
          <!-- Export Button -->
          <button
            @click="exportCustomers"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Download class="w-4 h-4 text-emerald-600 dark:text-emerald-400" />
            <span>Export Excel</span>
          </button>

          <!-- Import Button -->
          <button
            @click="isImportModalOpen = true"
            class="flex items-center gap-2 px-3.5 py-2 rounded-xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors shadow-sm dark:shadow-md"
          >
            <Upload class="w-4 h-4 text-sky-600 dark:text-sky-400" />
            <span>Import Excel</span>
          </button>

          <!-- Add Customer Button -->
          <button
            @click="isCreateModalOpen = true"
            class="flex items-center gap-2 px-4 py-2 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-semibold text-white shadow-lg shadow-indigo-600/20 transition-all"
          >
            <Plus class="w-4 h-4" />
            <span>Add Customer</span>
          </button>
        </div>
      </div>

      <!-- Filters & Search Bar -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg flex flex-col sm:flex-row items-center justify-between gap-4">
        <div class="relative w-full sm:w-80">
          <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
          <input
            v-model="search"
            type="text"
            placeholder="Search by code, company, NPWP, VA..."
            class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all"
          />
        </div>
        <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
          Showing <span class="font-bold text-slate-900 dark:text-white">{{ customers.data.length }}</span> of <span class="font-bold text-slate-900 dark:text-white">{{ customers.total }}</span> registered enterprise clients
        </div>
      </div>

      <!-- Customers Data Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-4">Customer Code</th>
                <th class="py-3.5 px-4">Company & Contact</th>
                <th class="py-3.5 px-4">NPWP Tax ID</th>
                <th class="py-3.5 px-4">Virtual Account</th>
                <th class="py-3.5 px-4">Email & Phone</th>
                <th class="py-3.5 px-4">City / Address</th>
                <th class="py-3.5 px-4">Status</th>
                <th class="py-3.5 px-4 text-right">Actions</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
              <tr
                v-for="customer in customers.data"
                :key="customer.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors"
              >
                <td class="py-3.5 px-4 font-mono font-bold text-slate-900 dark:text-white">{{ customer.customer_code }}</td>
                <td class="py-3.5 px-4">
                  <p class="font-semibold text-slate-900 dark:text-slate-100">{{ customer.company_name }}</p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                    <Building2 class="w-3 h-3 text-slate-400" />
                    <span>{{ customer.name }}</span>
                  </p>
                </td>
                <td class="py-3.5 px-4 font-mono text-slate-600 dark:text-slate-300">{{ customer.npwp || '—' }}</td>
                <td class="py-3.5 px-4 font-mono font-semibold text-sky-600 dark:text-sky-400">
                  <div class="flex items-center gap-1.5">
                    <CreditCard class="w-3.5 h-3.5 text-sky-500" />
                    <span>{{ customer.virtual_account || '—' }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <p class="text-slate-700 dark:text-slate-300 flex items-center gap-1">
                    <Mail class="w-3 h-3 text-slate-400" />
                    <span>{{ customer.email }}</span>
                  </p>
                  <p class="text-[11px] text-slate-500 dark:text-slate-400 flex items-center gap-1 mt-0.5">
                    <Phone class="w-3 h-3 text-slate-400" />
                    <span>{{ customer.phone || '—' }}</span>
                  </p>
                </td>
                <td class="py-3.5 px-4 text-slate-500 dark:text-slate-400 max-w-xs truncate">{{ customer.city }}, {{ customer.address }}</td>
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-[11px] font-semibold border',
                      customer.status === 'active'
                        ? 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30'
                        : 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/30'
                    ]"
                  >
                    {{ customer.status === 'active' ? 'Active' : 'Inactive' }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <div class="flex items-center justify-end gap-2">
                    <button
                      @click="openEditModal(customer)"
                      class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                      title="Edit Customer"
                    >
                      <Edit2 class="w-3.5 h-3.5" />
                    </button>
                    <button
                      @click="openDeleteModal(customer)"
                      class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-rose-600 dark:hover:text-rose-400 hover:bg-slate-200 dark:hover:bg-slate-800 transition-colors"
                      title="Delete Customer"
                    >
                      <Trash2 class="w-3.5 h-3.5" />
                    </button>
                  </div>
                </td>
              </tr>
              <tr v-if="customers.data.length === 0">
                <td colspan="8" class="py-12 text-center text-slate-400">
                  <Users class="w-8 h-8 mx-auto text-slate-400 mb-2" />
                  <p class="text-xs font-semibold text-slate-600 dark:text-slate-400">No enterprise customers found</p>
                  <p class="text-[11px] text-slate-400 mt-1">Try adjusting your search filter or add a new customer</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-500 dark:text-slate-400">
          <div>
            Showing <span class="font-semibold text-slate-900 dark:text-white">{{ customers.from || 0 }}</span> to <span class="font-semibold text-slate-900 dark:text-white">{{ customers.to || 0 }}</span> of <span class="font-semibold text-slate-900 dark:text-white">{{ customers.total }}</span> results
          </div>
          <div class="flex items-center gap-1.5">
            <Link
              v-for="link in customers.links"
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

    <!-- CREATE CUSTOMER MODAL -->
    <div
      v-if="isCreateModalOpen"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto"
    >
      <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 relative shadow-2xl my-8">
        <button
          @click="isCreateModalOpen = false"
          class="absolute top-5 right-5 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800"
        >
          <X class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
          <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
            <UserCheck class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Add Enterprise Customer</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Register a new B2B client account & Virtual Account</p>
          </div>
        </div>

        <form @submit.prevent="submitCreate" class="space-y-4 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Code (Auto if empty)</label>
              <input
                v-model="createForm.customer_code"
                type="text"
                placeholder="e.g. CUST-TLK-009"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">PIC Contact Person *</label>
              <input
                v-model="createForm.name"
                type="text"
                required
                placeholder="Person Name"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Company / Institution Name *</label>
            <input
              v-model="createForm.company_name"
              type="text"
              required
              placeholder="e.g. PT Great Giant Pineapple"
              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NPWP Tax ID</label>
              <input
                v-model="createForm.npwp"
                type="text"
                placeholder="01.234.567.8-901.000"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Virtual Account (Bank Mandiri)</label>
              <input
                v-model="createForm.virtual_account"
                type="text"
                placeholder="8800108..."
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono text-sky-600 dark:text-sky-400"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Official Billing Email *</label>
              <input
                v-model="createForm.email"
                type="email"
                required
                placeholder="finance@company.com"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Telephone / Whatsapp</label>
              <input
                v-model="createForm.phone"
                type="text"
                placeholder="0812..."
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Street Address</label>
              <input
                v-model="createForm.address"
                type="text"
                placeholder="Jl. Raya..."
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">City</label>
              <input
                v-model="createForm.city"
                type="text"
                placeholder="Bandar Lampung"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
            <button
              type="button"
              @click="isCreateModalOpen = false"
              class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="createForm.processing"
              class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-lg shadow-indigo-600/30 disabled:opacity-50"
            >
              Save Customer
            </button>
          </div>
        </form>
      </div>
    </div>

    <!-- EDIT CUSTOMER MODAL -->
    <div
      v-if="isEditModalOpen"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto"
    >
      <div class="w-full max-w-lg bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 relative shadow-2xl my-8">
        <button
          @click="isEditModalOpen = false"
          class="absolute top-5 right-5 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800"
        >
          <X class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
          <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
            <Edit2 class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Edit Enterprise Customer</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Update company details, NPWP, or Virtual Account</p>
          </div>
        </div>

        <form @submit.prevent="submitEdit" class="space-y-4 text-xs">
          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Customer Code *</label>
              <input
                v-model="editForm.customer_code"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">PIC Contact Person *</label>
              <input
                v-model="editForm.name"
                type="text"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Company / Institution Name *</label>
            <input
              v-model="editForm.company_name"
              type="text"
              required
              class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
            />
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">NPWP Tax ID</label>
              <input
                v-model="editForm.npwp"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Virtual Account</label>
              <input
                v-model="editForm.virtual_account"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none font-mono text-sky-600 dark:text-sky-400"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Billing Email *</label>
              <input
                v-model="editForm.email"
                type="email"
                required
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Telephone</label>
              <input
                v-model="editForm.phone"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
          </div>

          <div class="grid grid-cols-1 sm:grid-cols-3 gap-4">
            <div class="sm:col-span-2">
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Address</label>
              <input
                v-model="editForm.address"
                type="text"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              />
            </div>
            <div>
              <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1">Account Status</label>
              <select
                v-model="editForm.status"
                class="w-full px-3.5 py-2.5 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white focus:border-indigo-600 focus:outline-none"
              >
                <option value="active">Active</option>
                <option value="inactive">Inactive</option>
              </select>
            </div>
          </div>

          <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end gap-3">
            <button
              type="button"
              @click="isEditModalOpen = false"
              class="px-4 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
            >
              Cancel
            </button>
            <button
              type="submit"
              :disabled="editForm.processing"
              class="px-5 py-2.5 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-lg shadow-indigo-600/30 disabled:opacity-50"
            >
              Update Changes
            </button>
          </div>
        </form>
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
          <h3 class="text-base font-bold text-slate-900 dark:text-white">Delete Customer Account?</h3>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">
            Are you sure you want to soft-delete <span class="font-bold text-slate-900 dark:text-white">{{ selectedCustomer?.company_name }}</span>? Active invoices will be retained.
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

    <!-- IMPORT EXCEL MODAL -->
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
            <h3 class="text-lg font-bold text-slate-900 dark:text-white">Import Customer Directory Excel</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Bulk upload enterprise client database</p>
          </div>
        </div>

        <form @submit.prevent="submitImport" class="space-y-5 text-xs">
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border-2 border-dashed border-slate-200 dark:border-slate-800 text-center hover:border-sky-500/50 transition-colors">
            <Upload class="w-8 h-8 text-sky-500 mx-auto mb-2" />
            <p class="text-xs font-semibold text-slate-900 dark:text-white mb-1">Select Customer Excel File</p>
            <p class="text-[11px] text-slate-500 mb-3">Supported formats: .xlsx, .xls, .csv</p>
            <input
              type="file"
              accept=".xlsx,.xls,.csv"
              required
              @change="handleFileSelect"
              class="block w-full text-xs text-slate-500 dark:text-slate-400 file:mr-4 file:py-2 file:px-4 file:rounded-xl file:border-0 file:text-xs file:font-semibold file:bg-sky-500/10 file:text-sky-600 dark:file:text-sky-400 hover:file:bg-sky-500/20"
            />
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
              Start Customer Import
            </button>
          </div>
        </form>
      </div>
    </div>
  </DashboardLayout>
</template>
