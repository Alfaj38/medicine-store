<template>
    <AdminLayout>
        <template #header>Subscription Packages</template>

        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
            <div v-for="pkg in packages" :key="pkg.id" class="bg-white rounded-2xl shadow-sm border overflow-hidden flex flex-col" :class="pkg.is_active ? 'border-emerald-200' : 'border-slate-200 opacity-75'">
                
                <div class="p-6 border-b" :class="pkg.is_active ? 'bg-emerald-50/50 border-emerald-100' : 'bg-slate-50 border-slate-100'">
                    <div class="flex justify-between items-start mb-2">
                        <h3 class="text-xl font-bold text-slate-800">{{ pkg.name }}</h3>
                        <span v-if="pkg.is_active" class="bg-emerald-100 text-emerald-700 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Active</span>
                        <span v-else class="bg-slate-200 text-slate-600 text-[10px] font-bold uppercase tracking-wider px-2 py-0.5 rounded">Inactive</span>
                    </div>
                    <p class="text-sm text-slate-500 font-medium">{{ pkg.description }}</p>
                </div>

                <div class="p-6 flex-1 space-y-4">
                    <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-0">
                        <span class="text-sm font-bold text-slate-600">Max Branches</span>
                        <span class="text-sm font-bold text-slate-800 bg-slate-100 px-2.5 py-0.5 rounded">{{ pkg.max_branches }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-0">
                        <span class="text-sm font-bold text-slate-600">Max Users</span>
                        <span class="text-sm font-bold text-slate-800 bg-slate-100 px-2.5 py-0.5 rounded">{{ pkg.max_users }}</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-0">
                        <span class="text-sm font-bold text-slate-600">Central Reporting</span>
                        <span v-if="pkg.has_central_reporting" class="text-emerald-500 font-bold">✓</span>
                        <span v-else class="text-slate-300 font-bold">✕</span>
                    </div>
                    <div class="flex justify-between items-center py-2 border-b border-slate-100 last:border-0">
                        <span class="text-sm font-bold text-slate-600">Branch Transfers</span>
                        <span v-if="pkg.has_branch_transfer" class="text-emerald-500 font-bold">✓</span>
                        <span v-else class="text-slate-300 font-bold">✕</span>
                    </div>
                </div>

                <div class="p-4 bg-slate-50 border-t border-slate-100">
                    <button @click="openEditModal(pkg)" class="w-full py-2.5 bg-white border border-slate-200 text-slate-700 hover:bg-slate-100 rounded-xl font-bold text-sm transition-colors shadow-sm">
                        Edit Package Limits
                    </button>
                </div>
            </div>
        </div>

        <!-- Edit Modal -->
        <div v-if="selectedPackage" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl max-w-md w-full border border-slate-200 overflow-hidden flex flex-col">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
                    <h3 class="text-lg font-bold text-slate-800">Edit {{ selectedPackage.name }}</h3>
                    <button @click="selectedPackage = null" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">✕</button>
                </div>
                
                <form @submit.prevent="submitEdit">
                    <div class="p-6 space-y-5 overflow-y-auto max-h-[70vh]">
                        
                        <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-100 p-4 rounded-xl">
                            <input v-model="form.is_active" type="checkbox" id="pkg_active" class="rounded border-emerald-300 text-emerald-600 focus:ring-emerald-500 h-5 w-5">
                            <label for="pkg_active" class="font-bold text-emerald-800 cursor-pointer">Package is Active</label>
                        </div>

                        <div class="grid grid-cols-2 gap-5">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Max Branches</label>
                                <input v-model="form.max_branches" type="number" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-1.5">Max Users</label>
                                <input v-model="form.max_users" type="number" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                            </div>
                        </div>

                        <div class="space-y-3 pt-2">
                            <h4 class="text-xs font-bold text-slate-400 uppercase tracking-wider">Features</h4>
                            
                            <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input v-model="form.has_central_reporting" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-sm font-bold text-slate-700">Central Reporting</span>
                            </label>

                            <label class="flex items-center gap-3 p-3 border border-slate-200 rounded-xl cursor-pointer hover:bg-slate-50 transition-colors">
                                <input v-model="form.has_branch_transfer" type="checkbox" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                <span class="text-sm font-bold text-slate-700">Branch Transfers</span>
                            </label>
                        </div>
                    </div>
                    
                    <div class="p-4 border-t border-slate-100 bg-slate-50 shrink-0 flex justify-end gap-3">
                        <button type="button" @click="selectedPackage = null" class="px-5 py-2.5 rounded-xl font-bold text-sm text-slate-700 bg-white border border-slate-200 hover:bg-slate-100 transition-colors shadow-sm">
                            Cancel
                        </button>
                        <button type="submit" class="px-5 py-2.5 rounded-xl font-bold text-sm text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm" :disabled="form.processing">
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
import { useForm, router } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

const props = defineProps({
    packages: Array
});

const selectedPackage = ref(null);

const form = useForm({
    is_active: true,
    max_branches: 0,
    max_users: 0,
    has_central_reporting: false,
    has_branch_transfer: false,
});

const openEditModal = (pkg) => {
    selectedPackage.value = pkg;
    form.is_active = pkg.is_active;
    form.max_branches = pkg.max_branches;
    form.max_users = pkg.max_users;
    form.has_central_reporting = pkg.has_central_reporting;
    form.has_branch_transfer = pkg.has_branch_transfer;
};

const submitEdit = () => {
    form.post(route('admin.packages.update', selectedPackage.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            selectedPackage.value = null;
        }
    });
};
</script>
