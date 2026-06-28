<script setup>
import { ref } from 'vue';
import { useForm, router } from '@inertiajs/vue3';
import debounce from 'lodash/debounce';
import Layout from '../Settings/Layout.vue';

const props = defineProps({
    users: Object,
    branches: Array,
    filters: Object
});

const isModalOpen = ref(false);
const editingUser = ref(null);

const form = useForm({
    name: '',
    email: '',
    phone: '',
    password: '',
    role: 'Pharmacist',
    branch_id: '',
    is_active: true
});

const search = ref(props.filters.search || '');

const openCreateModal = () => {
    editingUser.value = null;
    form.reset();
    isModalOpen.value = true;
};

const openEditModal = (user) => {
    editingUser.value = user;
    form.name = user.name;
    form.email = user.email;
    form.phone = user.phone;
    form.password = '';
    form.role = user.roles && user.roles.length ? user.roles[0].name : 'Pharmacist';
    form.branch_id = user.branch_id || '';
    form.is_active = user.is_active;
    isModalOpen.value = true;
};

const closeModal = () => {
    isModalOpen.value = false;
    form.reset();
    form.clearErrors();
};

const submitForm = () => {
    if (editingUser.value) {
        form.put(route('company.users.update', editingUser.value.id), {
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('company.users.store'), {
            onSuccess: () => closeModal(),
        });
    }
};

const deleteUser = (user) => {
    if (confirm(`Are you sure you want to remove ${user.name}?`)) {
        router.delete(route('company.users.destroy', user.id));
    }
};

const performSearch = debounce(() => {
    router.get(route('company.users.index'), { search: search.value }, { preserveState: true, replace: true });
}, 300);
</script>

<template>
    <Layout title="Employees">
        <div class="bg-white shadow-sm border border-slate-200 rounded-2xl overflow-hidden">
            <div class="px-4 py-5 border-b border-slate-200 sm:px-6 flex flex-col sm:flex-row justify-between items-start sm:items-center gap-4 bg-white">
                <div>
                    <h3 class="text-lg leading-6 font-medium text-slate-900">Employees</h3>
                    <p class="mt-1 text-sm text-slate-500">Manage staff access and branch assignments.</p>
                </div>
                <div class="flex items-center gap-4 w-full sm:w-auto">
                    <input v-model="search" @input="performSearch" type="text" placeholder="Search employees..." class="block w-full sm:w-64 rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                    <button @click="openCreateModal" class="flex-shrink-0 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                        Add Employee
                    </button>
                </div>
            </div>
            
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Employee</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Contact</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Role & Branch</th>
                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Status</th>
                            <th scope="col" class="relative px-6 py-3"><span class="sr-only">Actions</span></th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-200">
                        <tr v-for="user in users.data" :key="user.id">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="flex items-center">
                                    <div class="h-10 w-10 flex-shrink-0 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-700 font-bold">
                                        {{ user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="ml-4">
                                        <div class="text-sm font-medium text-slate-900">{{ user.name }}</div>
                                        <div v-if="user.is_company_owner" class="text-xs text-indigo-600 font-semibold">Owner</div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-slate-500">
                                <div>{{ user.email }}</div>
                                <div>{{ user.phone }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm text-slate-900">{{ user.roles && user.roles.length ? user.roles[0].name : 'User' }}</div>
                                <div class="text-sm text-slate-500">{{ user.branch ? user.branch.name : 'Unassigned' }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="px-2 inline-flex text-xs leading-5 font-semibold rounded-full" :class="user.is_active ? 'bg-emerald-100 text-emerald-800' : 'bg-red-100 text-red-800'">
                                    {{ user.is_active ? 'Active' : 'Inactive' }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <button @click="openEditModal(user)" class="text-indigo-600 hover:text-indigo-900 mr-4">Edit</button>
                                <button v-if="!user.is_company_owner && user.id !== $page.props.auth.user.id" @click="deleteUser(user)" class="text-red-600 hover:text-red-900">Delete</button>
                            </td>
                        </tr>
                        <tr v-if="users.data.length === 0">
                            <td colspan="5" class="px-6 py-12 text-center text-slate-500">
                                No employees found.
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
            
            <div class="px-4 py-3 border-t border-slate-200 bg-slate-50 flex items-center justify-between sm:px-6" v-if="users.links && users.links.length > 3">
                <div class="hidden sm:flex-1 sm:flex sm:items-center sm:justify-between">
                    <div>
                        <p class="text-sm text-slate-700">
                            Showing <span class="font-medium">{{ users.from }}</span> to <span class="font-medium">{{ users.to }}</span> of <span class="font-medium">{{ users.total }}</span> results
                        </p>
                    </div>
                    <div>
                        <nav class="relative z-0 inline-flex rounded-md shadow-sm -space-x-px" aria-label="Pagination">
                            <Link v-for="(link, i) in users.links" :key="i" :href="link.url" v-html="link.label" class="relative inline-flex items-center px-4 py-2 border text-sm font-medium" :class="[link.active ? 'z-10 bg-emerald-50 border-emerald-500 text-emerald-600' : 'bg-white border-slate-300 text-slate-500 hover:bg-slate-50', !link.url ? 'opacity-50 cursor-not-allowed' : '']" />
                        </nav>
                    </div>
                </div>
            </div>
        </div>

        <!-- Modal -->
        <div v-if="isModalOpen" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="closeModal"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-xl sm:w-full">
                    <form @submit.prevent="submitForm">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4" id="modal-title">
                                {{ editingUser ? 'Edit Employee' : 'Add New Employee' }}
                            </h3>
                            
                            <div class="grid grid-cols-1 sm:grid-cols-2 gap-4">
                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Full Name</label>
                                    <input v-model="form.name" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.name" class="mt-1 text-sm text-red-500">{{ form.errors.name }}</p>
                                </div>
                                
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Email Address</label>
                                    <input v-model="form.email" type="email" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.email" class="mt-1 text-sm text-red-500">{{ form.errors.email }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Phone</label>
                                    <input v-model="form.phone" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.phone" class="mt-1 text-sm text-red-500">{{ form.errors.phone }}</p>
                                </div>

                                <div class="sm:col-span-2">
                                    <label class="block text-sm font-medium text-slate-700">Password <span v-if="editingUser" class="text-slate-400 font-normal">(Leave blank to keep current)</span></label>
                                    <input v-model="form.password" type="password" :required="!editingUser" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                    <p v-if="form.errors.password" class="mt-1 text-sm text-red-500">{{ form.errors.password }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Role</label>
                                    <select v-model="form.role" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="Manager">Manager</option>
                                        <option value="Pharmacist">Pharmacist</option>
                                        <option value="Cashier">Cashier</option>
                                    </select>
                                    <p v-if="form.errors.role" class="mt-1 text-sm text-red-500">{{ form.errors.role }}</p>
                                </div>

                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Assign to Branch</label>
                                    <select v-model="form.branch_id" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                        <option value="">Unassigned</option>
                                        <option v-for="branch in branches" :key="branch.id" :value="branch.id">{{ branch.name }}</option>
                                    </select>
                                    <p v-if="form.errors.branch_id" class="mt-1 text-sm text-red-500">{{ form.errors.branch_id }}</p>
                                </div>

                                <div class="sm:col-span-2 flex items-center pt-2">
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="form.is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span class="ml-2 text-sm text-slate-700">Active Account</span>
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
