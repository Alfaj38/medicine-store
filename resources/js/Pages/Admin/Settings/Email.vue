<template>
    <AdminLayout>
        <template #header>Email Configuration</template>

        <div class="max-w-3xl">
            <div class="mb-6 flex items-center gap-3">
                <Link :href="route('admin.settings.index')" class="text-slate-400 hover:text-slate-600 font-medium text-sm flex items-center gap-1">
                    ← Back to Settings
                </Link>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50">
                    <h2 class="text-lg font-bold text-slate-800">SMTP Server Details</h2>
                    <p class="text-sm text-slate-500 mt-1">These settings will override the default .env configuration for all outgoing platform emails.</p>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-5">
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Mail Host</label>
                            <input v-model="form.mail_host" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="smtp.mailtrap.io">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Mail Port</label>
                            <input v-model="form.mail_port" type="number" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="2525">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Mail Username</label>
                            <input v-model="form.mail_username" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="Username">
                        </div>
                        <div class="col-span-2">
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Mail Password</label>
                            <input v-model="form.mail_password" type="password" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="••••••••">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">Encryption</label>
                            <select v-model="form.mail_encryption" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <option value="tls">TLS</option>
                                <option value="ssl">SSL</option>
                                <option value="">None</option>
                            </select>
                        </div>
                    </div>

                    <hr class="border-slate-100 my-6">

                    <h3 class="text-md font-bold text-slate-800 mb-4">Sender Details</h3>
                    <div class="grid grid-cols-2 gap-5">
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">From Address</label>
                            <input v-model="form.mail_from_address" type="email" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="noreply@medisaas.com">
                        </div>
                        <div>
                            <label class="block text-sm font-bold text-slate-700 mb-1.5">From Name</label>
                            <input v-model="form.mail_from_name" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" placeholder="MediSaaS Platform">
                        </div>
                    </div>

                    <div class="pt-6 flex justify-end gap-3">
                        <button type="button" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 bg-slate-100 hover:bg-slate-200 transition-colors">
                            Send Test Email
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
    mail_host: props.settings.mail_host || '',
    mail_port: props.settings.mail_port || '',
    mail_username: props.settings.mail_username || '',
    mail_password: props.settings.mail_password || '',
    mail_encryption: props.settings.mail_encryption || 'tls',
    mail_from_address: props.settings.mail_from_address || '',
    mail_from_name: props.settings.mail_from_name || '',
});

const submit = () => {
    form.post(route('admin.settings.update'), {
        preserveScroll: true,
    });
};
</script>
