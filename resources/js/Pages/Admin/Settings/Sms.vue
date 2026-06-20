<template>
    <AdminLayout>
        <template #header>SMS Gateway Configuration</template>

        <div class="max-w-3xl">
            <div class="mb-6 flex items-center gap-3">
                <Link :href="route('admin.settings.index')" class="text-slate-400 hover:text-slate-600 font-medium text-sm flex items-center gap-1">
                    ← Back to Settings
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-lg font-bold text-slate-800">SMS API Settings</h2>
                    <p class="text-sm text-slate-500 mt-1">Configure your SMS provider for sending OTPs and transactional notifications to tenants.</p>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-5">
                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">SMS Provider</label>
                        <select v-model="form.sms_provider" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            <option value="twilio">Twilio</option>
                            <option value="vonage">Vonage (Nexmo)</option>
                            <option value="sslwireless">SSL Wireless (Bangladesh)</option>
                            <option value="bulksmsbd">BulkSMSBD</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">API Key / Auth Token</label>
                        <input v-model="form.sms_api_key" type="password" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Enter API Key">
                    </div>

                    <div>
                        <label class="block text-sm font-bold text-slate-700 mb-1.5">Sender ID / Masking Name</label>
                        <input v-model="form.sms_sender_id" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="e.g. MEDISAAS">
                        <p class="text-xs text-slate-500 mt-1.5">Must be approved by your SMS provider if using masking.</p>
                    </div>

                    <div class="pt-6 flex justify-end gap-3">
                        <button type="button" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                            Send Test SMS
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-colors shadow-sm" :disabled="form.processing">
                            Save Configuration
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
    sms_provider: props.settings.sms_provider || 'twilio',
    sms_api_key: props.settings.sms_api_key || '',
    sms_sender_id: props.settings.sms_sender_id || '',
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>
