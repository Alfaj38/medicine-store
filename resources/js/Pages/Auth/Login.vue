<script setup>
import { useForm, Head } from '@inertiajs/vue3';
import { ref } from 'vue';

const form = useForm({
    email: '',
    password: '',
    remember: false,
});

const activeTab = ref('company'); // 'management' or 'company'

const submit = () => {
    form.transform((data) => ({
        ...data,
        login_type: activeTab.value,
    })).post(route('login'), {
        onFinish: () => form.reset('password'),
    });
};
</script>

<template>
    <Head title="Log in - MediSaaS" />
    
    <!-- Light mint background pattern (approximated with a CSS pattern) -->
    <div class="min-h-screen bg-[#e8f7f2] flex items-center justify-center font-sans p-4" 
         style="background-image: radial-gradient(#d1ebe1 1px, transparent 1px); background-size: 20px 20px;">
        
        <!-- Main Card Container -->
        <div class="w-full max-w-[1000px] bg-white rounded-[2rem] shadow-2xl overflow-hidden flex flex-col md:flex-row min-h-[600px]">
            
            <!-- Left Side: Login Form -->
            <div class="w-full md:w-1/2 p-10 lg:p-14 flex flex-col justify-center bg-white relative">
                
                <div class="text-center mb-8">
                    <h1 class="text-3xl font-bold text-slate-800 tracking-tight">Log in to</h1>
                    <h2 class="text-3xl font-bold text-[#344b8b] mt-1">{{ activeTab === 'management' ? 'Platform Admin' : 'Company Portal' }}</h2>
                </div>

                <!-- Tabs -->
                <div class="flex p-1 bg-slate-50 rounded-xl mb-6">
                    <button 
                        @click="activeTab = 'management'"
                        :class="activeTab === 'management' ? 'bg-[#344b8b] text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        Management Login
                    </button>
                    <button 
                        @click="activeTab = 'company'"
                        :class="activeTab === 'company' ? 'bg-[#344b8b] text-white shadow-sm' : 'text-slate-500 hover:text-slate-700'"
                        class="flex-1 py-2.5 text-sm font-semibold rounded-lg transition-all flex items-center justify-center gap-2"
                    >
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                        Company Login
                    </button>
                </div>

                <div class="text-center text-xs text-slate-500 font-medium mb-8">
                    Don't have an account? <a :href="route('register')" class="text-[#344b8b] font-bold hover:underline">Register here</a>
                </div>

                <form @submit.prevent="submit" class="space-y-5">
                    <div>
                        <label for="email" class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Email / Phone Number</label>
                        <input 
                            id="email" 
                            type="text" 
                            v-model="form.email" 
                            required 
                            autofocus 
                            class="w-full bg-[#f0f4f8] border-2 border-emerald-500 text-slate-900 rounded-xl px-4 py-3.5 focus:outline-none focus:ring-0 transition-colors font-medium text-sm" 
                            placeholder="admin@medisaas.com"
                        />
                        <p v-if="form.errors.email" class="text-red-500 text-xs mt-1.5 ml-1">{{ form.errors.email }}</p>
                    </div>

                    <div>
                        <label for="password" class="block text-xs font-semibold text-slate-600 mb-1.5 ml-1">Password</label>
                        <div class="relative">
                            <input 
                                id="password" 
                                type="password" 
                                v-model="form.password" 
                                required 
                                class="w-full bg-[#f0f4f8] border-2 border-transparent text-slate-900 rounded-xl px-4 py-3.5 focus:border-emerald-500 focus:outline-none focus:ring-0 transition-colors font-medium text-sm" 
                                placeholder="••••••••"
                            />
                            <div class="absolute inset-y-0 right-0 pr-4 flex items-center pointer-events-none">
                                <svg class="w-5 h-5 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                            </div>
                        </div>
                        <p v-if="form.errors.password" class="text-red-500 text-xs mt-1.5 ml-1">{{ form.errors.password }}</p>
                    </div>

                    <div class="flex items-center justify-between mt-2">
                        <label class="flex items-center cursor-pointer group">
                            <input type="checkbox" v-model="form.remember" class="w-4 h-4 rounded border-slate-300 text-[#344b8b] focus:ring-[#344b8b] cursor-pointer" />
                            <span class="ml-2 text-xs font-medium text-slate-500">Remember Me</span>
                        </label>

                        <a href="#" class="text-xs font-bold text-slate-500 hover:text-[#344b8b] transition-colors">Forgot Your Password?</a>
                    </div>

                    <div class="pt-4">
                        <button 
                            type="submit" 
                            :disabled="form.processing"
                            class="w-32 bg-white border-2 border-emerald-500 text-emerald-600 rounded-full py-2.5 text-sm font-bold flex items-center justify-center gap-2 hover:bg-emerald-50 transition-colors disabled:opacity-50"
                        >
                            <span v-if="form.processing">Wait...</span>
                            <template v-else>
                                Login
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </template>
                        </button>
                    </div>
                </form>
            </div>
            
            <!-- Right Side: Dark Green Info Card -->
            <div class="w-full md:w-1/2 bg-[#125c34] p-8 lg:p-12 flex items-center justify-center relative">
                
                <!-- Inner darker card -->
                <div class="w-full h-full bg-[#12422b] rounded-3xl p-10 flex flex-col justify-between text-white relative overflow-hidden shadow-2xl">
                    
                    <!-- Decorative subtle background shapes can go here -->
                    <div class="absolute top-0 right-0 -mr-20 -mt-20 w-64 h-64 bg-white opacity-5 rounded-full blur-3xl"></div>
                    <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-64 h-64 bg-emerald-400 opacity-5 rounded-full blur-3xl"></div>

                    <div>
                        <!-- Empty spacer for top -->
                    </div>

                    <!-- Center Content -->
                    <div class="text-center z-10">
                        <h3 class="text-xl font-medium mb-3 tracking-wide">Welcome to</h3>
                        <div class="flex items-center justify-center gap-3 mb-6">
                            <div class="w-10 h-10 rounded-full border-4 border-emerald-400 flex items-center justify-center border-t-transparent animate-[spin_3s_linear_infinite]">
                                <div class="w-4 h-4 bg-emerald-400 rounded-full"></div>
                            </div>
                            <h2 class="text-4xl font-bold tracking-wider">MediSaaS</h2>
                        </div>
                        <p class="text-emerald-100 text-sm font-medium">Pharmacy & ERP solutions to suit your business</p>
                    </div>

                    <!-- Bottom Contact Info -->
                    <div class="z-10 mt-16 pt-6 border-t border-emerald-800/50 flex flex-col xl:flex-row justify-between items-center gap-4">
                        <div class="flex flex-col gap-2 text-xs text-emerald-100">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path></svg>
                                +880 1711 314 600
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4 text-emerald-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                                sales@erp.com
                            </div>
                        </div>
                        
                        <div class="flex gap-3">
                            <a href="#" class="w-8 h-8 rounded-full bg-emerald-900/50 flex items-center justify-center hover:bg-emerald-800 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-full bg-emerald-900/50 flex items-center justify-center hover:bg-emerald-800 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 0h-14c-2.761 0-5 2.239-5 5v14c0 2.761 2.239 5 5 5h14c2.762 0 5-2.239 5-5v-14c0-2.761-2.238-5-5-5zm-11 19h-3v-11h3v11zm-1.5-12.268c-.966 0-1.75-.79-1.75-1.764s.784-1.764 1.75-1.764 1.75.79 1.75 1.764-.783 1.764-1.75 1.764zm13.5 12.268h-3v-5.604c0-3.368-4-3.113-4 0v5.604h-3v-11h3v1.765c1.396-2.586 7-2.777 7 2.476v6.759z"/></svg>
                            </a>
                            <a href="#" class="w-8 h-8 rounded-full bg-emerald-900/50 flex items-center justify-center hover:bg-emerald-800 transition-colors">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M23.498 6.186a3.016 3.016 0 0 0-2.122-2.136C19.505 3.545 12 3.545 12 3.545s-7.505 0-9.377.505A3.017 3.017 0 0 0 .502 6.186C0 8.07 0 12 0 12s0 3.93.502 5.814a3.016 3.016 0 0 0 2.122 2.136c1.871.505 9.376.505 9.376.505s7.505 0 9.377-.505a3.015 3.015 0 0 0 2.122-2.136C24 15.93 24 12 24 12s0-3.93-.502-5.814zM9.545 15.568V8.432L15.818 12l-6.273 3.568z"/></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</template>
