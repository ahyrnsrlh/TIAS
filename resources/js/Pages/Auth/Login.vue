<script setup>
import { ref } from 'vue';
import { Head, Link, useForm } from '@inertiajs/vue3';
import { User, Lock, Eye, EyeOff, ShieldCheck } from 'lucide-vue-next';

defineProps({
  canResetPassword: {
    type: Boolean,
    default: true,
  },
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
  password: '',
  remember: false,
});

const showPassword = ref(false);

const submit = () => {
  form.post(route('login'), {
    onFinish: () => form.reset('password'),
  });
};
</script>

<template>
  <Head title="Login - Telkom Invoice Automation System Witel Lampung" />

  <div class="min-h-screen w-full bg-[url('/bg_login.webp')] bg-cover bg-center bg-no-repeat flex flex-col justify-between p-6 md:p-12 font-sans relative">
    <!-- Top Spacer -->
    <div></div>

    <!-- Main Content Container: Left Aligned Glassmorphism Card -->
    <div class="w-full max-w-md my-auto mx-auto lg:mx-0 lg:ml-12 xl:ml-20">
      <div class="bg-white/80 dark:bg-slate-900/80 backdrop-blur-2xl border border-white/80 dark:border-slate-800 rounded-[32px] p-8 sm:p-10 shadow-2xl shadow-slate-900/10 space-y-6">
        <!-- Logo & Greeting Header -->
        <div class="text-center space-y-3">
          <img
            src="/logo-telkom.webp"
            alt="Telkom Indonesia Logo"
            class="h-20 sm:h-24 w-auto object-contain mx-auto drop-shadow-md pb-1"
          />
          <div>
            <h1 class="text-xl font-bold text-slate-800 dark:text-white tracking-tight">Selamat Datang</h1>
            <p class="text-xs text-slate-500 dark:text-slate-400 mt-1">Silakan masuk untuk melanjutkan ke portal TIAS</p>
          </div>
        </div>

        <div v-if="status" class="text-xs font-semibold text-emerald-700 bg-emerald-50/90 p-3.5 rounded-xl border border-emerald-200">
          {{ status }}
        </div>

        <!-- Login Form -->
        <form @submit.prevent="submit" class="space-y-4">
          <!-- Email / Username Input -->
          <div>
            <div class="relative">
              <User class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" />
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                autocomplete="username"
                placeholder="Username / Email Admin"
                class="w-full pl-11 pr-4 py-3 rounded-2xl bg-white/80 dark:bg-slate-950/70 border border-slate-200/90 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#E00000] focus:ring-1 focus:ring-[#E00000] transition-all font-medium"
              />
            </div>
            <p v-if="form.errors.email" class="mt-1 text-xs text-rose-600 font-medium">
              {{ form.errors.email }}
            </p>
          </div>

          <!-- Password Input with Show/Hide Toggle -->
          <div>
            <div class="relative">
              <Lock class="w-4 h-4 text-slate-400 absolute left-4 top-3.5" />
              <input
                id="password"
                :type="showPassword ? 'text' : 'password'"
                v-model="form.password"
                required
                autocomplete="current-password"
                placeholder="Password"
                class="w-full pl-11 pr-11 py-3 rounded-2xl bg-white/80 dark:bg-slate-950/70 border border-slate-200/90 dark:border-slate-800 text-xs text-slate-900 dark:text-white placeholder-slate-400 focus:outline-none focus:border-[#E00000] focus:ring-1 focus:ring-[#E00000] transition-all font-medium"
              />
              <button
                type="button"
                @click="showPassword = !showPassword"
                class="absolute right-4 top-3.5 text-slate-400 hover:text-slate-600 dark:hover:text-slate-200 transition-colors"
                title="Toggle password visibility"
              >
                <EyeOff v-if="showPassword" class="w-4 h-4" />
                <Eye v-else class="w-4 h-4" />
              </button>
            </div>
            <p v-if="form.errors.password" class="mt-1 text-xs text-rose-600 font-medium">
              {{ form.errors.password }}
            </p>
          </div>

          <!-- Remember Me & Forgot Password Links -->
          <div class="flex items-center justify-between pt-1 text-xs">
            <label class="flex items-center gap-2 cursor-pointer">
              <input
                type="checkbox"
                v-model="form.remember"
                class="w-4 h-4 rounded border-slate-300 text-[#E00000] focus:ring-[#E00000]"
              />
              <span class="text-slate-600 dark:text-slate-300 font-medium text-[11px]">Ingat saya</span>
            </label>

            <Link
              v-if="canResetPassword"
              :href="route('password.request')"
              class="text-[11px] text-[#E00000] hover:underline font-semibold"
            >
              Lupa Password?
            </Link>
          </div>

          <!-- Primary Submit Button -->
          <button
            type="submit"
            :disabled="form.processing"
            class="w-full py-3 rounded-2xl bg-[#E00000] hover:bg-[#C00000] text-xs font-bold text-white shadow-lg shadow-[#E00000]/30 transition-all disabled:opacity-50 mt-2"
          >
            <span>Masuk</span>
          </button>
        </form>

        <!-- Separator Divider -->
        <div class="relative my-4 flex items-center justify-center">
          <div class="w-full border-t border-slate-200/80 dark:border-slate-800"></div>
          <span class="absolute bg-white/90 dark:bg-slate-900/90 px-3 text-[11px] text-slate-400 font-medium">atau</span>
        </div>

        <!-- SSO Button / Admin Authentication Portal -->
        <button
          type="button"
          @click="submit"
          class="w-full flex items-center justify-center gap-2.5 py-2.5 px-4 rounded-2xl bg-white/90 dark:bg-slate-800/90 border border-slate-200 dark:border-slate-700 hover:bg-slate-50 dark:hover:bg-slate-800 text-xs font-semibold text-slate-700 dark:text-slate-200 transition-all shadow-sm"
        >
          <img src="/logo-telkom.webp" alt="SSO Logo" class="h-4 w-auto object-contain" />
          <span>Masuk dengan SSO Telkom</span>
        </button>
      </div>
    </div>

    <!-- Bottom Footer Bar -->
    <div class="w-full flex flex-col md:flex-row items-center justify-between gap-2 text-[11px] text-slate-500 dark:text-slate-400 pt-6">
      <p>© 2026 PT Telkom Indonesia (Persero) Tbk — Witel Lampung</p>
      <p class="font-semibold text-slate-700 dark:text-slate-300">
        Invoice Automation System <span class="font-normal text-slate-500 dark:text-slate-400">| Digital Solution for Better Business</span>
      </p>
    </div>
  </div>
</template>
