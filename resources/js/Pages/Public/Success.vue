<script setup>
import { Head, Link } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';

const props = defineProps({
    type: { type: String, default: 'demo' }, // 'demo' or 'trial'
    name: { type: String, default: '' },
});

const isDemo = props.type === 'demo';

const config = {
    demo: {
        icon: '🎉',
        title: 'Demo Booked Successfully!',
        subtitle: `Thanks ${props.name ? props.name.split(' ')[0] : 'there'}! We've received your booking.`,
        steps: [
            { icon: '📧', title: 'Check Your Email', desc: 'A confirmation email with your booking details is on its way.' },
            { icon: '📞', title: 'We\'ll Call You', desc: 'Our team will contact you 24 hours before to confirm the session.' },
            { icon: '🎯', title: 'Personalized Demo', desc: 'We\'ll tailor the demo to the pain points you mentioned.' },
        ],
        cta: { label: 'Explore Features First', route: 'features' },
        cta2: { label: 'View Pricing', route: 'pricing' },
        color: 'blue',
    },
    trial: {
        icon: '🚀',
        title: 'Your Trial is Active!',
        subtitle: `Welcome aboard ${props.name ? props.name.split(' ')[0] : ''}! Your 14-day free trial has started.`,
        steps: [
            { icon: '✅', title: 'Account Created', desc: 'Your company workspace is ready. Log in to get started.' },
            { icon: '📦', title: 'Import Your Data', desc: 'Use our CSV wizard to import products, suppliers, and stock.' },
            { icon: '💳', title: 'Make Your First Sale', desc: 'Head to POS and complete your first billing in under a minute.' },
        ],
        cta: { label: 'Go to Dashboard', route: 'login' },
        cta2: { label: 'View Pricing Plans', route: 'pricing' },
        color: 'emerald',
    },
};

const c = config[props.type] || config.demo;
</script>

<template>
    <Head :title="`${isDemo ? 'Demo Booked' : 'Trial Started'} - MediSaaS`" />
    <PublicLayout>
        <div class="min-h-screen bg-gradient-to-br from-slate-50 to-emerald-50/30 flex items-center justify-center py-20 px-4">
            <div class="max-w-2xl w-full text-center">

                <!-- Animated Success Icon -->
                <div class="relative inline-flex mb-8">
                    <div class="w-24 h-24 rounded-full flex items-center justify-center text-5xl shadow-2xl"
                        :class="isDemo ? 'bg-blue-50 shadow-blue-200/50' : 'bg-emerald-50 shadow-emerald-200/50'">
                        {{ c.icon }}
                    </div>
                    <div class="absolute -top-1 -right-1 w-7 h-7 bg-emerald-500 rounded-full flex items-center justify-center shadow-lg shadow-emerald-400/50">
                        <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M5 13l4 4L19 7"/>
                        </svg>
                    </div>
                </div>

                <h1 class="text-4xl font-black text-slate-900 mb-3">{{ c.title }}</h1>
                <p class="text-lg text-slate-500 mb-12">{{ c.subtitle }}</p>

                <!-- What Happens Next -->
                <div class="bg-white rounded-2xl border border-slate-200 shadow-sm p-8 mb-8 text-left">
                    <h3 class="text-sm font-bold text-slate-500 uppercase tracking-wider mb-6 text-center">What Happens Next</h3>
                    <div class="space-y-6">
                        <div v-for="(step, i) in c.steps" :key="i" class="flex items-start gap-4">
                            <div class="w-10 h-10 rounded-xl flex items-center justify-center text-xl flex-shrink-0"
                                :class="isDemo ? 'bg-blue-50' : 'bg-emerald-50'">
                                {{ step.icon }}
                            </div>
                            <div class="flex-1">
                                <div class="font-semibold text-slate-800 text-sm">{{ step.title }}</div>
                                <div class="text-slate-500 text-sm mt-0.5">{{ step.desc }}</div>
                            </div>
                            <div class="w-6 h-6 rounded-full flex items-center justify-center text-xs font-bold text-white mt-1 flex-shrink-0"
                                :class="isDemo ? 'bg-blue-500' : 'bg-emerald-500'">
                                {{ i + 1 }}
                            </div>
                        </div>
                    </div>
                </div>

                <!-- CTAs -->
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    <Link :href="route(c.cta.route)"
                        class="font-bold px-8 py-4 rounded-full shadow-lg transition-all hover:-translate-y-0.5 text-white"
                        :class="isDemo ? 'bg-blue-600 hover:bg-blue-500 shadow-blue-500/30' : 'bg-emerald-600 hover:bg-emerald-500 shadow-emerald-500/30'">
                        {{ c.cta.label }}
                    </Link>
                    <Link :href="route(c.cta2.route)"
                        class="font-bold px-8 py-4 rounded-full border-2 border-slate-200 text-slate-700 hover:border-slate-300 transition-all">
                        {{ c.cta2.label }}
                    </Link>
                </div>

                <p class="text-sm text-slate-400 mt-8">
                    Questions? <a :href="'mailto:info@medisaas.com'" class="text-emerald-600 hover:underline font-medium">Email us at info@medisaas.com</a>
                </p>
            </div>
        </div>
    </PublicLayout>
</template>
