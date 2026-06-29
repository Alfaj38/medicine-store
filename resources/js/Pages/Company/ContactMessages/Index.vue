<script setup>
import { Head, Link, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import AppLayout from '@/Layouts/AppLayout.vue';
import Swal from 'sweetalert2';

const props = defineProps({
    messages: Object,
    filters: Object,
});

const showModal = ref(false);
const selectedMessage = ref(null);

const replyForm = useForm({
    status: 'replied',
    reply_message: '',
});

const openMessage = (msg) => {
    selectedMessage.value = msg;
    replyForm.reply_message = msg.reply_message || '';
    
    // Automatically mark as read if it's unread when opening
    if (msg.status === 'unread') {
        router.patch(route('contact-messages.status', msg.id), { status: 'read' }, {
            preserveScroll: true,
            preserveState: true,
            onSuccess: () => {
                msg.status = 'read';
            }
        });
    }
    
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
    selectedMessage.value = null;
    replyForm.reset();
};

const submitReply = () => {
    replyForm.patch(route('contact-messages.status', selectedMessage.value.id), {
        preserveScroll: true,
        onSuccess: () => {
            Swal.fire({
                title: 'Success!',
                text: 'Reply recorded and status updated.',
                icon: 'success',
                confirmButtonColor: '#059669',
            });
            closeModal();
        },
        onError: () => {
            Swal.fire('Error', 'There was a problem submitting your reply.', 'error');
        }
    });
};

const deleteMessage = (id) => {
    Swal.fire({
        title: 'Are you sure?',
        text: "You won't be able to revert this!",
        icon: 'warning',
        showCancelButton: true,
        confirmButtonColor: '#e11d48',
        cancelButtonColor: '#94a3b8',
        confirmButtonText: 'Yes, delete it!'
    }).then((result) => {
        if (result.isConfirmed) {
            router.delete(route('contact-messages.destroy', id), {
                preserveScroll: true,
                onSuccess: () => {
                    Swal.fire('Deleted!', 'The message has been deleted.', 'success');
                }
            });
        }
    });
};

const formatTime = (dateStr) => {
    const d = new Date(dateStr);
    return d.toLocaleString('en-US', { month: 'short', day: 'numeric', hour: '2-digit', minute: '2-digit' });
};
</script>

<template>
    <Head title="Contact Messages" />

    <AppLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-bold text-xl text-slate-800 leading-tight">
                    Contact Messages
                </h2>
                
                <div class="flex gap-2">
                    <Link :href="route('contact-messages.index')" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="!filters.status ? 'bg-slate-800 text-white' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50'">
                        All
                    </Link>
                    <Link :href="route('contact-messages.index', { status: 'unread' })" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="filters.status === 'unread' ? 'bg-amber-100 text-amber-800 border border-amber-200' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50'">
                        Unread
                    </Link>
                    <Link :href="route('contact-messages.index', { status: 'read' })" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="filters.status === 'read' ? 'bg-blue-100 text-blue-800 border border-blue-200' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50'">
                        Read
                    </Link>
                    <Link :href="route('contact-messages.index', { status: 'replied' })" class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors" :class="filters.status === 'replied' ? 'bg-emerald-100 text-emerald-800 border border-emerald-200' : 'bg-white text-slate-600 border border-slate-300 hover:bg-slate-50'">
                        Replied
                    </Link>
                </div>
            </div>
        </template>

        <div class="py-12 max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-slate-200">
                <div class="overflow-x-auto">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50 border-b border-slate-200">
                                <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Date</th>
                                <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Customer</th>
                                <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Subject</th>
                                <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider">Status</th>
                                <th class="py-3 px-6 text-xs font-semibold text-slate-500 uppercase tracking-wider text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="divide-y divide-slate-100">
                            <tr v-for="msg in messages.data" :key="msg.id" class="hover:bg-slate-50/50 transition-colors" :class="{'bg-emerald-50/30': msg.status === 'unread'}">
                                <td class="py-4 px-6 text-sm text-slate-600 whitespace-nowrap">
                                    {{ formatTime(msg.created_at) }}
                                </td>
                                <td class="py-4 px-6">
                                    <div class="text-sm font-medium text-slate-900">{{ msg.name }}</div>
                                    <div class="text-xs text-slate-500">{{ msg.phone }}</div>
                                    <div v-if="msg.email" class="text-xs text-slate-500">{{ msg.email }}</div>
                                </td>
                                <td class="py-4 px-6 text-sm text-slate-700 font-medium">
                                    {{ msg.subject }}
                                </td>
                                <td class="py-4 px-6">
                                    <span v-if="msg.status === 'unread'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-amber-100 text-amber-800">
                                        Unread
                                    </span>
                                    <span v-else-if="msg.status === 'read'" class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                        Read
                                    </span>
                                    <span v-else class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-emerald-100 text-emerald-800">
                                        Replied
                                    </span>
                                </td>
                                <td class="py-4 px-6 text-right space-x-3">
                                    <button @click="openMessage(msg)" class="text-emerald-600 hover:text-emerald-900 text-sm font-medium transition-colors">
                                        View / Reply
                                    </button>
                                    <button @click="deleteMessage(msg.id)" class="text-rose-500 hover:text-rose-700 text-sm font-medium transition-colors">
                                        Delete
                                    </button>
                                </td>
                            </tr>
                            <tr v-if="messages.data.length === 0">
                                <td colspan="5" class="py-12 px-6 text-center text-slate-500">
                                    No contact messages found.
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                
                <!-- Pagination -->
                <div v-if="messages.links && messages.links.length > 3" class="px-6 py-4 border-t border-slate-200 bg-slate-50 flex items-center justify-between">
                    <div class="text-sm text-slate-500">
                        Showing <span class="font-medium text-slate-900">{{ messages.from || 0 }}</span> to <span class="font-medium text-slate-900">{{ messages.to || 0 }}</span> of <span class="font-medium text-slate-900">{{ messages.total }}</span> results
                    </div>
                    <div class="flex space-x-1">
                        <template v-for="(link, i) in messages.links" :key="i">
                            <Link 
                                v-if="link.url" 
                                :href="link.url" 
                                v-html="link.label"
                                class="px-3 py-1 rounded text-sm transition-colors"
                                :class="link.active ? 'bg-emerald-600 text-white font-medium' : 'bg-white text-slate-600 hover:bg-slate-200 border border-slate-200'"
                            />
                            <span 
                                v-else 
                                v-html="link.label"
                                class="px-3 py-1 rounded text-sm bg-slate-100 text-slate-400 border border-slate-200 cursor-not-allowed"
                            ></span>
                        </template>
                    </div>
                </div>
            </div>
        </div>

        <!-- View / Reply Modal -->
        <Teleport to="body">
            <div v-if="showModal" class="fixed inset-0 z-[100] overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-900/75 backdrop-blur-sm transition-opacity" @click="closeModal" aria-hidden="true"></div>

                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>

                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-2xl sm:w-full">
                    <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4 border-b border-slate-100">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-lg leading-6 font-bold text-slate-900" id="modal-title">
                                Message Details
                            </h3>
                            <button @click="closeModal" class="text-slate-400 hover:text-slate-500">
                                <span class="sr-only">Close</span>
                                <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                            </button>
                        </div>
                        
                        <div v-if="selectedMessage" class="space-y-4">
                            <div class="bg-slate-50 rounded-xl p-4 border border-slate-200">
                                <div class="grid grid-cols-2 gap-4 mb-4">
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">From</p>
                                        <p class="text-sm font-medium text-slate-900">{{ selectedMessage.name }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">Date Received</p>
                                        <p class="text-sm font-medium text-slate-900">{{ formatTime(selectedMessage.created_at) }}</p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">Contact Info</p>
                                        <p class="text-sm text-slate-700">{{ selectedMessage.phone }} <span v-if="selectedMessage.email">| {{ selectedMessage.email }}</span></p>
                                    </div>
                                    <div>
                                        <p class="text-xs font-semibold text-slate-500 uppercase">Subject</p>
                                        <p class="text-sm font-bold text-slate-900">{{ selectedMessage.subject }}</p>
                                    </div>
                                </div>
                                
                                <div class="mt-4 pt-4 border-t border-slate-200">
                                    <p class="text-xs font-semibold text-slate-500 uppercase mb-2">Message Content</p>
                                    <div class="text-sm text-slate-800 whitespace-pre-wrap bg-white p-4 rounded-lg border border-slate-100 shadow-sm">{{ selectedMessage.message }}</div>
                                </div>
                            </div>
                            
                            <form @submit.prevent="submitReply" class="mt-6">
                                <div class="mb-4">
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Internal Note / Record Reply</label>
                                    <p class="text-xs text-slate-500 mb-2">Use this space to record what was communicated back to the customer. (This does not automatically send an email/SMS yet).</p>
                                    <textarea v-model="replyForm.reply_message" rows="4" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3" placeholder="Notes about your reply..."></textarea>
                                </div>
                                
                                <div class="flex justify-end gap-3 mt-6 pt-4 border-t border-slate-100">
                                    <button type="button" @click="closeModal" class="px-4 py-2 bg-white border border-slate-300 rounded-xl text-slate-700 font-medium hover:bg-slate-50 transition-colors text-sm">
                                        Close
                                    </button>
                                    <button type="submit" class="px-4 py-2 bg-emerald-600 text-white rounded-xl font-medium hover:bg-emerald-500 transition-colors text-sm flex items-center gap-2" :disabled="replyForm.processing">
                                        <svg v-if="replyForm.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                                        Mark as Replied & Save Note
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        </Teleport>
    </AppLayout>
</template>
