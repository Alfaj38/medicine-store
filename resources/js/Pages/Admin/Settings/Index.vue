<template>
    <Head title="Platform Settings" />

    <AdminLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                Platform Settings
            </h2>
        </template>

        <div class="py-12">
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                    
                    <!-- Email Settings -->
                    <Link :href="route('admin.settings.email')" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all group cursor-pointer">
                        <div class="p-6">
                            <div class="w-14 h-14 rounded-full bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                📧
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">Email Configuration</h3>
                            <p class="text-sm text-slate-500 font-medium">Configure SMTP details, sender address, and email templates for platform notifications.</p>
                        </div>
                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between items-center text-sm font-bold text-slate-600 group-hover:text-emerald-600">
                            <span>Manage Settings</span>
                            <span>→</span>
                        </div>
                    </Link>

                    <!-- SMS Settings -->
                    <Link :href="route('admin.settings.sms')" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all group cursor-pointer">
                        <div class="p-6">
                            <div class="w-14 h-14 rounded-full bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                📱
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">SMS Configuration</h3>
                            <p class="text-sm text-slate-500 font-medium">Set up SMS API credentials, sender IDs, and gateway routing for critical alerts.</p>
                        </div>
                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between items-center text-sm font-bold text-slate-600 group-hover:text-emerald-600">
                            <span>Manage Settings</span>
                            <span>→</span>
                        </div>
                    </Link>

                    <!-- Payment Gateway Settings -->
                    <Link :href="route('admin.settings.payment_gateway')" class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 hover:border-emerald-300 hover:shadow-md transition-all group cursor-pointer">
                        <div class="p-6">
                            <div class="w-14 h-14 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                                💳
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">Payment Gateways</h3>
                            <p class="text-sm text-slate-500 font-medium">Configure Stripe, SSLCommerz, and other payment processors for subscription billing.</p>
                        </div>
                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between items-center text-sm font-bold text-slate-600 group-hover:text-emerald-600">
                            <span>Manage Settings</span>
                            <span>→</span>
                        </div>
                    </Link>

                    <!-- General Settings (Future) -->
                    <div class="bg-white overflow-hidden shadow-sm rounded-2xl border border-slate-200 opacity-60">
                        <div class="p-6">
                            <div class="w-14 h-14 rounded-full bg-slate-100 text-slate-400 flex items-center justify-center text-2xl mb-4">
                                ⚙️
                            </div>
                            <h3 class="text-lg font-bold text-slate-800 mb-2">General Settings</h3>
                            <p class="text-sm text-slate-500 font-medium">Update platform name, logos, default timezones, and global preferences.</p>
                        </div>
                        <div class="bg-slate-50 px-6 py-3 border-t border-slate-100 flex justify-between items-center text-sm font-bold text-slate-400">
                            <span>Coming Soon</span>
                        </div>
                    </div>

                </div>

            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Head, Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';
</script>
