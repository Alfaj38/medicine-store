<template>
    <AdminLayout>
        <template #header>Platform Settings</template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <Link :href="route('admin.settings.email')" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-400 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-blue-50 text-blue-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                    📧
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Email Configuration</h3>
                <p class="text-sm text-slate-500 font-medium">Manage SMTP settings, sender addresses, and connection security.</p>
            </Link>

            <Link :href="route('admin.settings.sms')" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-400 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-purple-50 text-purple-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                    💬
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">SMS Gateway</h3>
                <p class="text-sm text-slate-500 font-medium">Configure SMS providers, API keys, and sender IDs for OTPs and alerts.</p>
            </Link>

            <Link :href="route('admin.settings.payment-gateway')" class="bg-white p-6 rounded-2xl shadow-sm border border-slate-200 hover:border-emerald-400 hover:shadow-md transition-all group">
                <div class="w-12 h-12 rounded-xl bg-emerald-50 text-emerald-600 flex items-center justify-center text-2xl mb-4 group-hover:scale-110 transition-transform">
                    💳
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Payment Gateways</h3>
                <p class="text-sm text-slate-500 font-medium">Manage Stripe and SSLCommerz API keys, and toggle sandbox mode.</p>
            </Link>

            <!-- Placeholders for future settings -->
            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 opacity-60 cursor-not-allowed">
                <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center text-2xl mb-4">
                    🎨
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">White-Labeling</h3>
                <p class="text-sm text-slate-500 font-medium">Coming soon: Upload custom logos, change platform colors.</p>
            </div>

            <div class="bg-slate-50 p-6 rounded-2xl border border-slate-200 opacity-60 cursor-not-allowed">
                <div class="w-12 h-12 rounded-xl bg-slate-200 text-slate-500 flex items-center justify-center text-2xl mb-4">
                    🌍
                </div>
                <h3 class="text-lg font-bold text-slate-800 mb-1">Localization</h3>
                <p class="text-sm text-slate-500 font-medium">Coming soon: Set default timezones, currencies, and languages.</p>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';
</script>
