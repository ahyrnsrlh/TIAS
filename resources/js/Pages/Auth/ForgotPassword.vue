<script setup>
import { Head, Link, useForm } from '@inertiajs/vue3';
import { Mail, ArrowLeft, Send } from 'lucide-vue-next';

defineProps({
  status: {
    type: String,
  },
});

const form = useForm({
  email: '',
});

const submit = () => {
  form.post(route('password.email'));
};
</script>

<template>
  <Head title="Forgot Password - Telkom Witel Lampung" />

  <div class="min-h-screen bg-slate-50 text-slate-900 flex items-center justify-center p-4 relative overflow-hidden font-sans">
    <div class="absolute -top-32 -left-32 w-96 h-96 bg-indigo-500/10 rounded-full blur-3xl pointer-events-none"></div>

    <div class="w-full max-w-md relative z-10">
      <div class="text-center mb-8">
        <img
          src="/logo-telkom.webp"
          alt="PT. Telkom Indonesia Logo"
          class="h-14 w-auto object-contain mx-auto mb-4 drop-shadow-sm"
        />
        <h1 class="text-2xl font-extrabold text-slate-900 tracking-tight">Password Reset</h1>
        <p class="text-xs text-slate-500 mt-1">PT. Telkom Indonesia Witel Lampung</p>
      </div>

      <div class="bg-white border border-slate-200 rounded-3xl p-8 shadow-xl shadow-slate-200/60">
        <p class="text-xs text-slate-600 mb-6 leading-relaxed">
          Forgot your administrator password? Enter your corporate email address below and we will send you a password reset link.
        </p>

        <div v-if="status" class="mb-4 text-xs font-semibold text-emerald-700 bg-emerald-50 p-3.5 rounded-xl border border-emerald-200">
          {{ status }}
        </div>

        <form @submit.prevent="submit" class="space-y-5">
          <div>
            <label class="block text-xs font-semibold text-slate-700 mb-1.5">Corporate Admin Email</label>
            <div class="relative">
              <Mail class="w-4 h-4 text-slate-400 absolute left-3.5 top-3.5" />
              <input
                id="email"
                type="email"
                v-model="form.email"
                required
                autofocus
                placeholder="admin@telkom-lampung.co.id"
                class="w-full pl-10 pr-4 py-2.5 rounded-xl bg-slate-50 border border-slate-200 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-indigo-600 focus:ring-1 focus:ring-indigo-600 transition-all font-medium"
              />
            </div>
            <p v-if="form.errors.email" class="mt-1.5 text-xs text-rose-600 font-medium">
              {{ form.errors.email }}
            </p>
          </div>

          <button
            type="submit"
            :disabled="form.processing"
            class="w-full flex items-center justify-center gap-2 py-3 rounded-xl bg-indigo-600 hover:bg-indigo-700 text-xs font-bold text-white shadow-lg shadow-indigo-600/20 transition-all disabled:opacity-50"
          >
            <span>Send Reset Link</span>
            <Send class="w-4 h-4" />
          </button>
        </form>

        <div class="mt-6 pt-6 border-t border-slate-200 text-center">
          <Link
            :href="route('login')"
            class="inline-flex items-center gap-2 text-xs font-semibold text-slate-600 hover:text-slate-900 transition-colors"
          >
            <ArrowLeft class="w-4 h-4" />
            <span>Back to Login</span>
          </Link>
        </div>
      </div>
    </div>
  </div>
</template>
