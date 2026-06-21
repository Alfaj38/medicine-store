<script setup>
import { Head, useForm, Link } from '@inertiajs/vue3';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const submit = () => {
    form.post(route('reseller.login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Partner Login - MediSaaS" />
    
    <div class="min-h-screen bg-slate-900 flex items-center justify-center p-4 relative overflow-hidden">
        <!-- Background Pattern -->
        <div class="absolute inset-0 bg-[url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSIyMCIgaGVpZ2h0PSIyMCI+CgkJPGNpcmNsZSBjeD0iMiIgY3k9IjIiIHI9IjIiIGZpbGw9IiMzMzQxNTUiIGZpbGwtb3BhY2l0eT0iMC40Ii8+Cgk8L3N2Zz4=')] opacity-20"></div>

        <div class="w-full max-w-[450px] relative z-10">
            <div class="text-center mb-8">
                <Link :href="route('home')" class="inline-flex items-center justify-center w-12 h-12 bg-emerald-500 rounded-xl mb-6 shadow-lg shadow-emerald-500/20">
                    <span class="text-white font-black text-2xl">+</span>
                </Link>
                <h1 class="text-3xl font-black text-white">Partner Portal</h1>
                <p class="text-emerald-400 mt-2 font-medium">Log in to manage your referrals</p>
            </div>

            <div class="bg-white rounded-3xl shadow-2xl p-8 md:p-10">
                <form @submit.prevent="submit" class="space-y-6">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Email Address</label>
                        <input v-model="form.email" type="email" required autofocus class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50">
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5 font-semibold">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <div class="flex items-center justify-between mb-1.5">
                            <label class="block text-sm font-bold text-slate-700">Password</label>
                            <a href="#" class="text-xs font-bold text-emerald-600 hover:underline">Forgot password?</a>
                        </div>
                        <input v-model="form.password" type="password" required class="w-full rounded-xl border border-slate-300 px-4 py-3 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 bg-slate-50">
                    </div>

                    <div class="flex items-center">
                        <label class="flex items-center cursor-pointer">
                            <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 cursor-pointer">
                            <span class="ml-2 text-sm font-medium text-slate-600">Remember me</span>
                        </label>
                    </div>

                    <button type="submit" :disabled="form.processing" class="w-full py-3.5 bg-emerald-600 hover:bg-emerald-500 text-white rounded-xl font-bold shadow-lg shadow-emerald-500/30 transition-all disabled:opacity-70 flex justify-center items-center">
                        <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                        Sign In to Dashboard
                    </button>
                </form>

                <div class="mt-8 pt-6 border-t border-slate-100 text-center">
                    <p class="text-sm text-slate-500">Not a partner yet? <Link :href="route('partner')" class="font-bold text-slate-900 hover:underline">Apply now</Link></p>
                </div>
            </div>
        </div>
    </div>
</template>
