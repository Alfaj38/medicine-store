<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import Layout from '../Settings/Layout.vue';

const props = defineProps({
    branches: Array
});

const isModalOpen = ref(false);
const editingBranch = ref(null);

const form = useForm({
    name: '',
    phone: '',
    address: '',
    is_default: false,
    is_active: true
});

const openCreateModal = () => {
    editingBranch.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (branch) => {
    editingBranch.value = branch;
    form.name = branch.name;
    form.phone = branch.phone;
    form.address = branch.address;
    form.is_default = branch.is_default;
    form.is_active = branch.is_active;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (editingBranch.value) {
        form.put(route('company.branches.update', editingBranch.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('company.branches.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteBranch = (branch) => {
    if (confirm(`Are you sure you want to delete ${branch.name}?`)) {
        router.delete(route('company.branches.destroy', branch.id));
    }
};
</script>

<template>
    <Layout title="Branches">
        <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-4 py-5 border-b border-slate-200 sm:px-6 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-slate-900">Branches</h3>
                    <p class="mt-1 text-sm text-slate-500">Manage your company's branch locations.</p>
                </div>
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                    Add Branch
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Branch Details</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employees</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <tr v-for="branch in branches" :key="branch.id">
                            <td class="px-6 py-4">
                                <div class="text-sm font-medium text-slate-900">
                                    {{ branch.name }}
                                    <span v-if="branch.is_default" class="ml-2 inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Primary
                                    </span>
                                </div>
                                <div class="text-sm text-slate-500">{{ branch.address }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ branch.phone }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="branch.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'">
                                    {{ branch.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ branch.users_count || 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(branch)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                <button @click="deleteBranch(branch)" class="text-red-600 hover:text-red-900" :disabled="branch.users_count > 0" :class="{'opacity-50 cursor-not-allowed': branch.users_count > 0}">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="branches.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No branches found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitForm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4" id="modal-title">
                                {{ editingBranch ? 'Edit Branch' : 'Add New Branch' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Branch Name</label>
                                    <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Phone</label>
                                    <input v-model="form.phone" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Address</label>
                                    <textarea v-model="form.address" rows="3" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                                    <p v-if="form.errors.address" class="mt-1 text-sm text-red-500">{{ form.errors.address }}</p>
                                </div>

                                <div class="flex items-center gap-4 pt-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="form.is_default" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span class="ml-2 text-sm text-slate-700">Primary Branch</span>
                                    </label>
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="form.is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span class="ml-2 text-sm text-slate-700">Active</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-200">
                            <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Save
                            </button>
                            <button type="button" @click="closeModal" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>
