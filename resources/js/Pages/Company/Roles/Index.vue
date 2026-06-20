<script setup>
import { ref } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import Layout from '../Settings/Layout.vue';

const props = defineProps({
    roles: Array
});

const isModalOpen = ref(false);
const editingRole = ref(null);

const form = useForm({
    name: ''
});

const openCreateModal = () => {
    editingRole.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (role) => {
    editingRole.value = role;
    form.name = role.name;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (editingRole.value) {
        form.put(route('company.roles.update', editingRole.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('company.roles.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteRole = (role) => {
    if (confirm(`Are you sure you want to delete the ${role.name} role?`)) {
        router.delete(route('company.roles.destroy', role.id));
    }
};
</script>

<template>
    <Layout title="Roles & Permissions">
        <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-4 py-5 border-b border-slate-200 sm:px-6 flex justify-between items-center bg-white">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-slate-900">Roles & Permissions</h3>
                    <p class="mt-1 text-sm text-slate-500">Manage user roles and their access levels.</p>
                </div>
                <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                    Add Role
                </button>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role Name</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Active Employees</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <tr v-for="role in roles" :key="role.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-slate-900">{{ role.name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                {{ role.users_count || 0 }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(role)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit Name</button>
                                <button @click="deleteRole(role)" class="text-red-600 hover:text-red-900" :disabled="role.users_count > 0" :class="{'opacity-50 cursor-not-allowed': role.users_count > 0}">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="roles.length === 0">
                            <td colspan="3" class="px-6 py-12 text-center text-slate-500">
                                No roles found.
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

                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitForm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4" id="modal-title">
                                {{ editingRole ? 'Edit Role' : 'Add New Role' }}
                            </h3>
                            
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Role Name</label>
                                    <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
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
