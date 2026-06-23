<script setup>
import { ref } from 'vue';
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const form = useForm({
    tracking_number: ''
});

const submitTracking = () => {
    form.post(route('track.order'));
};
</script>

<template>
    <Head title="Track Your Order - SaaSMedi" />

    <PublicLayout>
        <div class="bg-white min-h-[70vh] flex flex-col justify-center items-center py-20 px-4 sm:px-6 lg:px-8">
            <div class="max-w-md w-full space-y-8">
                <div class="text-center">
                    <div class="mx-auto w-20 h-20 bg-emerald-100 rounded-full flex items-center justify-center mb-6">
                        <svg class="w-10 h-10 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 20l-5.447-2.724A1 1 0 013 16.382V5.618a1 1 0 011.447-.894L9 7m0 13l6-3m-6 3V7m6 10l4.553 2.276A1 1 0 0021 18.382V7.618a1 1 0 00-.553-.894L15 4m0 13V4m0 0L9 7" />
                        </svg>
                    </div>
                    <h2 class="mt-6 text-3xl font-extrabold text-slate-900">
                        Track Your Order
                    </h2>
                    <p class="mt-2 text-sm text-slate-600">
                        Enter your Order ID below to view the live status of your delivery.
                    </p>
                </div>
                
                <form class="mt-8 space-y-6" @submit.prevent="submitTracking">
                    <div class="rounded-md shadow-sm -space-y-px">
                        <div>
                            <label for="tracking_number" class="sr-only">Order Tracking ID</label>
                            <input id="tracking_number" name="tracking_number" type="text" required v-model="form.tracking_number"
                                class="appearance-none rounded-xl relative block w-full px-4 py-4 border border-slate-300 placeholder-slate-500 text-slate-900 focus:outline-none focus:ring-emerald-500 focus:border-emerald-500 focus:z-10 sm:text-lg transition-all" 
                                placeholder="e.g. PHA-123456" />
                        </div>
                    </div>

                    <div v-if="form.errors.tracking_number" class="text-red-500 text-sm font-bold text-center">
                        {{ form.errors.tracking_number }}
                    </div>

                    <div>
                        <button type="submit" :disabled="form.processing"
                            class="group relative w-full flex justify-center py-4 px-4 border border-transparent text-lg font-bold rounded-xl text-white bg-[#00a669] hover:bg-emerald-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-emerald-500 shadow-md shadow-emerald-500/30 transition-all">
                            <span class="absolute left-0 inset-y-0 flex items-center pl-3">
                                <svg class="h-5 w-5 text-emerald-400 group-hover:text-emerald-300" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M8 4a4 4 0 100 8 4 4 0 000-8zM2 8a6 6 0 1110.89 3.476l4.817 4.817a1 1 0 01-1.414 1.414l-4.816-4.816A6 6 0 012 8z" clip-rule="evenodd" />
                                </svg>
                            </span>
                            {{ form.processing ? 'Searching...' : 'Track Order' }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
