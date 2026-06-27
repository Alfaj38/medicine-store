<template>
    <AdminLayout>
        <template #header>Subscription Packages</template>

        <div class="grid grid-cols-1 xl:grid-cols-2 gap-6">
            <div v-for="pkg in packages" :key="pkg.id" class="bg-white rounded-2xl shadow-sm border overflow-hidden flex flex-col" :class="pkg.is_active ? 'border-emerald-200' : 'border-slate-200 opacity-75'">
                
                <div class="p-6 border-b" :class="pkg.is_active ? 'bg-emerald-50/50 border-emerald-100' : 'bg-slate-50 border-slate-100'">
                    <div class="flex justify-between items-start mb-2">
                        <div>
                            <h3 class="text-xl font-bold text-slate-800">{{ pkg.name }}</h3>
                            <div class="text-sm font-medium text-slate-500 mt-1">
                                <span class="font-bold text-slate-700">৳{{ pkg.monthly_price }}</span> / mo &nbsp;&bull;&nbsp;
                                <span class="font-bold text-slate-700">৳{{ pkg.yearly_price }}</span> / yr
                            </div>
                        </div>
                        <span v-if="pkg.is_active" class="bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Active</span>
                        <span v-else class="bg-slate-200 text-slate-600 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Inactive</span>
                    </div>
                </div>

                <div class="p-6 flex-1 bg-white">
                    <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Package Features</h4>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-3 max-h-60 overflow-y-auto pr-2">
                        <div v-for="feature in pkg.features" :key="feature.id" class="flex justify-between items-center py-2 border-b border-slate-50 last:border-0">
                            <span class="text-xs font-semibold text-slate-600 capitalize">
                                {{ feature.feature_code.replace(/_/g, ' ') }}
                            </span>
                            
                            <template v-if="feature.limit !== null">
                                <span class="text-xs font-bold text-slate-800 bg-slate-100 px-2 py-0.5 rounded">{{ feature.limit }}</span>
                            </template>
                            <template v-else>
                                <span v-if="feature.is_enabled" class="text-emerald-500 font-bold text-sm">✓</span>
                                <span v-else class="text-slate-300 font-bold text-sm">✕</span>
                            </template>
                        </div>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100 mt-auto">
                    <button @click="openEditModal(pkg)" class="w-full py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl font-bold text-sm transition-colors shadow-sm">
                        Edit Package
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="selectedPackage" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl max-w-3xl w-full border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-slate-800">Edit {{ selectedPackage.name }}</h3>
                    <button @click="selectedPackage = null" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">✕</button>
                </div>
                
                <form @submit.prevent="submitEdit" class="flex flex-col overflow-hidden">
                    <div class="p-6 overflow-y-auto flex-1">
                        
                        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 p-4 rounded-xl mb-6">
                            <input v-model="form.is_active" type="checkbox" id="pkg_active" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 h-5 w-5">
                            <label for="pkg_active" class="font-bold text-emerald-800 cursor-pointer">Package is Active</label>
                        </div>

                        <div class="grid grid-cols-1 md:grid-cols-3 gap-5 mb-6 pb-6 border-b border-slate-100">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Package Name</label>
                                <input v-model="form.name" type="text" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Monthly Price (৳)</label>
                                <input v-model="form.monthly_price" type="number" step="0.01" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Yearly Price (৳)</label>
                                <input v-model="form.yearly_price" type="number" step="0.01" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                        </div>

                        <h4 class="text-sm font-bold text-slate-800 mb-4">Package Features</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
                            <div v-for="(feature, index) in form.features" :key="index" class="p-3 border border-slate-200 rounded-xl bg-slate-50 flex flex-col justify-between">
                                <div class="flex items-start justify-between mb-2">
                                    <span class="text-xs font-bold text-slate-700 capitalize">{{ feature.feature_code.replace(/_/g, ' ') }}</span>
                                    <input v-model="feature.is_enabled" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                </div>
                                <div class="mt-2" v-if="feature.is_enabled">
                                    <label class="block text-[10px] font-bold text-slate-500 uppercase mb-1">Limit (Empty = Unlimited)</label>
                                    <input v-model="feature.limit" type="number" placeholder="Unlimited" class="w-full rounded-lg border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-xs py-1.5">
                                </div>
                            </div>
                        </div>

                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-slate-50 shrink-0 flex justify-end gap-3">
                        <button type="button" @click="selectedPackage = null" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm flex items-center gap-2" :disabled="form.processing">
                            <svg v-if="form.processing" class="animate-spin h-4 w-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                            Save Changes
                        </button>
                    </div>
                </form>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { useForm } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

const props = defineProps({
    packages: Array
});

const selectedPackage = ref(null);

const form = useForm({
    name: '',
    monthly_price: 0,
    yearly_price: 0,
    is_active: true,
    features: []
});

const openEditModal = (pkg) => {
    selectedPackage.value = pkg;
    form.name = pkg.name;
    form.monthly_price = pkg.monthly_price;
    form.yearly_price = pkg.yearly_price;
    form.is_active = pkg.is_active;
    
    // Deep copy features so v-model doesn't mutate props directly before saving
    form.features = pkg.features.map(f => ({
        feature_code: f.feature_code,
        is_enabled: !!f.is_enabled,
        limit: f.limit
    }));
};

const submitEdit = () => {
    // Transform empty string limits to null for the backend
    const submissionData = {
        ...form,
        features: form.features.map(f => ({
            ...f,
            limit: f.limit === '' ? null : f.limit
        }))
    };

    form.transform((data) => submissionData)
        .post(route('admin.packages.update', selectedPackage.value.id), {
            preserveScroll: true,
            onSuccess: () => {
                selectedPackage.value = null;
            }
        });
};
</script>
