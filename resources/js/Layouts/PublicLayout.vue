<script setup>
import { Link } from '@inertiajs/vue3';
import { ref } from 'vue';

const mobileMenuOpen = ref(false);
</script>

<template>
    <div class="min-h-screen bg-slate-50/50 font-sans text-slate-900 selection:bg-emerald-500 selection:text-white flex flex-col">
        
        <!-- Navigation -->
        <nav class="fixed top-0 w-full bg-white/80 backdrop-blur-md z-50 transition-all duration-300 border-b border-slate-100">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between items-center h-20">
                    <!-- Logo -->
                    <div class="flex-shrink-0 flex items-center gap-2">
                        <Link :href="route('home')" class="flex items-center gap-2">
                            <div class="w-7 h-7 rounded-md bg-emerald-600 flex items-center justify-center shadow-md shadow-emerald-500/20">
                                <span class="text-white font-black text-lg leading-none">+</span>
                            </div>
                            <span class="font-extrabold text-xl tracking-tight text-emerald-600">Medi<span class="text-slate-800">SaaS</span></span>
                        </Link>
                    </div>

                    <!-- Desktop Menu -->
                    <div class="hidden md:flex items-center gap-8 lg:gap-12">
                        <Link :href="route('features')" :class="['text-[13px] font-bold transition-colors', $page.url === '/features' ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600']">Features</Link>
                        <Link :href="route('pricing')" :class="['text-[13px] font-bold transition-colors', $page.url === '/pricing' ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600']">Pricing</Link>
                        <Link href="#" :class="['text-[13px] font-bold transition-colors', $page.url === '/about' ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600']">About</Link>
                        <Link :href="route('contact')" :class="['text-[13px] font-bold transition-colors', $page.url === '/contact' ? 'text-emerald-600' : 'text-slate-600 hover:text-emerald-600']">Contact</Link>
                    </div>

                    <!-- Actions -->
                    <div class="hidden md:flex items-center gap-5">
                        <Link :href="route('login')" class="text-[13px] font-bold text-slate-600 hover:text-slate-900 transition-colors">Sign In</Link>
                        <Link :href="route('register')" class="bg-emerald-500 hover:bg-emerald-600 text-white text-[13px] font-bold px-6 py-2.5 rounded-lg shadow-md shadow-emerald-500/20 transition-all hover:-translate-y-0.5">
                            Start Free Trial
                        </Link>
                    </div>

                    <!-- Mobile menu button -->
                    <div class="md:hidden flex items-center">
                        <button @click="mobileMenuOpen = !mobileMenuOpen" class="text-slate-600 hover:text-slate-900 focus:outline-none">
                            <svg class="h-6 w-6" fill="none" viewBox="0 0 24 24" stroke="currentColor">
                                <path v-if="!mobileMenuOpen" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                                <path v-else stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                            </svg>
                        </button>
                    </div>
                </div>
            </div>

            <!-- Mobile Menu -->
            <div v-show="mobileMenuOpen" class="md:hidden bg-white border-b border-slate-100 px-4 pt-2 pb-4 space-y-1 shadow-lg absolute w-full left-0 top-20">
                <Link :href="route('features')" class="block px-3 py-2 rounded-md text-sm font-bold text-slate-700 hover:text-emerald-600 hover:bg-slate-50">Features</Link>
                <Link :href="route('pricing')" class="block px-3 py-2 rounded-md text-sm font-bold text-slate-700 hover:text-emerald-600 hover:bg-slate-50">Pricing</Link>
                <Link href="#" class="block px-3 py-2 rounded-md text-sm font-bold text-slate-700 hover:text-emerald-600 hover:bg-slate-50">About</Link>
                <Link :href="route('contact')" class="block px-3 py-2 rounded-md text-sm font-bold text-slate-700 hover:text-emerald-600 hover:bg-slate-50">Contact</Link>
                <div class="mt-4 pt-4 border-t border-slate-100 flex flex-col gap-3">
                    <Link :href="route('login')" class="block text-center px-4 py-2 text-sm font-bold text-slate-700 hover:text-emerald-600">Sign In</Link>
                    <Link :href="route('register')" class="block text-center bg-emerald-500 hover:bg-emerald-600 text-white px-4 py-3 rounded-lg text-sm font-bold">Start Free Trial</Link>
                </div>
            </div>
        </nav>

        <!-- Main Content -->
        <main class="flex-1 flex flex-col bg-slate-50/50">
            <slot />
        </main>

        <!-- Footer -->
        <footer class="bg-slate-50/50 pt-16 pb-8 mt-auto">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="grid grid-cols-2 md:grid-cols-5 gap-8 lg:gap-12 mb-12">
                    <div class="col-span-2 md:col-span-2">
                        <Link :href="route('home')" class="flex items-center gap-2 mb-4">
                            <div class="w-6 h-6 rounded bg-emerald-600 flex items-center justify-center">
                                <span class="text-white font-black text-sm leading-none">+</span>
                            </div>
                            <span class="font-extrabold text-lg text-emerald-600 tracking-tight">Medi<span class="text-slate-800">SaaS</span></span>
                        </Link>
                        <p class="text-[11px] text-slate-500 mb-6 leading-relaxed max-w-xs font-medium">
                            The complete cloud POS & ERP solution for modern pharmacies to manage and grow their business efficiently.
                        </p>
                        <div class="flex gap-4">
                            <!-- Social Icons -->
                            <a href="#" class="w-6 h-6 rounded bg-white flex items-center justify-center shadow-sm text-slate-400 hover:text-emerald-500 transition-colors"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg></a>
                            <a href="#" class="w-6 h-6 rounded bg-white flex items-center justify-center shadow-sm text-slate-400 hover:text-emerald-500 transition-colors"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/></svg></a>
                            <a href="#" class="w-6 h-6 rounded bg-white flex items-center justify-center shadow-sm text-slate-400 hover:text-emerald-500 transition-colors"><svg class="w-3 h-3" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg></a>
                        </div>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-[12px] mb-4">Product</h3>
                        <ul class="space-y-3">
                            <li><Link :href="route('features')" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Features</Link></li>
                            <li><Link :href="route('pricing')" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Pricing</Link></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Integrations</a></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Updates</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-[12px] mb-4">Company</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">About Us</a></li>
                            <li><Link :href="route('contact')" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Contact</Link></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Careers</a></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Privacy Policy</a></li>
                        </ul>
                    </div>
                    <div>
                        <h3 class="font-bold text-slate-900 text-[12px] mb-4">Support</h3>
                        <ul class="space-y-3">
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Help Center</a></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Documentation</a></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Terms of Service</a></li>
                            <li><a href="#" class="text-[11px] font-medium text-slate-500 hover:text-emerald-600 transition-colors">Status</a></li>
                        </ul>
                    </div>
                </div>
                <div class="border-t border-slate-200/60 pt-8 flex flex-col md:flex-row justify-center items-center">
                    <p class="text-[10px] text-slate-400 font-medium">
                        &copy; {{ new Date().getFullYear() }} MediSaaS. All rights reserved.
                    </p>
                </div>
            </div>
        </footer>
    </div>
</template>
