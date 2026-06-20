<template>
    <AdminLayout>
        <template #header>System Monitoring Center</template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            
            <!-- Database Health -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
                    :class="metrics.db_status === 'healthy' ? 'bg-emerald-100 text-emerald-600' : 'bg-red-100 text-red-600'">
                    🗄️
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Database Connection</p>
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        {{ metrics.db_status === 'healthy' ? 'Healthy & Connected' : 'Connection Error' }}
                        <span class="w-2.5 h-2.5 rounded-full" :class="metrics.db_status === 'healthy' ? 'bg-emerald-500' : 'bg-red-500'"></span>
                    </h3>
                </div>
            </div>

            <!-- Cache Health -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl"
                    :class="metrics.cache_status === 'healthy' ? 'bg-emerald-100 text-emerald-600' : 'bg-amber-100 text-amber-600'">
                    ⚡
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Redis / Cache Engine</p>
                    <h3 class="text-lg font-black text-slate-800 flex items-center gap-2">
                        {{ metrics.cache_status === 'healthy' ? 'Operational' : 'Degraded' }}
                        <span class="w-2.5 h-2.5 rounded-full" :class="metrics.cache_status === 'healthy' ? 'bg-emerald-500' : 'bg-amber-500'"></span>
                    </h3>
                </div>
            </div>

            <!-- Disk Space -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl bg-blue-100 text-blue-600">
                    💾
                </div>
                <div>
                    <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Disk Free Space</p>
                    <h3 class="text-lg font-black text-slate-800">{{ metrics.disk_free_gb }} GB</h3>
                </div>
            </div>

            <!-- Background Jobs Queue -->
            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 flex items-center gap-4 lg:col-span-2">
                <div class="w-12 h-12 rounded-full flex items-center justify-center text-2xl bg-indigo-100 text-indigo-600">
                    ⚙️
                </div>
                <div class="flex-1 flex justify-between items-center">
                    <div>
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Background Queue Workers</p>
                        <h3 class="text-lg font-black text-slate-800">{{ metrics.queue_jobs }} Pending Jobs</h3>
                    </div>
                    <div class="text-right">
                        <p class="text-xs font-bold text-slate-500 uppercase tracking-wider mb-1">Failed Jobs</p>
                        <h3 class="text-lg font-black" :class="metrics.failed_jobs > 0 ? 'text-red-600' : 'text-emerald-600'">{{ metrics.failed_jobs }} Failed</h3>
                    </div>
                </div>
            </div>

        </div>
    </AdminLayout>
</template>

<script setup>
import AdminLayout from '../Layout.vue';

defineProps({
    metrics: Object,
});
</script>
