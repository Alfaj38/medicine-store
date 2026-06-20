<template>
    <AdminLayout>
        <template #header>Support Center</template>

        <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden">
            <div class="p-5 border-b border-slate-100 flex justify-between items-center bg-slate-50">
                <h2 class="text-lg font-bold text-slate-800 flex items-center gap-2"><span class="text-xl">🎫</span> Ticket Queue</h2>
                
                <div class="flex space-x-2">
                    <select v-model="filters.status" @change="applyFilters" class="rounded-xl border-slate-300 text-sm focus:ring-emerald-500 focus:border-emerald-500 font-medium text-slate-700 bg-white">
                        <option value="">All Statuses</option>
                        <option value="open">Open</option>
                        <option value="answered">Answered</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse whitespace-nowrap text-sm">
                    <thead>
                        <tr class="bg-slate-50 border-b border-slate-100 font-semibold text-slate-500 uppercase tracking-wider text-xs">
                            <th class="p-4">Tenant / User</th>
                            <th class="p-4">Subject & Category</th>
                            <th class="p-4">SLA / Priority</th>
                            <th class="p-4">Status</th>
                            <th class="p-4 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr v-for="ticket in tickets.data" :key="ticket.id" class="hover:bg-slate-50/80 transition-colors">
                            <td class="p-4">
                                <div class="font-bold text-slate-800">{{ ticket.company?.name || 'N/A' }}</div>
                                <div class="text-xs font-medium text-slate-500 mt-0.5">{{ ticket.user?.name || 'Unknown User' }}</div>
                            </td>
                            <td class="p-4">
                                <div class="flex items-center gap-2 mb-1">
                                    <span class="px-2 py-0.5 bg-slate-100 text-slate-600 rounded text-[10px] font-bold uppercase tracking-wider">{{ ticket.category }}</span>
                                    <span class="font-bold text-slate-800 truncate max-w-[250px]">{{ ticket.subject }}</span>
                                </div>
                                <p class="text-xs text-slate-500 truncate max-w-xs">{{ ticket.body }}</p>
                            </td>
                            <td class="p-4">
                                <span class="px-2.5 py-1 rounded-full text-xs font-bold capitalize flex inline-flex items-center gap-1.5 w-fit"
                                    :class="{
                                        'bg-red-100 text-red-700 border border-red-200': ticket.priority === 'critical',
                                        'bg-orange-100 text-orange-700 border border-orange-200': ticket.priority === 'high',
                                        'bg-blue-100 text-blue-700 border border-blue-200': ticket.priority === 'medium',
                                        'bg-slate-100 text-slate-600 border border-slate-200': ticket.priority === 'low',
                                    }">
                                    <span class="w-1.5 h-1.5 rounded-full" 
                                        :class="{
                                            'bg-red-500': ticket.priority === 'critical',
                                            'bg-orange-500': ticket.priority === 'high',
                                            'bg-blue-500': ticket.priority === 'medium',
                                            'bg-slate-400': ticket.priority === 'low',
                                        }"></span>
                                    {{ ticket.priority }}
                                </span>
                            </td>
                            <td class="p-4">
                                <span v-if="ticket.status === 'open'" class="text-xs font-bold text-amber-600 bg-amber-50 px-2.5 py-1 rounded uppercase tracking-wide border border-amber-200">Open</span>
                                <span v-else-if="ticket.status === 'answered'" class="text-xs font-bold text-emerald-600 bg-emerald-50 px-2.5 py-1 rounded uppercase tracking-wide border border-emerald-200">Answered</span>
                                <span v-else class="text-xs font-bold text-slate-500 bg-slate-100 px-2.5 py-1 rounded uppercase tracking-wide border border-slate-200">Closed</span>
                            </td>
                            <td class="p-4 text-right">
                                <button @click="openReplyModal(ticket)" class="px-3 py-1.5 bg-slate-900 hover:bg-slate-800 text-white text-xs font-bold rounded-lg transition-colors shadow-sm">
                                    Reply
                                </button>
                            </td>
                        </tr>
                        <tr v-if="tickets.data.length === 0">
                            <td colspan="5" class="p-8 text-center text-slate-500 font-medium">No support tickets found in the queue.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-4 border-t border-slate-100 bg-slate-50 flex justify-between items-center" v-if="tickets.data.length > 0">
                <div class="text-xs font-medium text-slate-500">
                    Showing {{ tickets.from }} to {{ tickets.to }} of {{ tickets.total }} entries
                </div>
                <Pagination :links="tickets.links" />
            </div>
        </div>

        <!-- Reply Modal -->
        <div v-if="replyingTicket" class="fixed inset-0 bg-slate-900/60 backdrop-blur-sm flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-2xl shadow-xl max-w-2xl w-full border border-slate-200 overflow-hidden">
                <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <h3 class="text-lg font-bold text-slate-800">Reply to Ticket</h3>
                    <button @click="replyingTicket = null" class="text-slate-400 hover:text-slate-600">✕</button>
                </div>
                <div class="p-6">
                    <div class="mb-5">
                        <p class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-1">Original Message</p>
                        <div class="p-4 bg-slate-50 border border-slate-100 rounded-xl text-sm text-slate-700 whitespace-pre-wrap font-medium">
                            {{ replyingTicket.body }}
                        </div>
                    </div>
                    
                    <form @submit.prevent="submitReply">
                        <div class="mb-5">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Your Reply</label>
                            <textarea v-model="replyForm.reply" rows="5" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm" required placeholder="Type your response here..."></textarea>
                        </div>
                        <div class="mb-6">
                            <label class="block text-sm font-bold text-slate-700 mb-2">Update Status</label>
                            <select v-model="replyForm.status" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                                <option value="answered">Mark as Answered</option>
                                <option value="closed">Close Ticket</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3 pt-4 border-t border-slate-100">
                            <button type="button" @click="replyingTicket = null" class="px-5 py-2.5 bg-white text-slate-700 border border-slate-300 rounded-xl font-bold text-sm hover:bg-slate-50 transition-colors">Cancel</button>
                            <button type="submit" class="px-5 py-2.5 bg-emerald-600 text-white rounded-xl font-bold text-sm hover:bg-emerald-700 transition-colors shadow-sm">Send Reply & Update</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { ref, reactive } from 'vue';
import { router, useForm } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';
import Pagination from '@/Components/Pagination.vue';

const props = defineProps({
    tickets: Object,
    filters: Object,
});

const filters = reactive({
    status: props.filters.status || '',
});

const applyFilters = () => {
    router.get(route('admin.support-tickets.index'), filters, { preserveState: true, replace: true });
};

const replyingTicket = ref(null);
const replyForm = useForm({
    reply: '',
    status: 'answered'
});

const openReplyModal = (ticket) => {
    replyingTicket.value = ticket;
    replyForm.reset();
    replyForm.status = 'answered';
};

const submitReply = () => {
    replyForm.post(route('admin.support-tickets.reply', replyingTicket.value.id), {
        onSuccess: () => {
            replyingTicket.value = null;
        }
    });
};
</script>
