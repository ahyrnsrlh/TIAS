<script setup>
import { ref, watch } from 'vue';
import { Head, Link, router } from '@inertiajs/vue3';
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import {
  Activity,
  Search,
  Clock,
  User,
  Globe,
  Filter,
  Eye,
  X,
  Code,
  Shield
} from 'lucide-vue-next';

const props = defineProps({
  logs: Object,
  actions: Array,
  filters: Object,
});

const search = ref(props.filters.search || '');
const selectedAction = ref(props.filters.action || '');
const selectedLog = ref(null);
const isDetailModalOpen = ref(false);

watch([search, selectedAction], () => {
  router.get(
    route('activity-logs.index'),
    { search: search.value, action: selectedAction.value },
    { preserveState: true, replace: true }
  );
});

const openDetailModal = (log) => {
  selectedLog.value = log;
  isDetailModalOpen.value = true;
};

const getActionBadgeClass = (action) => {
  if (!action) return 'bg-slate-100 dark:bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-500/30';
  if (action.includes('PAYMENT')) return 'bg-emerald-50 dark:bg-emerald-500/10 text-emerald-700 dark:text-emerald-400 border-emerald-200 dark:border-emerald-500/30';
  if (action.includes('CREATE')) return 'bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 border-indigo-200 dark:border-indigo-500/30';
  if (action.includes('UPDATE')) return 'bg-sky-50 dark:bg-sky-500/10 text-sky-700 dark:text-sky-400 border-sky-200 dark:border-sky-500/30';
  if (action.includes('DELETE')) return 'bg-rose-50 dark:bg-rose-500/10 text-rose-700 dark:text-rose-400 border-rose-200 dark:border-rose-500/30';
  if (action.includes('TAX')) return 'bg-purple-50 dark:bg-purple-500/10 text-purple-700 dark:text-purple-400 border-purple-200 dark:border-purple-500/30';
  return 'bg-slate-100 dark:bg-slate-500/10 text-slate-700 dark:text-slate-400 border-slate-200 dark:border-slate-500/30';
};
</script>

<template>
  <Head title="Activity Log & Audit Trail - Telkom Witel Lampung" />

  <DashboardLayout>
    <div class="space-y-8 font-sans">
      <!-- Page Header -->
      <div class="flex flex-col sm:flex-row sm:items-center justify-between gap-4">
        <div>
          <h1 class="text-2xl font-extrabold text-slate-900 dark:text-white tracking-tight">System Audit Trail Stream</h1>
          <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Real-time security log tracking for all user activities, invoice generation, email dispatches, and system updates</p>
        </div>
      </div>

      <!-- Action Filter Tabs & Search -->
      <div class="p-4 rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg space-y-4">
        <!-- Filter Tabs -->
        <div class="flex items-center gap-2 overflow-x-auto pb-2 border-b border-slate-200 dark:border-slate-800/80">
          <button
            @click="selectedAction = ''"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap',
              selectedAction === ''
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            All Actions
          </button>
          <button
            v-for="act in actions"
            :key="act"
            @click="selectedAction = act"
            :class="[
              'px-3.5 py-1.5 rounded-xl text-xs font-semibold transition-all whitespace-nowrap font-mono',
              selectedAction === act
                ? 'bg-indigo-600 text-white shadow-md shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-900'
            ]"
          >
            {{ act }}
          </button>
        </div>

        <!-- Search Bar -->
        <div class="flex flex-col sm:flex-row items-center justify-between gap-4 pt-1">
          <div class="relative w-full sm:w-96">
            <Search class="w-4 h-4 text-slate-400 absolute left-3.5 top-3" />
            <input
              v-model="search"
              type="text"
              placeholder="Search by action, description, IP address..."
              class="w-full pl-10 pr-4 py-2 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all"
            />
          </div>
          <div class="text-xs text-slate-500 dark:text-slate-400 font-medium">
            Total Log Entries: <span class="font-bold text-slate-900 dark:text-white">{{ logs.total }}</span>
          </div>
        </div>
      </div>

      <!-- Activity Logs Table -->
      <div class="rounded-2xl bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 shadow-sm dark:shadow-lg overflow-hidden">
        <div class="overflow-x-auto">
          <table class="w-full text-left text-xs">
            <thead class="bg-slate-50 dark:bg-slate-900/80 text-slate-600 dark:text-slate-400 font-semibold border-b border-slate-200 dark:border-slate-800">
              <tr>
                <th class="py-3.5 px-4 w-40">Timestamp</th>
                <th class="py-3.5 px-4">User Officer</th>
                <th class="py-3.5 px-4">Action Event</th>
                <th class="py-3.5 px-4">Description</th>
                <th class="py-3.5 px-4 w-32">IP Address</th>
                <th class="py-3.5 px-4 text-right w-20">Details</th>
              </tr>
            </thead>
            <tbody class="divide-y divide-slate-200 dark:divide-slate-800/60 text-slate-700 dark:text-slate-300">
              <tr
                v-for="log in logs.data"
                :key="log.id"
                class="hover:bg-slate-50 dark:hover:bg-slate-900/50 transition-colors"
              >
                <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400 text-[11px]">
                  <div class="flex items-center gap-1.5">
                    <Clock class="w-3.5 h-3.5 text-slate-400" />
                    <span>{{ log.created_at ? log.created_at.substring(0, 19).replace('T', ' ') : 'Just Now' }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4">
                  <div class="flex items-center gap-1.5">
                    <User class="w-3.5 h-3.5 text-slate-400" />
                    <span class="font-semibold text-slate-900 dark:text-slate-200">{{ log.user?.name || 'System Auto' }}</span>
                  </div>
                  <p class="text-[10px] text-slate-500 font-mono pl-5">{{ log.user?.email || 'system@telkom-lampung.co.id' }}</p>
                </td>
                <td class="py-3.5 px-4">
                  <span
                    :class="[
                      'px-2.5 py-1 rounded-full text-[10px] font-mono font-bold border',
                      getActionBadgeClass(log.action)
                    ]"
                  >
                    {{ log.action }}
                  </span>
                </td>
                <td class="py-3.5 px-4 text-slate-800 dark:text-slate-200 max-w-md truncate">
                  {{ log.description }}
                </td>
                <td class="py-3.5 px-4 font-mono text-slate-500 dark:text-slate-400 text-[11px]">
                  <div class="flex items-center gap-1">
                    <Globe class="w-3 h-3 text-slate-400" />
                    <span>{{ log.ip_address || '127.0.0.1' }}</span>
                  </div>
                </td>
                <td class="py-3.5 px-4 text-right">
                  <button
                    @click="openDetailModal(log)"
                    class="p-1.5 rounded-lg bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-slate-600 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
                    title="View JSON Payload"
                  >
                    <Eye class="w-3.5 h-3.5" />
                  </button>
                </td>
              </tr>
              <tr v-if="logs.data.length === 0">
                <td colspan="6" class="py-12 text-center text-slate-500">
                  <Activity class="w-8 h-8 mx-auto text-slate-400 mb-2" />
                  <p class="text-xs font-semibold text-slate-700 dark:text-slate-300">No activity log records found</p>
                  <p class="text-[11px] text-slate-500 mt-1">Try selecting a different action filter or search query</p>
                </td>
              </tr>
            </tbody>
          </table>
        </div>

        <!-- Pagination Controls -->
        <div class="p-4 bg-slate-50 dark:bg-slate-900/60 border-t border-slate-200 dark:border-slate-800 flex items-center justify-between text-xs text-slate-600 dark:text-slate-400">
          <div>
            Showing <span class="font-semibold text-slate-900 dark:text-white">{{ logs.from || 0 }}</span> to <span class="font-semibold text-slate-900 dark:text-white">{{ logs.to || 0 }}</span> of <span class="font-semibold text-slate-900 dark:text-white">{{ logs.total }}</span> audit records
          </div>
          <div class="flex items-center gap-1.5">
            <Link
              v-for="link in logs.links"
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

    <!-- JSON DETAIL PAYLOAD MODAL -->
    <div
      v-if="isDetailModalOpen && selectedLog"
      class="fixed inset-0 bg-slate-950/80 backdrop-blur-sm flex items-center justify-center p-4 z-50 overflow-y-auto"
    >
      <div class="w-full max-w-2xl bg-white dark:bg-slate-900 border border-slate-200 dark:border-slate-800 rounded-3xl p-6 sm:p-8 space-y-6 relative shadow-2xl my-8">
        <button
          @click="isDetailModalOpen = false"
          class="absolute top-5 right-5 p-1.5 rounded-lg text-slate-400 hover:text-slate-700 dark:hover:text-white hover:bg-slate-100 dark:hover:bg-slate-800"
        >
          <X class="w-5 h-5" />
        </button>

        <div class="flex items-center gap-3 border-b border-slate-200 dark:border-slate-800 pb-4">
          <div class="p-3 rounded-2xl bg-indigo-50 dark:bg-indigo-500/10 text-indigo-600 dark:text-indigo-400">
            <Code class="w-6 h-6" />
          </div>
          <div>
            <h3 class="text-base font-bold text-slate-900 dark:text-white">Audit Log JSON Payload</h3>
            <p class="text-xs text-slate-500 dark:text-slate-400">Action: <span class="font-mono text-indigo-600 dark:text-indigo-400 font-semibold">{{ selectedLog.action }}</span></p>
          </div>
        </div>

        <div class="space-y-4 text-xs">
          <div class="p-4 rounded-2xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 space-y-2">
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
              <span>Timestamp:</span>
              <span class="font-mono text-slate-900 dark:text-white">{{ selectedLog.created_at }}</span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
              <span>User Officer:</span>
              <span class="font-semibold text-slate-900 dark:text-white">{{ selectedLog.user?.name || 'System' }}</span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
              <span>IP Address:</span>
              <span class="font-mono text-slate-900 dark:text-white">{{ selectedLog.ip_address || '127.0.0.1' }}</span>
            </div>
            <div class="flex justify-between text-slate-500 dark:text-slate-400">
              <span>User Agent:</span>
              <span class="font-mono text-[10px] text-slate-600 dark:text-slate-300 max-w-xs truncate">{{ selectedLog.user_agent || 'Mozilla/5.0' }}</span>
            </div>
          </div>

          <div>
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Description</label>
            <p class="p-3 rounded-xl bg-slate-50 dark:bg-slate-950 border border-slate-200 dark:border-slate-800 text-slate-900 dark:text-white">{{ selectedLog.description }}</p>
          </div>

          <div v-if="selectedLog.payload">
            <label class="block font-semibold text-slate-700 dark:text-slate-300 mb-1.5">Context Payload (JSON)</label>
            <pre class="p-4 rounded-2xl bg-slate-900 text-slate-100 font-mono text-[11px] overflow-x-auto border border-slate-800">{{ JSON.stringify(selectedLog.payload, null, 2) }}</pre>
          </div>
        </div>

        <div class="pt-4 border-t border-slate-200 dark:border-slate-800 flex justify-end">
          <button
            @click="isDetailModalOpen = false"
            class="px-5 py-2.5 rounded-xl bg-slate-100 dark:bg-slate-800 hover:bg-slate-200 dark:hover:bg-slate-700 text-xs font-semibold text-slate-700 dark:text-slate-300"
          >
            Close
          </button>
        </div>
      </div>
    </div>
  </DashboardLayout>
</template>
