<template>
    <AdminLayout>
        <template #header>CRM: Lead Pipeline</template>

        <div class="flex gap-4 h-[calc(100vh-180px)] overflow-x-auto pb-4 custom-scrollbar">
            
            <!-- Kanban Columns -->
            <div v-for="column in columns" :key="column.id" class="min-w-[300px] w-[300px] bg-slate-50 rounded-2xl border border-slate-200 flex flex-col overflow-hidden shrink-0">
                
                <!-- Column Header -->
                <div class="p-3 border-b border-slate-200 flex justify-between items-center bg-slate-100/50">
                    <h3 class="font-bold text-sm text-slate-700 flex items-center gap-2">
                        <span :class="column.colorClass" class="w-2.5 h-2.5 rounded-full"></span>
                        {{ column.title }}
                    </h3>
                    <span class="bg-slate-200 text-slate-600 text-xs font-bold px-2 py-0.5 rounded-full">
                        {{ groupedLeads[column.id]?.length || 0 }}
                    </span>
                </div>

                <!-- Column Body (Drop Zone) -->
                <div 
                    class="p-3 flex-1 overflow-y-auto space-y-3"
                    @dragover.prevent
                    @drop="onDrop($event, column.id)"
                >
                    <div 
                        v-for="lead in groupedLeads[column.id]" 
                        :key="lead.id"
                        draggable="true"
                        @dragstart="onDragStart($event, lead)"
                        @click="openLeadModal(lead)"
                        class="bg-white p-4 rounded-xl shadow-sm border border-slate-200 cursor-grab active:cursor-grabbing hover:border-emerald-300 hover:shadow-md transition-all group"
                    >
                        <div class="flex justify-between items-start mb-2">
                            <h4 class="font-bold text-sm text-slate-800">{{ lead.name }}</h4>
                            <span v-if="lead.lead_score >= 40" class="text-xs font-bold text-red-600 bg-red-50 px-1.5 py-0.5 rounded border border-red-100 flex items-center gap-1">🔥 {{ lead.lead_score }}</span>
                            <span v-else-if="lead.lead_score >= 20" class="text-xs font-bold text-orange-600 bg-orange-50 px-1.5 py-0.5 rounded border border-orange-100 flex items-center gap-1">⚡ {{ lead.lead_score }}</span>
                            <span v-else class="text-xs font-bold text-slate-500 bg-slate-50 px-1.5 py-0.5 rounded border border-slate-100">{{ lead.lead_score }}</span>
                        </div>
                        <p class="text-xs text-slate-500 font-medium mb-1 truncate">🏢 {{ lead.company_name || 'N/A' }}</p>
                        <p class="text-[10px] text-slate-400 truncate">{{ lead.email }}</p>
                    </div>
                </div>

            </div>

        </div>

        <!-- Lead Modal -->
        <div v-if="selectedLead" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl max-w-lg w-full border border-slate-200 overflow-hidden flex flex-col max-h-[90vh]">
                <div class="p-5 border-b border-slate-100 bg-slate-50 flex justify-between items-center shrink-0">
                    <div>
                        <h3 class="text-lg font-bold text-slate-800">{{ selectedLead.name }}</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">{{ selectedLead.company_name }}</p>
                    </div>
                    <button @click="selectedLead = null" class="text-slate-400 hover:text-slate-600 w-8 h-8 rounded-full bg-white border border-slate-200 flex items-center justify-center shadow-sm">✕</button>
                </div>
                
                <div class="p-6 overflow-y-auto flex-1">
                    <div class="grid grid-cols-2 gap-4 mb-6">
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Email</p>
                            <p class="text-sm font-medium text-slate-700">{{ selectedLead.email || 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Phone</p>
                            <p class="text-sm font-medium text-slate-700">{{ selectedLead.phone || 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Business Type</p>
                            <p class="text-sm font-medium text-slate-700">{{ selectedLead.business_type || 'N/A' }}</p>
                        </div>
                        <div class="p-3 bg-slate-50 rounded-xl border border-slate-100">
                            <p class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-1">Source</p>
                            <p class="text-sm font-medium text-slate-700 capitalize">{{ selectedLead.source || 'Direct' }}</p>
                        </div>
                    </div>

                    <form @submit.prevent="submitNote">
                        <label class="block text-sm font-bold text-slate-700 mb-2">Sales Notes</label>
                        <textarea v-model="noteForm.notes" rows="4" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm mb-3" placeholder="Add conversation notes here..."></textarea>
                        <button type="submit" class="w-full py-2 bg-slate-900 text-white rounded-xl font-bold text-sm hover:bg-slate-800 transition-colors shadow-sm" :disabled="noteForm.processing">
                            Save Notes
                        </button>
                    </form>
                </div>
                
                <div class="p-4 border-t border-slate-100 bg-slate-50 shrink-0 flex gap-2">
                    <select v-model="statusForm.status" @change="submitStatus" class="flex-1 rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                        <option v-for="col in columns" :key="col.id" :value="col.id">{{ col.title }}</option>
                    </select>
                </div>
            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { ref } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../../Layout.vue';

const props = defineProps({
    groupedLeads: Object
});

const columns = [
    { id: 'new', title: 'New Lead', colorClass: 'bg-blue-500' },
    { id: 'contacted', title: 'Contacted', colorClass: 'bg-amber-500' },
    { id: 'demo_scheduled', title: 'Demo Scheduled', colorClass: 'bg-purple-500' },
    { id: 'in_trial', title: 'In Trial', colorClass: 'bg-indigo-500' },
    { id: 'converted', title: 'Converted', colorClass: 'bg-emerald-500' },
    { id: 'lost', title: 'Lost', colorClass: 'bg-slate-500' },
];

const selectedLead = ref(null);
const noteForm = useForm({ notes: '' });
const statusForm = useForm({ status: '' });

const onDragStart = (event, lead) => {
    event.dataTransfer.setData('leadId', lead.id);
    event.dataTransfer.effectAllowed = 'move';
};

const onDrop = (event, targetStatus) => {
    const leadId = event.dataTransfer.getData('leadId');
    if (leadId) {
        // Find lead current status
        let currentStatus = null;
        for (const [status, leads] of Object.entries(props.groupedLeads)) {
            if (leads.some(l => l.id == leadId)) {
                currentStatus = status;
                break;
            }
        }

        if (currentStatus && currentStatus !== targetStatus) {
            router.post(route('admin.crm.leads.update-status', leadId), { status: targetStatus }, { preserveScroll: true });
        }
    }
};

const openLeadModal = (lead) => {
    selectedLead.value = lead;
    noteForm.notes = lead.notes || '';
    statusForm.status = lead.status;
};

const submitNote = () => {
    noteForm.post(route('admin.crm.leads.store-note', selectedLead.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            selectedLead.value.notes = noteForm.notes;
        }
    });
};

const submitStatus = () => {
    statusForm.post(route('admin.crm.leads.update-status', selectedLead.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            selectedLead.value = null; // Close modal on status change since it moves column
        }
    });
};
</script>

<style>
.custom-scrollbar::-webkit-scrollbar {
    height: 8px;
    width: 8px;
}
.custom-scrollbar::-webkit-scrollbar-track {
    background: #f1f5f9;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb {
    background: #cbd5e1;
    border-radius: 4px;
}
.custom-scrollbar::-webkit-scrollbar-thumb:hover {
    background: #94a3b8;
}
</style>
