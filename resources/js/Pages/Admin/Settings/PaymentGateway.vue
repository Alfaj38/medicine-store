<template>
    <AdminLayout>
        <template #header>Payment Gateway Configuration</template>

        <div class="max-w-3xl">
            <div class="mb-6 flex items-center gap-3">
                <Link :href="route('admin.settings.index')" class="text-slate-400 hover:text-slate-600 font-medium text-sm flex items-center gap-1">
                    ← Back to Settings
                </Link>
            </div>

            <!-- Stripe Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-[#635BFF]/10 flex items-center justify-center text-[#635BFF] font-bold text-lg shrink-0">S</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">Stripe Integration</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Used for international recurring SaaS subscriptions.</p>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Publishable Key</label>
                        <input v-model="form.stripe_key" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono" placeholder="pk_test_...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Secret Key</label>
                        <input v-model="form.stripe_secret" type="password" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono" placeholder="sk_test_...">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Webhook Secret</label>
                        <input v-model="form.stripe_webhook_secret" type="password" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono" placeholder="whsec_...">
                        <p class="text-xs text-slate-500 mt-1.5">Required for automatically fulfilling successful subscription renewals.</p>
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm" :disabled="form.processing">
                            Save Stripe Settings
                        </button>
                    </div>
                </form>
            </div>

            <!-- SSLCommerz Settings -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50 flex items-center gap-3">
                    <div class="w-8 h-8 rounded-lg bg-orange-100 flex items-center justify-center text-orange-600 font-bold text-lg shrink-0">SSL</div>
                    <div>
                        <h2 class="text-lg font-bold text-slate-800">SSLCommerz Integration</h2>
                        <p class="text-sm text-slate-500 mt-0.5">Used for domestic Bangladeshi mobile banking & cards.</p>
                    </div>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-5">
                    
                    <div class="flex items-center gap-3 bg-orange-50 border border-orange-200 p-4 rounded-xl">
                        <input v-model="form.sslcommerz_sandbox" type="checkbox" id="ssl_sandbox" class="rounded border-orange-300 text-orange-600 focus:ring-orange-500 h-5 w-5">
                        <label for="ssl_sandbox" class="font-bold text-orange-800 cursor-pointer">Enable Sandbox (Test Mode)</label>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Store ID</label>
                        <input v-model="form.sslcommerz_store_id" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono" placeholder="Enter Store ID">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Store Password</label>
                        <input v-model="form.sslcommerz_store_password" type="password" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono" placeholder="Enter Store Password">
                    </div>

                    <div class="pt-2 flex justify-end">
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm" :disabled="form.processing">
                            Save SSLCommerz Settings
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

const props = defineProps({
    settings: Object
});

const form = useForm({
    stripe_key: props.settings.stripe_key || '',
    stripe_secret: props.settings.stripe_secret || '',
    stripe_webhook_secret: props.settings.stripe_webhook_secret || '',
    
    sslcommerz_store_id: props.settings.sslcommerz_store_id || '',
    sslcommerz_store_password: props.settings.sslcommerz_store_password || '',
    sslcommerz_sandbox: props.settings.sslcommerz_sandbox === 'true' || props.settings.sslcommerz_sandbox === true,
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>
