<script setup>
import { ref, computed, watch, onMounted } from 'vue';
import { useForm, router, Link } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import Layout from '../Settings/Layout.vue';
import PermissionsTab from './Partials/PermissionsTab.vue';
import BranchAccessTab from './Partials/BranchAccessTab.vue';

const props = defineProps({
    roles: Array,
    permissionsByModule: Object,
    branches: Array
});

// Top bar filters
const selectedBranch = ref('All Branches');
const selectedRoleId = ref(props.roles.length > 0 ? props.roles[0].id : null);
const statusFilter = ref('All');

// Active Tab
const activeTab = ref('Permissions');
const tabs = ['Permissions', 'Branch Access', 'Assigned Users', 'Role Information'];

// Active Module in Permissions Tab
const activeModule = ref(Object.keys(props.permissionsByModule)[0] || '');

// Modals
const isCreateModalOpen = ref(false);
const createForm = useForm({
    name: ''
});

// Main Form for the selected role
const form = useForm({
    name: '',
    permissions: [],
    branches: []
});

const activeRole = computed(() => {
    return props.roles.find(r => r.id === selectedRoleId.value);
});

// Initialize form when a role is selected
watch(selectedRoleId, (newId) => {
    const role = props.roles.find(r => r.id === newId);
    if (role) {
        form.name = role.name;
        form.permissions = role.permissions ? role.permissions.map(p => p.name) : [];
        form.branches = role.branch_ids || [];
    } else {
        form.name = '';
        form.permissions = [];
        form.branches = [];
    }
}, { immediate: true });

const totalAvailablePermissions = computed(() => {
    let count = 0;
    for (const module in props.permissionsByModule) {
        count += props.permissionsByModule[module].length;
    }
    return count;
});

const openCreateModal = () => {
    createForm.reset();
    isCreateModalOpen.value = true;
};

const closeCreateModal = () => {
    isCreateModalOpen.value = false;
    createForm.reset();
    createForm.clearErrors();
};

const submitCreateRole = () => {
    createForm.post(route('company.roles.store'), {
        onSuccess: (page) => {
            closeCreateModal();
            // Automatically select the new role if possible, or reload data handles it.
        }
    });
};

const saveChanges = () => {
    if (!activeRole.value) return;
    
    form.put(route('company.roles.update', activeRole.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                icon: 'success',
                title: 'Changes saved successfully!',
                toast: true,
                position: 'top-end',
                showConfirmButton: false,
                timer: 3000,
                timerProgressBar: true,
            });
        }
    });
};

const resetChanges = () => {
    if (activeRole.value) {
        form.name = activeRole.value.name;
        form.permissions = activeRole.value.permissions ? activeRole.value.permissions.map(p => p.name) : [];
        form.branches = activeRole.value.branch_ids || [];
    }
};

const deleteRole = () => {
    if (!activeRole.value) return;
    if (confirm(`Are you sure you want to delete the ${activeRole.value.name} role?`)) {
        router.delete(route('company.roles.destroy', activeRole.value.id));
    }
};

// Handlers for child components
const togglePermission = (permName) => {
    const index = form.permissions.indexOf(permName);
    if (index === -1) {
        form.permissions.push(permName);
    } else {
        form.permissions.splice(index, 1);
    }
};

const toggleModule = (moduleName, selectAll) => {
    const modulePerms = props.permissionsByModule[moduleName] || [];
    if (selectAll) {
        modulePerms.forEach(p => {
            if (!form.permissions.includes(p.name)) {
                form.permissions.push(p.name);
            }
        });
    } else {
        modulePerms.forEach(p => {
            const idx = form.permissions.indexOf(p.name);
            if (idx !== -1) {
                form.permissions.splice(idx, 1);
            }
        });
    }
};

const toggleBranch = (branchId) => {
    const index = form.branches.indexOf(branchId);
    if (index === -1) {
        form.branches.push(branchId);
    } else {
        form.branches.splice(index, 1);
    }
};

</script>

<template>
    <Layout title="Roles & Permissions">
        <div class="flex justify-between items-center mb-6">
            <div class="flex items-center gap-3">
                <div class="w-10 h-10 rounded-xl bg-white border border-slate-200 flex items-center justify-center text-slate-500 shadow-sm">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                </div>
                <div>
                    <h2 class="text-xl font-bold text-slate-800">Roles & Permissions</h2>
                    <p class="text-sm text-slate-500">Manage user roles, permissions, and access levels.</p>
                </div>
            </div>
            <button @click="openCreateModal" class="inline-flex items-center gap-2 px-4 py-2 border border-transparent text-sm font-semibold rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700 transition-all active:scale-[0.98]">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                Add Role
            </button>
        </div>

        <!-- Filters Box -->
        <div class="bg-white p-4 border border-slate-200 rounded-2xl flex flex-wrap gap-4 mb-6 shadow-sm relative z-20">
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 pl-1">Select Branch</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                    </div>
                    <select disabled class="pl-10 block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-11 cursor-not-allowed">
                        <option>All Branches</option>
                    </select>
                </div>
            </div>
            
            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 pl-1">Select Role</label>
                <div class="relative">
                    <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                        <svg class="w-4 h-4 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                    </div>
                    <select v-model="selectedRoleId" class="pl-10 block w-full rounded-xl border-slate-200 text-slate-900 font-medium focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-11 transition-colors hover:border-slate-300">
                        <option v-for="role in roles" :key="role.id" :value="role.id">{{ role.name }}</option>
                        <option v-if="roles.length === 0" :value="null">No Roles Found</option>
                    </select>
                </div>
            </div>

            <div class="flex-1 min-w-[200px]">
                <label class="block text-xs font-bold text-slate-500 uppercase tracking-wider mb-2 pl-1">Status</label>
                <select disabled class="block w-full rounded-xl border-slate-200 bg-slate-50 text-slate-500 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-11 cursor-not-allowed">
                    <option>All</option>
                </select>
            </div>
        </div>

        <div v-if="activeRole">
            <!-- Tabs -->
            <div class="border-b border-slate-200 mb-6">
                <nav class="-mb-px flex space-x-8" aria-label="Tabs">
                    <button 
                        v-for="tab in tabs" :key="tab"
                        @click="activeTab = tab"
                        class="whitespace-nowrap py-4 px-1 border-b-2 font-medium text-sm transition-colors"
                        :class="activeTab === tab ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'"
                    >
                        {{ tab }}
                        <span v-if="tab === 'Assigned Users'" class="ml-2 bg-slate-100 text-slate-600 py-0.5 px-2.5 rounded-full text-xs">{{ activeRole.users_count }}</span>
                    </button>
                </nav>
            </div>

            <div class="flex flex-col xl:flex-row gap-6 mb-24">
                <!-- Main Tab Content Area -->
                <div class="flex-1 min-w-0">
                    <div v-show="activeTab === 'Permissions'">
                        <PermissionsTab 
                            :permissionsByModule="permissionsByModule" 
                            v-model:activeModule="activeModule"
                            :formPermissions="form.permissions"
                            @togglePermission="togglePermission"
                            @toggleModule="toggleModule"
                        />
                    </div>
                    <div v-show="activeTab === 'Branch Access'">
                        <BranchAccessTab 
                            :branches="branches"
                            :formBranches="form.branches"
                            @toggleBranch="toggleBranch"
                        />
                    </div>
                    <div v-show="activeTab === 'Assigned Users'">
                        <div class="bg-white border border-slate-200 rounded-2xl p-6">
                            <h3 class="font-bold text-slate-800 text-lg mb-6">Assigned Users</h3>
                            
                            <div v-if="activeRole.users && activeRole.users.length > 0" class="overflow-x-auto">
                                <table class="min-w-full divide-y divide-slate-200">
                                    <thead class="bg-slate-50">
                                        <tr>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Name</th>
                                            <th scope="col" class="px-6 py-3 text-left text-xs font-medium text-slate-500 uppercase tracking-wider">Email</th>
                                        </tr>
                                    </thead>
                                    <tbody class="bg-white divide-y divide-slate-200">
                                        <tr v-for="user in activeRole.users" :key="user.id">
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="flex items-center">
                                                    <div class="flex-shrink-0 h-10 w-10">
                                                        <div v-if="user.avatar" class="h-10 w-10 rounded-full overflow-hidden">
                                                            <img :src="user.avatar" alt="" class="h-full w-full object-cover">
                                                        </div>
                                                        <div v-else class="h-10 w-10 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 font-bold">
                                                            {{ user.name.charAt(0) }}
                                                        </div>
                                                    </div>
                                                    <div class="ml-4">
                                                        <div class="text-sm font-medium text-slate-900">{{ user.name }}</div>
                                                    </div>
                                                </div>
                                            </td>
                                            <td class="px-6 py-4 whitespace-nowrap">
                                                <div class="text-sm text-slate-500">{{ user.email }}</div>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                            
                            <div v-else class="text-center py-12">
                                <svg class="mx-auto h-12 w-12 text-slate-300 mb-3" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
                                </svg>
                                <h3 class="text-sm font-medium text-slate-900">No Users Assigned</h3>
                                <p class="mt-1 text-sm text-slate-500">There are currently no users assigned to this role.</p>
                            </div>
                        </div>
                    </div>
                    <div v-show="activeTab === 'Role Information'">
                        <div class="bg-white border border-slate-200 rounded-2xl p-6 max-w-2xl">
                            <h3 class="font-bold text-slate-800 text-lg mb-6">Role Details</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700 mb-1">Role Name</label>
                                    <input v-model="form.name" type="text" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                <div class="pt-4 border-t border-slate-100 flex justify-between items-center">
                                    <p class="text-sm text-slate-500">Created on {{ new Date(activeRole.created_at).toLocaleDateString() }}</p>
                                    <button @click="deleteRole" class="text-red-600 hover:text-red-700 font-medium text-sm px-4 py-2 hover:bg-red-50 rounded-lg transition-colors" :disabled="activeRole.users_count > 0" :class="{'opacity-50 cursor-not-allowed': activeRole.users_count > 0}">
                                        Delete Role
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Sidebar (Role Summary) -->
                <div class="w-full xl:w-72 flex-shrink-0 space-y-6">
                    <!-- Role Summary Card -->
                    <div class="bg-white border border-slate-200 rounded-2xl p-5">
                        <div class="flex items-center gap-3 mb-5">
                            <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                            <h3 class="font-bold text-slate-800">Role Summary</h3>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Role Name</p>
                                <p class="text-sm font-bold text-slate-800">{{ activeRole.name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Status</p>
                                <span class="inline-flex items-center px-2 py-0.5 rounded text-xs font-medium bg-emerald-100 text-emerald-800">
                                    Active
                                </span>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Total Permissions</p>
                                <p class="text-sm font-bold text-slate-800">{{ form.permissions.length }} <span class="text-slate-400 font-normal">/ {{ totalAvailablePermissions }}</span></p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Branch Access</p>
                                <p class="text-sm font-bold text-slate-800">{{ form.branches.length }} <span class="text-slate-400 font-normal">/ {{ branches.length }}</span></p>
                            </div>
                            <div class="pt-4 border-t border-slate-100">
                                <p class="text-xs text-slate-500 mb-1">Created At</p>
                                <p class="text-xs font-medium text-slate-700">{{ new Date(activeRole.created_at).toLocaleString() }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 mb-1">Last Updated</p>
                                <p class="text-xs font-medium text-slate-700">{{ new Date(activeRole.updated_at).toLocaleString() }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Permission Guide Card -->
                    <div class="bg-amber-50 border border-amber-100 rounded-2xl p-5">
                        <div class="flex items-center gap-2 mb-3 text-amber-700">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path></svg>
                            <h3 class="font-bold text-sm">Permission Guide</h3>
                        </div>
                        <ul class="text-xs space-y-2 text-slate-700">
                            <li><strong class="text-slate-900">View:</strong> Can view data and information</li>
                            <li><strong class="text-slate-900">Add:</strong> Can add new records</li>
                            <li><strong class="text-slate-900">Edit:</strong> Can edit existing records</li>
                            <li><strong class="text-slate-900">Delete:</strong> Can delete records</li>
                        </ul>
                    </div>
                </div>
            </div>

            <!-- Sticky Bottom Actions -->
            <div class="fixed bottom-0 left-0 md:left-64 right-0 bg-white border-t border-slate-200 py-4 px-6 flex justify-end gap-3 z-40">
                <button @click="resetChanges" :disabled="form.processing" class="px-5 py-2.5 bg-white border border-slate-300 text-slate-700 rounded-xl text-sm font-medium hover:bg-slate-50 transition-colors focus:outline-none">
                    <div class="flex items-center gap-2">
                        <svg class="w-4 h-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path></svg>
                        Reset Changes
                    </div>
                </button>
                <button @click="saveChanges" :disabled="form.processing" class="px-5 py-2.5 bg-emerald-600 border border-transparent text-white rounded-xl text-sm font-semibold hover:bg-emerald-700 transition-colors focus:outline-none shadow-sm flex items-center gap-2">
                    <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                    Save Changes
                </button>
            </div>
        </div>

        <!-- Empty State (No Roles) -->
        <div v-else class="bg-white border border-slate-200 rounded-2xl p-12 text-center">
            <svg class="mx-auto h-12 w-12 text-slate-300 mb-4" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path>
            </svg>
            <h3 class="text-lg font-medium text-slate-900">No Roles Available</h3>
            <p class="mt-2 text-sm text-slate-500 mb-6">Get started by creating a new role for your company.</p>
            <button @click="openCreateModal" class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-semibold rounded-xl shadow-sm text-white bg-emerald-600 hover:bg-emerald-700">
                Create First Role
            </button>
        </div>

        <!-- Create Role Modal -->
        <div v-if="isCreateModalOpen" class="fixed inset-0 z-[60] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900 bg-opacity-40 backdrop-blur-sm transition-opacity" @click="closeCreateModal"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitCreateRole">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-bold text-slate-900 mb-2" id="modal-title">Create New Role</h3>
                            <p class="text-sm text-slate-500 mb-6">Define a new role before assigning permissions to it.</p>
                            <div>
                                <label class="block text-sm font-medium text-slate-700 mb-1">Role Name <span class="text-rose-500">*</span></label>
                                <input v-model="createForm.name" type="text" required placeholder="e.g. Cashier, Manager" class="block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                <p v-if="createForm.errors.name" class="mt-1 text-sm text-rose-500">{{ createForm.errors.name }}</p>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse border-t border-slate-100">
                            <button type="submit" :disabled="createForm.processing" class="w-full inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-base font-medium text-white hover:bg-emerald-700 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm">
                                Create Role
                            </button>
                            <button type="button" @click="closeCreateModal" class="mt-3 w-full inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-slate-700 hover:bg-slate-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Cancel
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </Layout>
</template>
