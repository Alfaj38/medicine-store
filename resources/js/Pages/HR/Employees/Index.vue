<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    employees: Array,
});

const showModal = ref(false);
const editingEmployee = ref(null);

const form = useForm({
    name: '',
    employee_id: '',
    designation: '',
    department: '',
    phone: '',
    email: '',
    address: '',
    salary: 0,
    joining_date: '',
    status: 'active',
    notes: '',
});

const openModal = (employee = null) => {
    if (employee) {
        editingEmployee.value = employee;
        form.name = employee.name;
        form.employee_id = employee.employee_id;
        form.designation = employee.designation;
        form.department = employee.department;
        form.phone = employee.phone;
        form.email = employee.email;
        form.address = employee.address;
        form.salary = employee.salary;
        form.joining_date = employee.joining_date;
        form.status = employee.status;
        form.notes = employee.notes;
    } else {
        editingEmployee.value = null;
        form.reset();
    }
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    if (editingEmployee.value) {
        form.put(route('hr.employees.update', editingEmployee.value.id), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    } else {
        form.post(route('hr.employees.store'), {
            preserveScroll: true,
            onSuccess: () => closeModal(),
        });
    }
};

const deleteEmployee = (id) => {
    if (confirm('Are you sure you want to delete this employee?')) {
        router.delete(route('hr.employees.destroy', id), {
            preserveScroll: true,
        });
    }
};
</script>

<template>
    <Head title="Employees - SaaSMedi" />

    <AppLayout>
        <div class="max-w-7xl mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Employees</h1>
                    <p class="text-sm text-slate-500">Manage your pharmacy staff and their details.</p>
                </div>
                <button @click="openModal()" class="bg-emerald-600 hover:bg-emerald-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"></path></svg>
                    Add Employee
                </button>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-6 py-4 font-semibold">Employee</th>
                                <th class="px-6 py-4 font-semibold">Role</th>
                                <th class="px-6 py-4 font-semibold text-right">Base Salary</th>
                                <th class="px-6 py-4 font-semibold">Joined</th>
                                <th class="px-6 py-4 font-semibold text-center">Status</th>
                                <th class="px-6 py-4 font-semibold text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="!employees.length">
                                <td colspan="6" class="px-6 py-8 text-center text-slate-500">No employees found.</td>
                            </tr>
                            <tr v-for="emp in employees" :key="emp.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-6 py-4">
                                    <div class="font-bold text-slate-900">{{ emp.name }}</div>
                                    <div class="text-xs text-slate-500">{{ emp.phone }}</div>
                                </td>
                                <td class="px-6 py-4">
                                    <div class="font-medium text-slate-700">{{ emp.designation }}</div>
                                    <div class="text-xs text-slate-500">{{ emp.department || '-' }}</div>
                                </td>
                                <td class="px-6 py-4 text-right font-medium text-slate-700">
                                    ${{ parseFloat(emp.salary).toFixed(2) }}
                                </td>
                                <td class="px-6 py-4">
                                    {{ emp.joining_date }}
                                </td>
                                <td class="px-6 py-4 text-center">
                                    <span :class="{'bg-emerald-100 text-emerald-700': emp.status === 'active', 'bg-rose-100 text-rose-700': emp.status === 'terminated', 'bg-slate-100 text-slate-700': emp.status === 'inactive'}" class="px-2.5 py-1 rounded-full text-[10px] font-bold uppercase tracking-wider">
                                        {{ emp.status }}
                                    </span>
                                </td>
                                <td class="px-6 py-4 text-right">
                                    <button @click="openModal(emp)" class="text-blue-600 hover:text-blue-800 font-medium text-xs mr-3">Edit</button>
                                    <button @click="deleteEmployee(emp.id)" class="text-rose-600 hover:text-rose-800 font-medium text-xs">Delete</button>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Employee Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-2xl overflow-hidden flex flex-col max-h-[90vh]">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900">{{ editingEmployee ? 'Edit Employee' : 'Add Employee' }}</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="flex flex-col flex-1 overflow-hidden">
                    <div class="p-6 overflow-y-auto flex-1 grid grid-cols-1 sm:grid-cols-2 gap-4">
                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name</label>
                            <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                            <div v-if="form.errors.name" class="text-xs text-rose-500 mt-1">{{ form.errors.name }}</div>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Role / Designation</label>
                            <input v-model="form.designation" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" placeholder="e.g. Pharmacist" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Phone</label>
                            <input v-model="form.phone" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Base Salary</label>
                            <input v-model="form.salary" type="number" step="0.01" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>

                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Joining Date</label>
                            <input v-model="form.joining_date" type="date" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                        </div>

                        <div class="sm:col-span-2">
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                            <select v-model="form.status" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm" required>
                                <option value="active">Active</option>
                                <option value="inactive">Inactive (Leave/Break)</option>
                                <option value="terminated">Terminated</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="px-6 py-4 bg-slate-50 border-t border-slate-100 flex justify-end gap-3 flex-shrink-0">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-emerald-600 hover:bg-emerald-500 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save Employee' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
