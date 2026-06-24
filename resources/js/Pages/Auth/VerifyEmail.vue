<script setup>
import { computed } from 'vue';
import { Head, Link, useForm, usePage } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    status: {
        type: String,
    },
});

const form = useForm({});

const submit = () => {
    form.post(route('verification.send'));
};

const verificationLinkSent = computed(() => props.status === 'verification-link-sent' || usePage().props.flash?.success);
</script>

<template>
    <PublicLayout>
        <Head title="Email Verification - SaaSMedi" />

        <div class="max-w-md mx-auto my-16 bg-white rounded-2xl shadow-xl border border-slate-100 overflow-hidden">
            <div class="p-8">
                <div class="text-center mb-8">
                    <div class="w-16 h-16 bg-emerald-100 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                        </svg>
                    </div>
                    <h2 class="text-2xl font-bold text-slate-900">Verify your email</h2>
                    <p class="mt-2 text-sm text-slate-500">
                        Thanks for signing up! Before getting started, could you verify your email address by clicking on the link we just emailed to you? If you didn't receive the email, we will gladly send you another.
                    </p>
                </div>

                <div class="mb-4 font-medium text-sm text-emerald-600 bg-emerald-50 p-4 rounded-lg" v-if="verificationLinkSent">
                    A new verification link has been sent to the email address you provided during registration.
                </div>

                <form @submit.prevent="submit">
                    <div class="mt-4 flex items-center justify-between">
                        <button type="submit" :class="{'opacity-25': form.processing}" :disabled="form.processing" class="inline-flex justify-center rounded-xl bg-emerald-600 px-4 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors w-full sm:w-auto">
                            Resend Email
                        </button>

                        <Link
                            :href="route('logout')"
                            method="post"
                            as="button"
                            class="text-sm text-slate-600 hover:text-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2"
                        >
                            Log Out
                        </Link>
                    </div>
                </form>
            </div>
        </div>
    </PublicLayout>
</template>
