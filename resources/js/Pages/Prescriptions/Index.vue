<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Pagination from '@/Components/Pagination.vue';
import Modal from '@/Components/Modal.vue';

const props = defineProps({
    prescriptions: Object,
    filters: Object,
});

const currentStatus = ref(props.filters.status || 'pending');
const selectedPrescription = ref(null);
const isModalOpen = ref(false);

const filterByStatus = (status) => {
    currentStatus.value = status;
    router.get(route('prescriptions.index'), { status }, { preserveState: true, preserveScroll: true });
};

const viewPrescription = (prescription) => {
    selectedPrescription.value = prescription;
    isModalOpen.value = true;
};

const updateStatus = (id, status) => {
    router.put(route('prescriptions.update', id), { status }, {
        preserveScroll: true,
        onSuccess: () => {
            isModalOpen.value = false;
        }
    });
};

const processInPos = (prescription) => {
    // Navigate to POS, maybe passing prescription id in query string if needed
    isModalOpen.value = false;
    router.get(route('pos.index'), { prescription_id: prescription.id });
};
</script>

<template>
    <Head title="Prescriptions - SaaSMedi" />

    <AppLayout>
        <div class="w-full mx-auto py-8 px-4 sm:px-6 lg:px-8">
            
            <div class="flex justify-between items-center mb-6">
                <div>
                    <h1 class="text-2xl font-bold text-slate-900">Prescription Uploads</h1>
                    <p class="text-sm text-slate-500 mt-1">Manage and process prescriptions sent by patients.</p>
                </div>
                
                <div class="flex bg-white rounded-lg p-1 border border-slate-200">
                    <button @click="filterByStatus('pending')" :class="{'bg-slate-100 text-slate-800 font-semibold shadow-sm': currentStatus === 'pending', 'text-slate-500 hover:text-slate-700': currentStatus !== 'pending'}" class="px-4 py-2 text-sm rounded-md transition-colors">Pending</button>
                    <button @click="filterByStatus('processing')" :class="{'bg-slate-100 text-slate-800 font-semibold shadow-sm': currentStatus === 'processing', 'text-slate-500 hover:text-slate-700': currentStatus !== 'processing'}" class="px-4 py-2 text-sm rounded-md transition-colors">Processing</button>
                    <button @click="filterByStatus('completed')" :class="{'bg-slate-100 text-slate-800 font-semibold shadow-sm': currentStatus === 'completed', 'text-slate-500 hover:text-slate-700': currentStatus !== 'completed'}" class="px-4 py-2 text-sm rounded-md transition-colors">Completed</button>
                    <button @click="filterByStatus('all')" :class="{'bg-slate-100 text-slate-800 font-semibold shadow-sm': currentStatus === 'all', 'text-slate-500 hover:text-slate-700': currentStatus !== 'all'}" class="px-4 py-2 text-sm rounded-md transition-colors">All</button>
                </div>
            </div>

            <!-- Prescriptions Grid -->
            <div v-if="prescriptions.data.length > 0" class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-6">
                <div v-for="p in prescriptions.data" :key="p.id" class="bg-white rounded-xl shadow-sm border border-slate-200 overflow-hidden flex flex-col hover:shadow-md transition-shadow cursor-pointer" @click="viewPrescription(p)">
                    
                    <div class="h-48 bg-slate-100 relative group overflow-hidden">
                        <img v-if="p.image_url" :src="p.image_url" class="w-full h-full object-cover transition-transform duration-300 group-hover:scale-105" />
                        <div v-else class="w-full h-full flex items-center justify-center text-slate-400">
                            <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                        </div>
                        
                        <div class="absolute top-2 right-2">
                            <span v-if="p.status === 'pending'" class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-amber-100 text-amber-700 shadow-sm border border-amber-200">Pending</span>
                            <span v-else-if="p.status === 'processing'" class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-blue-100 text-blue-700 shadow-sm border border-blue-200">Processing</span>
                            <span v-else-if="p.status === 'completed'" class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-emerald-100 text-emerald-700 shadow-sm border border-emerald-200">Completed</span>
                            <span v-else-if="p.status === 'rejected'" class="inline-flex items-center px-2 py-1 rounded text-xs font-bold bg-rose-100 text-rose-700 shadow-sm border border-rose-200">Rejected</span>
                        </div>
                    </div>

                    <div class="p-4 flex-1 flex flex-col">
                        <h3 class="font-bold text-slate-800 text-sm truncate">{{ p.patient_name }}</h3>
                        <p class="text-xs text-slate-500 mt-1 flex items-center gap-1">
                            <svg class="w-3 h-3 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                            {{ p.patient_phone }}
                        </p>
                        <p class="text-[11px] text-slate-400 mt-2 line-clamp-2 leading-snug">{{ p.patient_address || 'No address provided' }}</p>
                        
                        <div class="mt-auto pt-4 flex justify-between items-end">
                            <span class="text-[10px] text-slate-400">{{ p.created_at }}</span>
                            <button class="text-xs font-semibold text-emerald-600 hover:text-emerald-700">View Details &rarr;</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Empty State -->
            <div v-else class="text-center py-20 bg-white rounded-2xl border border-dashed border-slate-300">
                <svg class="mx-auto h-12 w-12 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                <h3 class="mt-2 text-sm font-semibold text-slate-900">No prescriptions found</h3>
                <p class="mt-1 text-sm text-slate-500">There are no prescriptions matching the current filter.</p>
            </div>

            <!-- Pagination -->
            <div class="mt-8" v-if="prescriptions.links.length > 3">
                <Pagination :links="prescriptions.links" />
            </div>

        </div>

        <!-- Prescription Details Modal -->
        <Modal :show="isModalOpen" @close="isModalOpen = false" maxWidth="4xl">
            <div class="bg-white rounded-lg shadow-xl overflow-hidden flex flex-col md:flex-row h-[80vh] md:h-[600px]" v-if="selectedPrescription">
                
                <!-- Left: Image Viewer -->
                <div class="w-full md:w-2/3 bg-slate-900 flex items-center justify-center relative group p-4">
                    <img v-if="selectedPrescription.image_url" :src="selectedPrescription.image_url" class="max-w-full max-h-full object-contain" />
                    <div v-else class="text-slate-400 text-sm">No image available</div>
                </div>

                <!-- Right: Details & Actions -->
                <div class="w-full md:w-1/3 flex flex-col border-l border-slate-200">
                    <div class="p-6 flex-1 overflow-y-auto">
                        <div class="flex justify-between items-start mb-4">
                            <h2 class="text-lg font-bold text-slate-900">Details</h2>
                            <button @click="isModalOpen = false" class="text-slate-400 hover:text-slate-600">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path></svg>
                            </button>
                        </div>

                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Patient Name</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ selectedPrescription.patient_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Phone Number</p>
                                <p class="text-sm font-semibold text-slate-900 mt-1">{{ selectedPrescription.patient_phone }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Delivery Address</p>
                                <p class="text-sm text-slate-700 mt-1">{{ selectedPrescription.patient_address || 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider">Notes / Instructions</p>
                                <p class="text-sm text-slate-700 mt-1 bg-slate-50 p-3 rounded-lg border border-slate-100">{{ selectedPrescription.notes || 'No notes provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium uppercase tracking-wider mb-2">Current Status</p>
                                <div class="flex gap-2 flex-wrap">
                                    <button @click="updateStatus(selectedPrescription.id, 'pending')" :class="{'ring-2 ring-amber-500 ring-offset-1': selectedPrescription.status === 'pending'}" class="px-3 py-1 bg-amber-100 text-amber-700 text-xs font-bold rounded shadow-sm border border-amber-200 hover:bg-amber-200">Pending</button>
                                    <button @click="updateStatus(selectedPrescription.id, 'processing')" :class="{'ring-2 ring-blue-500 ring-offset-1': selectedPrescription.status === 'processing'}" class="px-3 py-1 bg-blue-100 text-blue-700 text-xs font-bold rounded shadow-sm border border-blue-200 hover:bg-blue-200">Processing</button>
                                    <button @click="updateStatus(selectedPrescription.id, 'completed')" :class="{'ring-2 ring-emerald-500 ring-offset-1': selectedPrescription.status === 'completed'}" class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded shadow-sm border border-emerald-200 hover:bg-emerald-200">Completed</button>
                                    <button @click="updateStatus(selectedPrescription.id, 'rejected')" :class="{'ring-2 ring-rose-500 ring-offset-1': selectedPrescription.status === 'rejected'}" class="px-3 py-1 bg-rose-100 text-rose-700 text-xs font-bold rounded shadow-sm border border-rose-200 hover:bg-rose-200">Reject</button>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 border-t border-slate-200 bg-slate-50">
                        <button @click="processInPos(selectedPrescription)" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3 px-4 rounded-xl shadow-sm transition-colors flex justify-center items-center gap-2">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            Process in POS
                        </button>
                    </div>
                </div>
            </div>
        </Modal>

    </AppLayout>
</template>
