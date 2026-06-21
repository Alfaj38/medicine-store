<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const form = useForm({
    name: '',
    email: '',
    phone: '',
    subject: '',
    message: ''
});

const submit = () => {
    Swal.fire({
        icon: 'success',
        title: 'Message Sent!',
        text: 'Thank you for contacting us! We will get back to you shortly.',
    });
    form.reset();
};
</script>

<template>
    <Head title="Contact Us - MediSaaS" />
    <PublicLayout>
        <div class="bg-white py-16 md:py-24">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <div class="lg:grid lg:grid-cols-2 lg:gap-16 items-start">
                    
                    <!-- Left: Info -->
                    <div class="mb-12 lg:mb-0 pr-0 lg:pr-12">
                        <h1 class="text-4xl font-black text-slate-900 mb-6">Get in Touch</h1>
                        <p class="text-lg text-slate-600 mb-12">We'd love to hear from you. Send us a message for enterprise pricing, demo requests, or any general inquiries and our team will get back to you shortly.</p>
                        
                        <div class="space-y-8">
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Phone</h4>
                                    <p class="text-slate-600 mt-1">+880 1234-567890</p>
                                </div>
                            </div>
                            
                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Email</h4>
                                    <p class="text-slate-600 mt-1">info@medisaas.com</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.243-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Address</h4>
                                    <p class="text-slate-600 mt-1">House # 12, Road # 5,<br/>Dhanmondi, Dhaka-1205,<br/>Bangladesh</p>
                                </div>
                            </div>

                            <div class="flex items-start gap-4">
                                <div class="w-12 h-12 bg-emerald-50 text-emerald-600 rounded-full flex items-center justify-center flex-shrink-0">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                </div>
                                <div>
                                    <h4 class="text-sm font-bold text-slate-900">Business Hours</h4>
                                    <p class="text-slate-600 mt-1">Sat - Thu: 9:00 AM - 6:00 PM</p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Right: Form -->
                    <div class="bg-white p-8 md:p-10 rounded-3xl border border-slate-200 shadow-2xl shadow-slate-200/50">
                        <form @submit.prevent="submit" class="space-y-6">
                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Your Name</label>
                                <input v-model="form.name" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required placeholder="John Doe">
                            </div>
                            
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Email Address</label>
                                    <input v-model="form.email" type="email" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required placeholder="john@example.com">
                                </div>
                                <div>
                                    <label class="block text-sm font-bold text-slate-700 mb-2">Phone Number</label>
                                    <input v-model="form.phone" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" placeholder="+880 1...">
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Subject</label>
                                <input v-model="form.subject" type="text" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500" required placeholder="How can we help?">
                            </div>

                            <div>
                                <label class="block text-sm font-bold text-slate-700 mb-2">Your Message</label>
                                <textarea v-model="form.message" rows="5" class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-3 text-slate-900 focus:outline-none focus:border-emerald-500 focus:ring-1 focus:ring-emerald-500 resize-none" required placeholder="Tell us more about your needs..."></textarea>
                            </div>

                            <button type="submit" class="w-full bg-emerald-600 hover:bg-emerald-500 text-white font-bold text-lg py-4 rounded-xl shadow-lg shadow-emerald-500/30 transition-all hover:-translate-y-0.5">
                                Send Message
                            </button>
                        </form>
                    </div>

                </div>
            </div>
        </div>
    </PublicLayout>
</template>
