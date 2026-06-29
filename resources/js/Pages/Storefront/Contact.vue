<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import Swal from 'sweetalert2';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    company: Object,
    seo: Object,
});

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: '',
});

const submit = () => {
    form.post(route('storefront.contact.submit', props.company.slug), {
        onSuccess: () => {
            Swal.fire({
                title: 'Message Sent!',
                text: 'Thank you for contacting us! We will get back to you shortly.',
                icon: 'success',
                confirmButtonColor: '#059669', // Emerald 600
                confirmButtonText: 'OK'
            });
            form.reset();
        },
        onError: () => {
            Swal.fire({
                title: 'Error',
                text: 'There was a problem sending your message. Please check the fields and try again.',
                icon: 'error',
                confirmButtonColor: '#e11d48'
            });
        }
    });
};
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />
    <StorefrontLayout :company="company">
        
        <!-- Page Header -->
        <div class="bg-gradient-to-r from-emerald-50 to-teal-50 py-12 lg:py-16 border-b border-emerald-100">
            <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 text-center">
                <h1 class="text-3xl lg:text-4xl font-extrabold text-slate-900 mb-4">Contact <span class="text-emerald-600">{{ company.name }}</span></h1>
                <p class="text-slate-600 text-lg max-w-2xl mx-auto">We're here to help! Whether you have a question about your order, need a specific medicine, or just want to say hello.</p>
            </div>
        </div>

        <div class="max-w-[1400px] mx-auto px-4 sm:px-6 lg:px-8 py-12 lg:py-16">
            <div class="grid grid-cols-1 lg:grid-cols-3 gap-8 lg:gap-12">
                
                <!-- Left Sidebar: Contact Info -->
                <div class="lg:col-span-1 space-y-6">
                    <!-- Contact Info Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 lg:p-8">
                        <h3 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                            <svg class="w-6 h-6 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Get in Touch
                        </h3>
                        
                        <div class="space-y-6">
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Our Location</h4>
                                    <p class="text-slate-600 text-sm leading-relaxed">{{ company.address || 'Dhaka, Bangladesh' }}</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Phone Number</h4>
                                    <p class="text-slate-600 text-sm font-medium">{{ company.phone || '+880 1918 828282' }}</p>
                                    <p class="text-slate-500 text-xs mt-0.5">Available 9:00 AM - 10:00 PM</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                </div>
                                <div>
                                    <h4 class="font-semibold text-slate-900 mb-1">Email Address</h4>
                                    <p class="text-slate-600 text-sm">{{ company.email || 'support@medicornerbd.com' }}</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Business Hours -->
                    <div class="bg-emerald-600 rounded-2xl shadow-sm p-6 lg:p-8 text-white relative overflow-hidden">
                        <div class="absolute right-0 top-0 opacity-10">
                            <svg class="w-32 h-32 -mr-10 -mt-10" fill="currentColor" viewBox="0 0 24 24"><path d="M11.99 2C6.47 2 2 6.48 2 12s4.47 10 9.99 10C17.52 22 22 17.52 22 12S17.52 2 11.99 2zM12 20c-4.42 0-8-3.58-8-8s3.58-8 8-8 8 3.58 8 8-3.58 8-8 8z"></path><path d="M12.5 7H11v6l5.25 3.15.75-1.23-4.5-2.67z"></path></svg>
                        </div>
                        <h3 class="text-xl font-bold mb-6 relative z-10">Business Hours</h3>
                        <ul class="space-y-3 text-sm relative z-10">
                            <li class="flex justify-between items-center border-b border-emerald-500/50 pb-2">
                                <span class="text-emerald-100">Monday - Friday</span>
                                <span class="font-semibold">9:00 AM - 10:00 PM</span>
                            </li>
                            <li class="flex justify-between items-center border-b border-emerald-500/50 pb-2">
                                <span class="text-emerald-100">Saturday</span>
                                <span class="font-semibold">9:00 AM - 10:00 PM</span>
                            </li>
                            <li class="flex justify-between items-center">
                                <span class="text-emerald-100">Sunday</span>
                                <span class="font-semibold bg-white text-emerald-700 px-2 py-0.5 rounded text-xs uppercase tracking-wider">Closed</span>
                            </li>
                        </ul>
                    </div>
                </div>

                <!-- Right Sidebar: Contact Form -->
                <div class="lg:col-span-2">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6 lg:p-10">
                        <h2 class="text-2xl font-bold text-slate-900 mb-2">Send us a Message</h2>
                        <p class="text-slate-500 mb-8">Fill out the form below and our team will get back to you within 24 hours.</p>
                        
                        <form @submit.prevent="submit" class="space-y-6">
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Your Name <span class="text-rose-500">*</span></label>
                                    <input v-model="form.name" type="text" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3" placeholder="John Doe" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Phone Number <span class="text-rose-500">*</span></label>
                                    <input v-model="form.phone" type="text" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3" placeholder="01XXXXXXXXX" required>
                                </div>
                            </div>

                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Email Address</label>
                                    <input v-model="form.email" type="email" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3" placeholder="john@example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-semibold text-slate-700 mb-2">Subject <span class="text-rose-500">*</span></label>
                                    <select v-model="form.subject" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3" required>
                                        <option value="" disabled>Select a subject</option>
                                        <option value="General Inquiry">General Inquiry</option>
                                        <option value="Order Status">Order Status</option>
                                        <option value="Prescription Delivery">Prescription Delivery</option>
                                        <option value="Feedback / Complaint">Feedback / Complaint</option>
                                    </select>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-2">Message <span class="text-rose-500">*</span></label>
                                <textarea v-model="form.message" rows="5" class="w-full rounded-xl border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 text-sm py-3" placeholder="How can we help you?" required></textarea>
                            </div>

                            <div>
                                <button type="submit" class="bg-emerald-600 hover:bg-emerald-500 text-white font-bold py-3.5 px-8 rounded-xl shadow-sm transition-colors text-sm w-full md:w-auto flex items-center justify-center gap-2">
                                    Send Message
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M14 5l7 7m0 0l-7 7m7-7H3"></path></svg>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>

            </div>
        </div>
    </StorefrontLayout>
</template>
