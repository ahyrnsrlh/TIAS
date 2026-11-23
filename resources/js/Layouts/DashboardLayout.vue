<script setup>
import { ref, onMounted } from 'vue';
import { Link, usePage, router } from '@inertiajs/vue3';
import {
  LayoutDashboard,
  FileText,
  Users,
  Briefcase,
  BarChart3,
  Settings,
  LogOut,
  Bell,
  Search,
  ChevronDown,
  Plus,
  Building2,
  ShieldCheck,
  Menu,
  X,
  Clock,
  Activity,
  Sun,
  Moon
} from 'lucide-vue-next';

const page = usePage();
const user = page.props.auth?.user || { name: 'Admin TIAS', email: 'admin@telkom-lampung.co.id' };

const isSidebarOpen = ref(true);
const isUserMenuOpen = ref(false);
const isDarkMode = ref(true);
const currentTime = ref('');

const updateClock = () => {
  const now = new Date();
  currentTime.value = now.toLocaleDateString('id-ID', {
    weekday: 'long',
    day: 'numeric',
    month: 'short',
    year: 'numeric'
  }) + ' | ' + now.toLocaleTimeString('id-ID') + ' WIB';
};

const toggleTheme = () => {
  isDarkMode.value = !isDarkMode.value;
  if (isDarkMode.value) {
    document.documentElement.classList.add('dark');
    localStorage.setItem('theme', 'dark');
  } else {
    document.documentElement.classList.remove('dark');
    localStorage.setItem('theme', 'light');
  }
};

onMounted(() => {
  updateClock();
  setInterval(updateClock, 1000);
  isDarkMode.value = document.documentElement.classList.contains('dark');
});

const navigation = [
  { name: 'Dashboard', href: '/dashboard', icon: LayoutDashboard },
  { name: 'Invoices & Billing', href: '/invoices', icon: FileText },
  { name: 'Customer Directory', href: '/customers', icon: Users },
  { name: 'Activity Log Audit', href: '/activity-logs', icon: Activity },
  { name: 'System Settings', href: '/settings', icon: Settings },
];

const logout = () => {
  router.post(route('logout'));
};
</script>

<template>
  <div class="min-h-screen bg-slate-50 dark:bg-slate-900 text-slate-900 dark:text-slate-100 flex flex-col font-sans transition-colors duration-200">
    <!-- Top Header Bar -->
    <header class="h-16 bg-white dark:bg-slate-950 border-b border-slate-200 dark:border-slate-800 flex items-center justify-between px-4 lg:px-6 sticky top-0 z-40 shadow-sm dark:shadow-md transition-colors">
      <div class="flex items-center gap-4">
        <button
          @click="isSidebarOpen = !isSidebarOpen"
          class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
        >
          <Menu v-if="!isSidebarOpen" class="w-5 h-5" />
          <X v-else class="w-5 h-5" />
        </button>

        <!-- Brand Identity -->
        <div class="flex items-center gap-3">
          <img src="/logo-telkom.webp" alt="PT. Telkom Indonesia Logo" class="h-9 w-auto object-contain" />
          <div>
            <div class="flex items-center gap-2">
              <span class="font-bold text-base tracking-wide text-slate-900 dark:text-white">TIAS</span>
              <span class="text-xs px-2 py-0.5 rounded bg-indigo-50 dark:bg-indigo-500/10 text-indigo-700 dark:text-indigo-400 font-semibold border border-indigo-200 dark:border-indigo-500/30">Witel Lampung</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400 font-mono">Telkom Invoice Automation System</p>
          </div>
        </div>
      </div>

      <!-- Center Live Clock -->
      <div class="hidden md:flex items-center gap-2 px-3 py-1.5 rounded-full bg-slate-100 dark:bg-slate-900 border border-slate-200 dark:border-slate-800 text-xs font-mono text-slate-600 dark:text-slate-400">
        <Clock class="w-3.5 h-3.5 text-indigo-600 dark:text-indigo-400" />
        <span>{{ currentTime }}</span>
      </div>

      <!-- Right Header Controls -->
      <div class="flex items-center gap-3">
        <!-- Theme Toggle Switch -->
        <button
          @click="toggleTheme"
          class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white transition-colors"
          :title="isDarkMode ? 'Switch to Light Mode' : 'Switch to Dark Mode'"
        >
          <Sun v-if="isDarkMode" class="w-5 h-5 text-amber-400" />
          <Moon v-else class="w-5 h-5 text-slate-700" />
        </button>

        <!-- Notification Bell -->
        <button class="p-2 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 text-slate-500 dark:text-slate-400 hover:text-slate-900 dark:hover:text-white relative transition-colors">
          <Bell class="w-5 h-5" />
          <span class="absolute top-1.5 right-1.5 w-2 h-2 rounded-full bg-indigo-600 ring-2 ring-white dark:ring-slate-950"></span>
        </button>

        <!-- Profile Menu -->
        <div class="relative">
          <button
            @click="isUserMenuOpen = !isUserMenuOpen"
            class="flex items-center gap-2.5 p-1.5 rounded-lg hover:bg-slate-100 dark:hover:bg-slate-800 transition-colors"
          >
            <div class="w-8 h-8 rounded-full bg-gradient-to-tr from-indigo-600 to-sky-500 flex items-center justify-center font-bold text-xs text-white">
              {{ user.name ? user.name.charAt(0) : 'A' }}
            </div>
            <div class="hidden lg:block text-left">
              <p class="text-xs font-semibold text-slate-900 dark:text-white leading-tight">{{ user.name }}</p>
              <p class="text-[10px] text-slate-500 dark:text-slate-400">Single Admin</p>
            </div>
            <ChevronDown class="w-4 h-4 text-slate-400" />
          </button>

          <!-- Dropdown -->
          <div
            v-if="isUserMenuOpen"
            class="absolute right-0 mt-2 w-56 bg-white dark:bg-slate-950 border border-slate-200 dark:border-slate-800 rounded-xl shadow-2xl py-2 z-50"
          >
            <div class="px-4 py-2 border-b border-slate-200 dark:border-slate-800">
              <p class="text-xs font-bold text-slate-900 dark:text-white">{{ user.name }}</p>
              <p class="text-[11px] text-slate-500 dark:text-slate-400 truncate">{{ user.email }}</p>
            </div>
            <Link
              href="/profile"
              class="flex items-center gap-2 px-4 py-2 text-xs text-slate-700 dark:text-slate-300 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-slate-900 dark:hover:text-white"
            >
              <ShieldCheck class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
              <span>Admin Profile</span>
            </Link>
            <button
              @click="logout"
              class="w-full text-left flex items-center gap-2 px-4 py-2 text-xs text-rose-600 dark:text-rose-400 hover:bg-slate-100 dark:hover:bg-slate-800 hover:text-rose-700 dark:hover:text-rose-300"
            >
              <LogOut class="w-4 h-4" />
              <span>Sign Out</span>
            </button>
          </div>
        </div>
      </div>
    </header>

    <div class="flex-1 flex">
      <!-- Sidebar Navigation -->
      <aside
        :class="[
          'bg-white dark:bg-slate-950 border-r border-slate-200 dark:border-slate-800 transition-all duration-300 flex flex-col justify-between sticky top-16 h-[calc(100vh-4rem)] z-30',
          isSidebarOpen ? 'w-64' : 'w-20'
        ]"
      >
        <div class="py-4 px-3 space-y-1">
          <Link
            v-for="item in navigation"
            :key="item.name"
            :href="item.href"
            :class="[
              'flex items-center gap-3 px-3.5 py-2.5 rounded-xl text-xs font-medium transition-all group',
              page.url.startsWith(item.href)
                ? 'bg-indigo-600 text-white shadow-lg shadow-indigo-600/20'
                : 'text-slate-600 dark:text-slate-400 hover:bg-slate-100 dark:hover:bg-slate-900 hover:text-slate-900 dark:hover:text-white'
            ]"
          >
            <component :is="item.icon" class="w-5 h-5 flex-shrink-0" />
            <span v-if="isSidebarOpen" class="truncate">{{ item.name }}</span>
          </Link>
        </div>

        <!-- Sidebar Footer / Organization Badge -->
        <div class="p-4 border-t border-slate-200 dark:border-slate-800">
          <div v-if="isSidebarOpen" class="p-3 rounded-xl bg-slate-50 dark:bg-slate-900 border border-slate-200 dark:border-slate-800">
            <div class="flex items-center gap-2 text-xs font-bold text-slate-900 dark:text-white mb-1">
              <Building2 class="w-4 h-4 text-indigo-600 dark:text-indigo-400" />
              <span>PT. Telkom Lampung</span>
            </div>
            <p class="text-[10px] text-slate-500 dark:text-slate-400">Jl. Majapahit No.1, Enggal, Bandar Lampung</p>
          </div>
        </div>
      </aside>

      <!-- Main Workspace -->
      <main class="flex-1 p-4 lg:p-8 overflow-y-auto">
        <slot />
      </main>
    </div>
  </div>
</template>
