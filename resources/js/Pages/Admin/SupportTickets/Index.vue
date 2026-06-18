<template>
    <AdminLayout>
        <template #header>Support Tickets</template>

        <div class="bg-white rounded-lg shadow overflow-hidden">
            <div class="p-6 border-b flex justify-between items-center">
                <h3 class="text-lg font-medium text-gray-900">Tickets Queue</h3>
                
                <div class="flex space-x-2">
                    <select v-model="filters.status" @change="applyFilters" class="rounded-md border-gray-300 text-sm">
                        <option value="">All Statuses</option>
                        <option value="open">Open</option>
                        <option value="answered">Answered</option>
                        <option value="closed">Closed</option>
                    </select>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr class="bg-gray-50 border-b">
                            <th class="p-4 font-medium text-sm text-gray-600">Company & User</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Subject</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Priority</th>
                            <th class="p-4 font-medium text-sm text-gray-600">Status</th>
                            <th class="p-4 font-medium text-sm text-gray-600 text-right">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="ticket in tickets.data" :key="ticket.id" class="border-b hover:bg-gray-50">
                            <td class="p-4">
                                <div class="font-medium text-gray-900">{{ ticket.company?.name }}</div>
                                <div class="text-sm text-gray-500">{{ ticket.user?.name }}</div>
                            </td>
                            <td class="p-4">
                                <span class="font-medium text-gray-900">{{ ticket.subject }}</span>
                                <p class="text-xs text-gray-500 mt-1 truncate max-w-xs">{{ ticket.body }}</p>
                            </td>
                            <td class="p-4">
                                <span class="capitalize text-sm">{{ ticket.priority }}</span>
                            </td>
                            <td class="p-4">
                                <span v-if="ticket.status === 'open'" class="px-2 py-1 bg-amber-100 text-amber-800 rounded text-xs font-medium uppercase tracking-wide">Open</span>
                                <span v-else-if="ticket.status === 'answered'" class="px-2 py-1 bg-emerald-100 text-emerald-800 rounded text-xs font-medium uppercase tracking-wide">Answered</span>
                                <span v-else class="px-2 py-1 bg-gray-100 text-gray-800 rounded text-xs font-medium uppercase tracking-wide">Closed</span>
                            </td>
                            <td class="p-4 text-right space-x-2">
                                <button @click="openReplyModal(ticket)" class="text-indigo-600 hover:text-indigo-900 text-sm font-medium">Reply</button>
                            </td>
                        </tr>
                        <tr v-if="tickets.data.length === 0">
                            <td colspan="5" class="p-6 text-center text-gray-500">No support tickets found.</td>
                        </tr>
                    </tbody>
                </table>
            </div>

            <div class="p-6 border-t bg-gray-50 flex justify-between items-center" v-if="tickets.data.length > 0">
                <div class="text-sm text-gray-600">
                    Showing {{ tickets.from }} to {{ tickets.to }} of {{ tickets.total }} entries
                </div>
                <Pagination :links="tickets.links" />
            </div>
        </div>

        <!-- Reply Modal (Simplified) -->
        <div v-if="replyingTicket" class="fixed inset-0 bg-gray-500 bg-opacity-75 flex items-center justify-center p-4 z-50">
            <div class="bg-white rounded-lg shadow-xl max-w-2xl w-full">
                <div class="p-6 border-b">
                    <h3 class="text-lg font-medium text-gray-900">Reply to Ticket: {{ replyingTicket.subject }}</h3>
                </div>
                <div class="p-6">
                    <div class="mb-4 p-4 bg-gray-50 rounded-lg text-sm text-gray-700">
                        {{ replyingTicket.body }}
                    </div>
                    
                    <form @submit.prevent="submitReply">
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Your Reply</label>
                            <textarea v-model="replyForm.reply" rows="4" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500" required></textarea>
                        </div>
                        <div class="mb-4">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Status</label>
                            <select v-model="replyForm.status" class="w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500">
                                <option value="answered">Answered</option>
                                <option value="closed">Closed</option>
                            </select>
                        </div>
                        <div class="flex justify-end space-x-3">
                            <button type="button" @click="replyingTicket = null" class="px-4 py-2 bg-gray-200 text-gray-800 rounded shadow hover:bg-gray-300">Cancel</button>
                            <button type="submit" class="px-4 py-2 bg-indigo-600 text-white rounded shadow hover:bg-indigo-700">Send Reply</button>
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
