<template>
    <AdminLayout>
        <template #header>{{ title }}</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
            <div class="text-5xl mb-4">🚧</div>
            <h2 class="text-2xl font-bold text-slate-800 mb-2">{{ title }}</h2>
            <p class="text-slate-500 font-medium max-w-md mx-auto">
                This module is currently under construction and will be available in the next platform update.
            </p>
            <div class="mt-8">
                <Link :href="route('admin.dashboard')" class="px-6 py-2.5 bg-emerald-600 text-white rounded-xl font-bold hover:bg-emerald-700 transition-colors">
                    Return to Dashboard
                </Link>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { Link } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';

defineProps({
    title: {
        type: String,
        default: 'Module Under Construction'
    }
});
</script>
