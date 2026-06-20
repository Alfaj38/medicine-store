<template>
    <AdminLayout>
        <template #header>Demo Request Details</template>

        <div class="max-w-4xl">
            <div class="mb-6 flex items-center justify-between">
                <Link :href="route('admin.demo-requests.index')" class="text-slate-400 hover:text-slate-600 font-medium text-sm flex items-center gap-1">
                    ← Back to Demo Requests
                </Link>
                
                <form @submit.prevent="deleteRequest" class="inline">
                    <button type="submit" class="text-red-500 hover:text-red-700 text-sm font-bold flex items-center gap-1">
                        Delete Request
                    </button>
                </form>
            </div>

            <div class="bg-white rounded-2xl shadow-sm border border-slate-200 overflow-hidden mb-6">
                <div class="p-6 border-b border-slate-100 bg-slate-50 flex justify-between items-center">
                    <div>
                        <h2 class="text-xl font-bold text-slate-800">{{ demoRequest.name }}</h2>
                        <p class="text-sm text-slate-500 font-medium mt-1">{{ demoRequest.company_name }}</p>
                    </div>
                    <span class="px-3 py-1.5 rounded-lg text-sm font-bold capitalize border"
                        :class="{
                            'bg-blue-50 text-blue-700 border-blue-200': demoRequest.status === 'pending',
                            'bg-purple-50 text-purple-700 border-purple-200': demoRequest.status === 'confirmed',
                            'bg-emerald-50 text-emerald-700 border-emerald-200': demoRequest.status === 'completed',
                            'bg-slate-50 text-slate-600 border-slate-200': demoRequest.status === 'no_show',
                        }">
                        {{ demoRequest.status.replace('_', '-') }}
                    </span>
                </div>
                
                <div class="p-6 grid grid-cols-2 gap-8">
                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Contact Information</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-1">Email Address</p>
                                <a :href="`mailto:${demoRequest.email}`" class="text-sm font-bold text-emerald-600 hover:underline">{{ demoRequest.email }}</a>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-1">Phone Number</p>
                                <p class="text-sm font-bold text-slate-800">{{ demoRequest.phone || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>

                    <div>
                        <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-4">Business Details</h3>
                        <div class="space-y-4">
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-1">Company Name</p>
                                <p class="text-sm font-bold text-slate-800">{{ demoRequest.company_name }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-1">Role / Designation</p>
                                <p class="text-sm font-bold text-slate-800">{{ demoRequest.role || 'Not provided' }}</p>
                            </div>
                            <div>
                                <p class="text-xs text-slate-500 font-medium mb-1">Company Size</p>
                                <p class="text-sm font-bold text-slate-800">{{ demoRequest.company_size || 'Not provided' }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="p-6 border-t border-slate-100" v-if="demoRequest.message">
                    <h3 class="text-xs font-bold text-slate-400 uppercase tracking-wider mb-3">Additional Notes / Message</h3>
                    <div class="bg-slate-50 rounded-xl p-4 border border-slate-100 text-sm text-slate-700 whitespace-pre-wrap">
                        {{ demoRequest.message }}
                    </div>
                </div>
                
                <div class="p-6 border-t border-slate-100 bg-slate-50">
                    <form @submit.prevent="updateStatus" class="flex items-end gap-4">
                        <div class="flex-1">
                            <label class="block text-xs font-bold text-slate-700 mb-1.5">Update Status</label>
                            <select v-model="form.status" class="w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 text-sm font-medium">
                                <option value="pending">Pending</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="completed">Completed</option>
                                <option value="no_show">No-show</option>
                            </select>
                        </div>
                        <button type="submit" class="px-5 py-2 rounded-xl font-bold text-sm text-white bg-slate-900 hover:bg-slate-800 transition-colors shadow-sm" :disabled="form.processing">
                            Save Changes
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </AdminLayout>
</template>

<script setup>
import { useForm, Link, router } from '@inertiajs/vue3';
import AdminLayout from '../Layout.vue';

const props = defineProps({
    demoRequest: Object
});

const form = useForm({
    status: props.demoRequest.status
});

const updateStatus = () => {
    form.put(route('admin.demo-requests.update', props.demoRequest.id), {
        preserveScroll: true
    });
};

const deleteRequest = () => {
    if (confirm('Are you sure you want to delete this demo request? This action cannot be undone.')) {
        router.delete(route('admin.demo-requests.destroy', props.demoRequest.id));
    }
};
</script>
