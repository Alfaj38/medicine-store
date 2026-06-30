<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';

const props = defineProps({
    employees: Array,
    attendances: Array,
    currentMonth: Number,
    currentYear: Number,
});

const showModal = ref(false);
const selectedDate = ref(new Date().toISOString().split('T')[0]);

const form = useForm({
    employee_id: '',
    date: selectedDate.value,
    status: 'present',
    check_in: '09:00',
    check_out: '17:00',
    notes: '',
});

const openModal = (date = null, empId = null) => {
    form.reset();
    form.date = date || selectedDate.value;
    if (empId) form.employee_id = empId;
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    form.reset();
    form.clearErrors();
};

const submit = () => {
    form.post(route('hr.attendance.store'), {
        preserveScroll: true,
        onSuccess: () => closeModal(),
    });
};

const changeMonth = (delta) => {
    let m = props.currentMonth + delta;
    let y = props.currentYear;
    if (m > 12) { m = 1; y++; }
    if (m < 1) { m = 12; y--; }
    router.get(route('hr.attendance.index'), { month: m, year: y }, { preserveScroll: true });
};

// Generate days for the current month
const daysInMonth = computed(() => new Date(props.currentYear, props.currentMonth, 0).getDate());
const monthDays = computed(() => {
    const days = [];
    for (let i = 1; i <= daysInMonth.value; i++) {
        days.push(i);
    }
    return days;
});

const getAttendance = (empId, day) => {
    const dateStr = `${props.currentYear}-${String(props.currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`;
    return props.attendances.find(a => a.employee_id === empId && a.date === dateStr);
};

const getStatusColor = (status) => {
    switch (status) {
        case 'present': return 'bg-emerald-500';
        case 'absent': return 'bg-rose-500';
        case 'leave': return 'bg-amber-400';
        case 'half-day': return 'bg-blue-400';
        default: return 'bg-slate-200';
    }
};
</script>

<template>
    <Head title="Attendance - SaaSMedi" />

    <AppLayout>
        <div class="w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Attendance Log</h1>
                    <p class="text-sm text-slate-500">Track daily attendance and working hours.</p>
                </div>
                <div class="flex items-center gap-4">
                    <div class="flex items-center bg-white border border-slate-200 rounded-lg p-1 shadow-sm">
                        <button @click="changeMonth(-1)" class="p-1 hover:bg-slate-100 rounded text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path></svg>
                        </button>
                        <span class="px-4 text-sm font-bold text-slate-700 min-w-[120px] text-center">
                            {{ new Date(currentYear, currentMonth - 1).toLocaleString('default', { month: 'long', year: 'numeric' }) }}
                        </span>
                        <button @click="changeMonth(1)" class="p-1 hover:bg-slate-100 rounded text-slate-600">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                    <button @click="openModal()" class="bg-blue-600 hover:bg-blue-500 text-white px-4 py-2 rounded-lg font-medium shadow-sm transition-colors flex items-center gap-2">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Mark Attendance
                    </button>
                </div>
            </div>

            <!-- Legends -->
            <div class="flex gap-4 mb-4 text-xs font-medium text-slate-600 bg-white p-3 rounded-lg border border-slate-200 shadow-sm inline-flex">
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-emerald-500"></span> Present</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-rose-500"></span> Absent</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-amber-400"></span> Leave</div>
                <div class="flex items-center gap-1.5"><span class="w-3 h-3 rounded-full bg-blue-400"></span> Half Day</div>
            </div>

            <div class="bg-white border border-slate-200 rounded-xl shadow-sm overflow-hidden">
                <div class="overflow-x-auto">
                    <table class="w-full text-left text-sm text-slate-600 border-collapse">
                        <thead class="bg-slate-50 text-xs uppercase text-slate-500 border-b border-slate-200">
                            <tr>
                                <th class="px-4 py-3 font-semibold sticky left-0 bg-slate-50 border-r border-slate-200 min-w-[150px]">Employee</th>
                                <th v-for="day in monthDays" :key="day" class="px-2 py-3 font-semibold text-center border-r border-slate-100 min-w-[35px]">
                                    {{ day }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-if="!employees.length">
                                <td :colspan="daysInMonth + 1" class="px-6 py-8 text-center text-slate-500">No employees found.</td>
                            </tr>
                            <tr v-for="emp in employees" :key="emp.id" class="hover:bg-slate-50 transition-colors">
                                <td class="px-4 py-2 font-medium text-slate-900 sticky left-0 bg-white border-r border-slate-200 shadow-[2px_0_5px_-2px_rgba(0,0,0,0.05)]">
                                    {{ emp.name }}
                                </td>
                                <td v-for="day in monthDays" :key="day" class="p-1 border-r border-slate-100 text-center cursor-pointer hover:bg-slate-100 transition-colors" @click="openModal(`${currentYear}-${String(currentMonth).padStart(2, '0')}-${String(day).padStart(2, '0')}`, emp.id)">
                                    <div class="w-full h-full flex items-center justify-center min-h-[28px]">
                                        <div v-if="getAttendance(emp.id, day)" :class="getStatusColor(getAttendance(emp.id, day).status)" class="w-4 h-4 rounded-sm shadow-sm" :title="getAttendance(emp.id, day).status"></div>
                                        <div v-else class="w-4 h-4 rounded-sm bg-slate-100 border border-slate-200"></div>
                                    </div>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

        <!-- Attendance Modal -->
        <div v-if="showModal" class="fixed inset-0 z-50 flex items-center justify-center bg-slate-900/50 backdrop-blur-sm p-4">
            <div class="bg-white rounded-2xl shadow-xl w-full max-w-md overflow-hidden flex flex-col">
                <div class="px-6 py-4 border-b border-slate-100 flex items-center justify-between bg-slate-50">
                    <h3 class="text-lg font-bold text-slate-900">Mark Attendance</h3>
                    <button @click="closeModal" class="text-slate-400 hover:text-slate-600 transition-colors">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                    </button>
                </div>
                
                <form @submit.prevent="submit" class="p-6 space-y-4">
                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Date</label>
                        <input v-model="form.date" type="date" class="w-full rounded-lg border-slate-300 bg-slate-50 text-sm" readonly>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Employee</label>
                        <select v-model="form.employee_id" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="" disabled>Select Employee</option>
                            <option v-for="emp in employees" :key="emp.id" :value="emp.id">{{ emp.name }}</option>
                        </select>
                    </div>

                    <div>
                        <label class="block text-sm font-semibold text-slate-700 mb-1">Status</label>
                        <select v-model="form.status" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm" required>
                            <option value="present">Present</option>
                            <option value="absent">Absent</option>
                            <option value="leave">Leave</option>
                            <option value="half-day">Half Day</option>
                        </select>
                    </div>

                    <div v-if="form.status === 'present' || form.status === 'half-day'" class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Check In</label>
                            <input v-model="form.check_in" type="time" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                        <div>
                            <label class="block text-sm font-semibold text-slate-700 mb-1">Check Out</label>
                            <input v-model="form.check_out" type="time" class="w-full rounded-lg border-slate-300 focus:border-blue-500 focus:ring-blue-500 text-sm">
                        </div>
                    </div>
                    
                    <div class="pt-4 flex justify-end gap-3 border-t border-slate-100">
                        <button type="button" @click="closeModal" class="px-4 py-2 text-sm font-medium text-slate-600 hover:text-slate-800 transition-colors">Cancel</button>
                        <button type="submit" :disabled="form.processing" class="bg-blue-600 hover:bg-blue-500 text-white px-5 py-2 rounded-lg text-sm font-semibold transition-colors disabled:opacity-50">
                            {{ form.processing ? 'Saving...' : 'Save' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </AppLayout>
</template>
