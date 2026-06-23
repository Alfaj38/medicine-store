<script setup>
import { Head, Link, router } from '@inertiajs/vue3';
import PublicLayout from '@/Layouts/PublicLayout.vue';
import { ref, computed, onMounted, watch } from 'vue';
import axios from 'axios';

const props = defineProps({
    pharmacies: {
        type: Array,
        default: () => []
    },
    filters: {
        type: Object,
        default: () => ({ search: '' })
    }
});

const searchQuery = ref(props.filters?.search || '');
const locationQuery = ref(props.filters?.location || '');
const topRated = ref(props.filters?.top_rated === true || props.filters?.top_rated === '1');
const showMoreFilters = ref(false);
const showLocationDropdown = ref(false);

const divisions = ref([]);
const districts = ref([]);
const upazilas = ref([]);

const selectedDivision = ref('');
const selectedDistrict = ref('');
const selectedUpazila = ref('');

onMounted(async () => {
    try {
        const response = await axios.get(route('locations.divisions'));
        divisions.value = response.data;
    } catch (error) {
        console.error('Error fetching divisions:', error);
    }
});

watch(selectedDivision, async (newVal) => {
    selectedDistrict.value = '';
    selectedUpazila.value = '';
    districts.value = [];
    upazilas.value = [];
    updateLocationQuery();
    if (newVal) {
        try {
            const response = await axios.get(route('locations.districts', newVal));
            districts.value = response.data;
        } catch (error) {
            console.error('Error fetching districts:', error);
        }
    }
});

watch(selectedDistrict, async (newVal) => {
    selectedUpazila.value = '';
    upazilas.value = [];
    updateLocationQuery();
    if (newVal) {
        try {
            const response = await axios.get(route('locations.upazilas', newVal));
            upazilas.value = response.data;
        } catch (error) {
            console.error('Error fetching upazilas:', error);
        }
    }
});

watch(selectedUpazila, () => {
    updateLocationQuery();
});

const updateLocationQuery = () => {
    let parts = [];
    if (selectedUpazila.value) {
        const upa = upazilas.value.find(u => u.id === selectedUpazila.value);
        if (upa) parts.push(upa.name);
    }
    if (selectedDistrict.value) {
        const dist = districts.value.find(d => d.id === selectedDistrict.value);
        if (dist) parts.push(dist.name);
    }
    if (selectedDivision.value && !selectedDistrict.value) {
        const div = divisions.value.find(d => d.id === selectedDivision.value);
        if (div) parts.push(div.name);
    }
    
    locationQuery.value = parts.length > 0 ? parts[0] : '';
    
    // Auto-search if at least a district is selected, or if completely cleared
    if (selectedDistrict.value || (!selectedDivision.value && !selectedDistrict.value && !selectedUpazila.value)) {
        handleSearch();
    }
};

const handleSearch = () => {
    router.get(route('home'), { 
        search: searchQuery.value,
        location: locationQuery.value,
        top_rated: topRated.value ? 1 : 0
    }, {
        preserveState: true,
        preserveScroll: true,
        only: ['pharmacies']
    });
};

const clearSearch = () => {
    searchQuery.value = '';
    handleSearch();
};

const items = ref([
    { id: 1, name: 'Napa Extra', desc: 'Paracetamol 500mg', price: 2.50, color: 'emerald' },
    { id: 2, name: 'Seclo 20mg', desc: 'Omeprazole', price: 5.00, color: 'blue' },
    { id: 3, name: 'Alatrol', desc: 'Cetirizine 10mg', price: 1.50, color: 'orange' },
    { id: 4, name: 'Fexo 120mg', desc: 'Fexofenadine', price: 3.00, color: 'purple' },
    { id: 5, name: 'Ceevit', desc: 'Vitamin C 250mg', price: 1.20, color: 'rose' },
]);

const cart = ref([
    { item: items.value[0], qty: 1 },
    { item: items.value[1], qty: 2 }
]);

const addToCart = (item) => {
    const existing = cart.value.find(c => c.item.id === item.id);
    if (existing) {
        existing.qty++;
    } else {
        cart.value.push({ item, qty: 1 });
    }
};

const subtotal = computed(() => cart.value.reduce((acc, c) => acc + (c.item.price * c.qty), 0));
</script>

<template>
    <Head title="Pharmacy POS & ERP Software" />
    <PublicLayout>
        
        <!-- Hero Section -->
        <div class="relative bg-slate-50/50 pt-20 pb-24 overflow-hidden">
            <!-- Background Decoration -->
            <div class="absolute top-0 right-0 -mr-48 -mt-48 w-[800px] h-[800px] bg-emerald-50 rounded-full blur-3xl opacity-60"></div>
            <div class="absolute bottom-0 left-0 -ml-48 -mb-48 w-[600px] h-[600px] bg-teal-50 rounded-full blur-3xl opacity-60"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 relative z-10">
                <div class="lg:grid lg:grid-cols-12 lg:gap-12 items-center">
                    
                    <!-- Left Content -->
                    <div class="lg:col-span-5 mb-16 lg:mb-0">
                        <div class="inline-flex items-center px-3 py-1.5 rounded-md bg-emerald-100 text-emerald-800 text-[11px] font-bold tracking-wide uppercase mb-6">
                            All-in-One Pharmacy Management System
                        </div>
                        <h1 class="text-3xl sm:text-4xl lg:text-[46px] font-extrabold text-slate-900 leading-[1.2] mb-6 tracking-tight">
                            The Complete Cloud<br/>
                            POS & ERP for<br/>
                            <span class="text-emerald-500">Modern Pharmacies</span>
                        </h1>
                        <p class="text-sm text-slate-600 mb-8 max-w-lg leading-relaxed font-medium">
                            Manage sales, inventory, purchases, and multi-branches effortlessly. Increase efficiency, reduce losses, and grow your pharmacy business.
                        </p>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4 mb-8">
                            <Link :href="route('register')" class="w-full sm:w-auto bg-emerald-500 hover:bg-emerald-600 text-white text-sm font-bold px-8 py-3.5 rounded-lg shadow-lg shadow-emerald-500/20 transition-all text-center">
                                Start Free Trial
                            </Link>
                            <Link :href="route('book-demo')" class="w-full sm:w-auto bg-white border-2 border-slate-200 hover:border-slate-300 text-slate-700 text-sm font-bold px-8 py-3.5 rounded-lg transition-all flex items-center justify-center gap-2">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path d="M4 4l12 6-12 6V4z"/></svg>
                                Book a Demo
                            </Link>
                        </div>
                        
                        <div class="flex flex-wrap items-center gap-x-6 gap-y-3 text-[13px] font-bold text-slate-500">
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center"><svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg></div>
                                <span>No Credit Card<br/>Required</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center"><svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                <span>Setup in<br/>5 Minutes</span>
                            </div>
                            <div class="flex items-center gap-1.5">
                                <div class="w-5 h-5 rounded-full bg-slate-100 flex items-center justify-center"><svg class="w-3 h-3 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg></div>
                                <span>Trusted by<br/>500+ Pharmacies</span>
                            </div>
                        </div>
                    </div>

                    <!-- Right Graphic -->
                    <div class="lg:col-span-7 relative">
                        <div class="relative w-full aspect-[4/3] transform rotate-[4deg] hover:rotate-0 transition-transform duration-700 ease-out">
                            <!-- Floating Stats -->
                            <div class="absolute -top-6 left-12 bg-white rounded-xl shadow-xl shadow-slate-200/50 p-3 flex items-center gap-3 z-20 border border-slate-100 animate-bounce-slow">
                                <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">💰</div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Today's Sales</div>
                                    <div class="text-sm font-extrabold text-slate-800">$4,285.00 <span class="text-[9px] text-emerald-500 font-bold">+15.3%</span></div>
                                </div>
                            </div>
                            <div class="absolute top-10 right-4 bg-white rounded-xl shadow-xl shadow-slate-200/50 p-3 flex items-center gap-3 z-20 border border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-orange-50 flex items-center justify-center text-orange-500">⚠️</div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Low Stock Items</div>
                                    <div class="text-sm font-extrabold text-orange-500">24 <span class="text-[9px] text-slate-400 font-bold ml-1 cursor-pointer hover:underline">View All</span></div>
                                </div>
                            </div>
                            <div class="absolute top-32 -right-6 bg-white rounded-xl shadow-xl shadow-slate-200/50 p-3 flex items-center gap-3 z-20 border border-slate-100">
                                <div class="w-8 h-8 rounded-full bg-red-50 flex items-center justify-center text-red-500">🛑</div>
                                <div>
                                    <div class="text-[9px] font-bold text-slate-400 uppercase tracking-wide">Expiry Alert</div>
                                    <div class="text-sm font-extrabold text-red-500">18 <span class="text-[9px] text-slate-400 font-bold ml-1 cursor-pointer hover:underline">View All</span></div>
                                </div>
                            </div>

                            <!-- Main App Mockup -->
                            <div class="absolute inset-0 bg-white rounded-2xl shadow-2xl shadow-slate-300/60 border border-slate-200 overflow-hidden flex flex-col z-10">
                                <!-- Mockup Header -->
                                <div class="h-10 border-b border-slate-100 flex items-center px-4 justify-between bg-slate-50">
                                    <div class="flex items-center gap-2">
                                        <div class="w-5 h-5 bg-emerald-500 rounded text-white flex items-center justify-center text-[10px] font-bold">+</div>
                                        <span class="text-xs font-bold text-slate-700">SaaSMedi</span>
                                    </div>
                                    <div class="w-64 h-6 bg-white border border-slate-200 rounded text-[9px] text-slate-400 flex items-center px-2 shadow-inner">
                                        🔍 Search barcode or medicine...
                                    </div>
                                    <div class="flex gap-2">
                                        <div class="w-6 h-6 rounded-full bg-slate-200"></div>
                                        <div class="w-6 h-6 rounded-full bg-emerald-100"></div>
                                    </div>
                                </div>
                                <!-- Mockup Body -->
                                <div class="flex flex-1 overflow-hidden">
                                    <!-- Sidebar -->
                                    <div class="w-16 bg-slate-900 border-r border-slate-800 flex flex-col items-center py-4 gap-4">
                                        <div class="w-8 h-8 rounded-lg bg-emerald-500/20 text-emerald-400 flex items-center justify-center text-xs">🏠</div>
                                        <div class="w-8 h-8 rounded-lg text-slate-500 flex items-center justify-center text-xs">📦</div>
                                        <div class="w-8 h-8 rounded-lg text-slate-500 flex items-center justify-center text-xs">👥</div>
                                        <div class="w-8 h-8 rounded-lg text-slate-500 flex items-center justify-center text-xs">📈</div>
                                    </div>
                                    <!-- Content Area -->
                                    <div class="flex-1 p-4 bg-slate-50 flex gap-4">
                                        <!-- Items List -->
                                        <div class="flex-1 bg-white rounded-lg border border-slate-100 p-3 flex flex-col gap-2">
                                            <div class="flex justify-between items-center pb-2 border-b border-slate-50">
                                                <span class="text-[10px] font-bold text-slate-600">Medicine (Click to Add)</span>
                                                <span class="text-[10px] font-bold text-slate-600">Price</span>
                                            </div>
                                            <div v-for="item in items" :key="item.id" @click="addToCart(item)" class="flex justify-between items-center py-1.5 px-2 rounded hover:bg-slate-50 cursor-pointer transition-colors group">
                                                <div>
                                                    <div class="text-[10px] font-bold text-slate-800 group-hover:text-emerald-600 transition-colors">{{ item.name }}</div>
                                                    <div class="text-[8px] text-slate-400">{{ item.desc }}</div>
                                                </div>
                                                <div class="text-[10px] font-bold text-emerald-600">${{ item.price.toFixed(2) }}</div>
                                            </div>
                                        </div>
                                        <!-- Current Sale -->
                                        <div class="w-48 bg-white rounded-lg border border-slate-100 p-3 flex flex-col">
                                            <div class="text-[11px] font-bold text-slate-800 border-b border-slate-100 pb-2 mb-2 flex justify-between">
                                                <span>Current Sale</span>
                                                <span class="text-slate-400 cursor-pointer hover:text-red-500" @click="cart = []">Clear</span>
                                            </div>
                                            <div class="flex-1 flex flex-col gap-2 overflow-y-auto min-h-[100px]">
                                                <div v-if="cart.length === 0" class="text-[9px] text-slate-400 text-center py-4">Cart is empty</div>
                                                <div v-for="(c, idx) in cart" :key="idx" class="flex justify-between items-center animate-pulse-once">
                                                    <div class="flex items-center gap-1.5">
                                                        <div :class="`w-5 h-5 rounded bg-${c.item.color}-100 text-${c.item.color}-600 flex items-center justify-center text-[8px]`">💊</div>
                                                        <div>
                                                            <div class="text-[9px] font-bold text-slate-700">{{ c.item.name }}</div>
                                                            <div class="text-[7px] text-slate-400">{{ c.qty }} x ${{ c.item.price.toFixed(2) }}</div>
                                                        </div>
                                                    </div>
                                                    <div class="text-[9px] font-bold text-slate-700">${{ (c.item.price * c.qty).toFixed(2) }}</div>
                                                </div>
                                            </div>
                                            <div class="pt-2 border-t border-slate-100 mt-2">
                                                <div class="flex justify-between items-center mb-1">
                                                    <span class="text-[10px] text-slate-500 font-medium">Subtotal</span>
                                                    <span class="text-[10px] font-bold text-slate-700">${{ subtotal.toFixed(2) }}</span>
                                                </div>
                                                <div class="flex justify-between items-center mb-3">
                                                    <span class="text-[10px] text-slate-500 font-medium">Tax</span>
                                                    <span class="text-[10px] font-bold text-slate-700">$0.00</span>
                                                </div>
                                                <div class="flex justify-between items-end mb-3">
                                                    <span class="text-xs font-bold text-slate-800">Total</span>
                                                    <span class="text-lg font-black text-slate-900">${{ subtotal.toFixed(2) }}</span>
                                                </div>
                                                <button class="w-full bg-emerald-500 hover:bg-emerald-600 transition-colors text-white text-[10px] font-bold py-2 rounded-md shadow-sm">Pay (F9)</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Featured Pharmacies Section (Improved) -->
        <div class="pt-20 pb-16 bg-[#f8fdfb]">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Header Section -->
                <div class="flex flex-col md:flex-row justify-between items-center mb-12">
                    <div class="max-w-2xl">
                        <div class="inline-flex items-center gap-1.5 px-3 py-1.5 rounded-full bg-emerald-50 text-emerald-600 text-sm font-bold border border-emerald-100 mb-6">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M12 2C6.48 2 2 6.48 2 12s4.48 10 10 10 10-4.48 10-10S17.52 2 12 2zm-1 14.5v-5A1.5 1.5 0 0112.5 10h1a1.5 1.5 0 011.5 1.5v5H11z"/></svg>
                            Trusted by Thousands
                        </div>
                        <h2 class="text-4xl md:text-5xl font-black text-slate-900 mb-4 tracking-tight">Our Network of Pharmacies</h2>
                        <p class="text-base md:text-lg text-slate-600 font-medium">Discover and order medicines & health products from trusted pharmacies near you.</p>
                    </div>
                    <div class="hidden md:block">
                        <img src="/images/medicine_sample.png" alt="SaaSMedi Pharmacy" class="h-32 object-contain drop-shadow-xl" />
                    </div>
                </div>

                <!-- Search Bar -->
                <div class="bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.04)] border border-slate-100 p-2.5 mb-12 flex flex-col md:flex-row items-center gap-3 relative z-20">
                    <div class="flex-1 flex items-center px-4 w-full md:border-r border-slate-100">
                        <svg class="w-5 h-5 text-emerald-500 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                        <input v-model="searchQuery" @keyup.enter="handleSearch" type="text" placeholder="Search pharmacy name, area or location..." class="w-full bg-transparent border-0 focus:ring-0 text-sm text-slate-700 placeholder-slate-400 py-2.5 outline-none font-medium" />
                        
                        <button v-if="searchQuery.length > 0" @click="clearSearch" class="text-slate-400 hover:text-slate-600 p-1 mr-1 rounded-full transition-colors flex-shrink-0" title="Clear search">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>

                        <button @click="handleSearch" class="bg-emerald-600 hover:bg-emerald-500 text-white px-6 py-2.5 rounded-xl text-sm font-bold transition-colors shadow-sm ml-2 whitespace-nowrap">
                            Search
                        </button>
                    </div>
                    
                    <div class="flex flex-wrap items-center gap-3 px-2 w-full md:w-auto py-1">
                        <div class="relative">
                            <button @click="showLocationDropdown = !showLocationDropdown" :class="[showLocationDropdown || locationQuery ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700']" class="flex items-center gap-2 px-4 py-2 border rounded-xl text-sm font-bold transition-colors whitespace-nowrap">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"/></svg>
                                {{ locationQuery ? (locationQuery.length > 15 ? locationQuery.substring(0, 15) + '...' : locationQuery) : 'All Locations' }}
                                <svg class="w-3 h-3 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </button>
                            
                            <!-- Location Dropdown Panel -->
                            <div v-if="showLocationDropdown" class="absolute left-0 top-full mt-2 w-72 bg-white rounded-xl shadow-xl border border-slate-100 p-4 z-50 flex flex-col gap-3">
                                <div class="flex justify-between items-center mb-1">
                                    <h4 class="text-xs font-bold text-slate-800 uppercase tracking-wide">Select Location</h4>
                                    <button @click="selectedDivision = ''; selectedDistrict = ''; selectedUpazila = ''; showLocationDropdown = false; handleSearch();" class="text-[10px] text-red-500 hover:text-red-700 font-bold">Clear</button>
                                </div>
                                
                                <div class="flex flex-col gap-1">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Division</label>
                                    <select v-model="selectedDivision" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                                        <option value="">Any Division</option>
                                        <option v-for="div in divisions" :key="div.id" :value="div.id">{{ div.name }}</option>
                                    </select>
                                </div>
                                
                                <div class="flex flex-col gap-1" v-if="selectedDivision">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">District</label>
                                    <select v-model="selectedDistrict" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                                        <option value="">Any District</option>
                                        <option v-for="dist in districts" :key="dist.id" :value="dist.id">{{ dist.name }}</option>
                                    </select>
                                </div>
                                
                                <div class="flex flex-col gap-1" v-if="selectedDistrict">
                                    <label class="text-[10px] font-bold text-slate-500 uppercase tracking-wider ml-1">Upazila / Area</label>
                                    <select v-model="selectedUpazila" class="w-full bg-slate-50 border border-slate-200 rounded-lg px-3 py-2 text-sm font-medium text-slate-700 focus:ring-2 focus:ring-emerald-500 focus:border-emerald-500 outline-none">
                                        <option value="">Any Upazila</option>
                                        <option v-for="upa in upazilas" :key="upa.id" :value="upa.id">{{ upa.name }}</option>
                                    </select>
                                </div>
                                
                                <button @click="showLocationDropdown = false; handleSearch();" class="mt-2 w-full bg-emerald-600 hover:bg-emerald-500 text-white rounded-lg py-2 text-xs font-bold transition-colors">
                                    Apply Location
                                </button>
                            </div>
                        </div>
                        
                        <button @click="topRated = !topRated; handleSearch()" :class="[topRated ? 'bg-emerald-50 border-emerald-200 text-emerald-700' : 'bg-white hover:bg-slate-50 border-slate-200 text-slate-700']" class="flex items-center gap-2 px-4 py-2 border rounded-xl text-sm font-bold transition-colors whitespace-nowrap">
                            <svg class="w-4 h-4 text-emerald-600" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                            Top Rated
                        </button>
                        
                        <div class="relative">
                            <button @click="showMoreFilters = !showMoreFilters" :class="[showMoreFilters ? 'bg-slate-100 border-slate-300' : 'bg-white hover:bg-slate-50 border-slate-200']" class="flex items-center gap-2 px-4 py-2 border rounded-xl text-sm font-bold text-slate-700 transition-colors whitespace-nowrap">
                                <svg class="w-4 h-4 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6V4m0 2a2 2 0 100 4m0-4a2 2 0 110 4m-6 8a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4m6 6v10m6-2a2 2 0 100-4m0 4a2 2 0 110-4m0 4v2m0-6V4"/></svg>
                                More Filters
                            </button>
                            <div v-if="showMoreFilters" class="absolute right-0 top-full mt-2 w-48 bg-white rounded-xl shadow-lg border border-slate-100 p-4 z-50">
                                <p class="text-xs text-slate-500 text-center font-medium">Advanced filters coming soon!</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Carousel Title and Link -->
                <div class="flex justify-between items-end mb-6">
                    <div class="flex items-center gap-3">
                        <div class="w-8 h-8 rounded bg-emerald-50 text-emerald-600 flex items-center justify-center">
                            <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 24 24"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2V9H8v3H5v2h3v3h2v-3h2v-2z"/></svg>
                        </div>
                        <div>
                            <h3 class="text-xl font-extrabold text-slate-900">Popular Pharmacies</h3>
                            <p class="text-sm font-medium text-slate-500">Top rated pharmacies in your area</p>
                        </div>
                    </div>
                    <Link :href="route('home')" class="hidden md:flex items-center gap-1 bg-white hover:bg-slate-50 border border-slate-200 text-emerald-600 text-[13px] font-bold px-4 py-2 rounded-xl transition-colors">
                        View All Pharmacies
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>

                <!-- Pharmacy Cards Carousel -->
                <div v-if="pharmacies && pharmacies.length > 0" class="relative group">
                    <!-- Scroll Left Arrow (Mock) -->
                    <button class="absolute -left-5 top-1/2 -translate-y-1/2 w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-600 shadow-lg z-10 hidden md:group-hover:flex hover:text-emerald-600 hover:border-emerald-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"/></svg>
                    </button>
                    
                    <div class="flex overflow-x-auto gap-6 pb-6 pt-2 snap-x snap-mandatory scrollbar-hide px-1" style="scrollbar-width: none;">
                        <div 
                            v-for="(pharmacy, index) in pharmacies" 
                            :key="pharmacy.id"
                            class="min-w-[280px] w-[280px] md:min-w-[300px] md:w-[300px] bg-white rounded-2xl shadow-[0_8px_30px_rgb(0,0,0,0.06)] border border-slate-100 overflow-hidden snap-start flex-shrink-0 flex flex-col hover:-translate-y-1 transition-transform duration-300 relative group/card"
                        >
                            <!-- Rating Badge -->
                            <div class="absolute top-3 right-3 bg-white/90 backdrop-blur-sm px-2.5 py-1 rounded-md text-[11px] font-black text-slate-800 shadow-sm z-10 flex items-center gap-1 border border-white/50">
                                <svg class="w-3 h-3 text-emerald-500" fill="currentColor" viewBox="0 0 20 20"><path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/></svg>
                                {{ 4.5 + (index % 5) * 0.1 }}
                            </div>
                            
                            <!-- Cover Image -->
                            <div class="h-32 bg-slate-800 relative flex items-center justify-center overflow-hidden">
                                <img src="https://images.unsplash.com/photo-1586281380349-632531db7ed4?auto=format&fit=crop&w=400&q=80" class="absolute inset-0 w-full h-full object-cover opacity-60" />
                                <div class="relative z-10 text-white font-black text-xl uppercase tracking-wider text-center px-4 drop-shadow-md">
                                    {{ pharmacy.name }}
                                </div>
                            </div>
                            
                            <!-- Content -->
                            <div class="p-5 pt-0 relative flex-1 flex flex-col">
                                <!-- Floating Logo -->
                                <div class="w-14 h-14 bg-white rounded-2xl shadow-md border border-slate-100 flex items-center justify-center -mt-7 mb-3 relative z-10 overflow-hidden">
                                    <img v-if="pharmacy.logo" :src="'/storage/' + pharmacy.logo" :alt="pharmacy.name" class="w-full h-full object-cover">
                                    <div v-else class="w-full h-full bg-emerald-500 text-white flex items-center justify-center font-black text-2xl pb-1">
                                        +
                                    </div>
                                </div>
                                
                                <h3 class="text-lg font-extrabold text-slate-900 truncate mb-1">{{ pharmacy.name }}</h3>
                                <p class="text-sm text-slate-500 mb-4 line-clamp-1 font-medium">{{ pharmacy.address || 'Address not provided' }}</p>
                                
                                <div class="space-y-2 mb-6">
                                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                        <span class="text-emerald-600">Open</span>
                                        <span class="text-slate-300">&bull;</span>
                                        <span>Closes 10:00 PM</span>
                                    </div>
                                    <div class="flex items-center gap-2 text-xs font-semibold text-slate-600">
                                        <svg class="w-3.5 h-3.5 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                                        <span>Home Delivery Available</span>
                                    </div>
                                </div>
                                
                                <div class="mt-auto">
                                    <Link :href="route('storefront.show', pharmacy.slug)" class="block w-full py-2.5 bg-emerald-50 hover:bg-emerald-100 text-emerald-700 text-center text-sm font-bold rounded-lg transition-colors border border-emerald-100">
                                        View Store &rarr;
                                    </Link>
                                </div>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Scroll Right Arrow (Mock) -->
                    <button class="absolute -right-5 top-1/2 -translate-y-1/2 w-10 h-10 bg-white border border-slate-200 rounded-full flex items-center justify-center text-slate-600 shadow-lg z-10 hidden md:group-hover:flex hover:text-emerald-600 hover:border-emerald-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </button>
                </div>
                
                <!-- Empty State -->
                <div v-else class="text-center py-16 bg-white rounded-2xl border border-slate-100 shadow-[0_8px_30px_rgb(0,0,0,0.04)] mb-8">
                    <div class="w-16 h-16 bg-emerald-50 text-emerald-500 rounded-full flex items-center justify-center mx-auto mb-4">
                        <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/></svg>
                    </div>
                    <h3 class="text-xl font-bold text-slate-900 mb-2">No pharmacies found</h3>
                    <p class="text-slate-500 text-[15px] max-w-md mx-auto mb-6">We couldn't find any pharmacies matching your current search or location. Try adjusting your filters.</p>
                    <button @click="clearSearch" class="bg-emerald-50 hover:bg-emerald-100 text-emerald-700 font-bold py-2.5 px-6 rounded-xl transition-colors border border-emerald-200">
                        Clear Search
                    </button>
                </div>
                
                <!-- Trusted Services Card View -->
                <div class="mt-16 bg-white border border-slate-200 rounded-2xl shadow-sm p-6 sm:p-8">
                    <!-- Header -->
                    <div class="flex flex-col sm:flex-row items-center justify-center gap-4 mb-6">
                        <span class="px-3 py-1 bg-emerald-100 text-emerald-700 text-xs font-bold rounded-full uppercase tracking-wider">Trusted Services</span>
                        <h2 class="text-lg sm:text-xl font-bold text-slate-800">Why customers choose SaaSMedi</h2>
                    </div>
                    
                    <hr class="border-slate-100 mb-8" />
                    
                    <!-- Cards -->
                    <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6 mb-8">
                        <!-- Card 1 -->
                        <div class="border border-slate-100 rounded-xl p-6 text-center hover:shadow-md transition-shadow bg-slate-50/50">
                            <div class="w-14 h-14 bg-emerald-200/50 text-emerald-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-2">100% Genuine Medicines</h3>
                            <p class="text-xs text-slate-500 font-medium">Verified pharmacies & authentic products only</p>
                        </div>
                        
                        <!-- Card 2 -->
                        <div class="border border-slate-100 rounded-xl p-6 text-center hover:shadow-md transition-shadow bg-slate-50/50">
                            <div class="w-14 h-14 bg-blue-200/50 text-blue-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 15v2m-6 4h12a2 2 0 002-2v-6a2 2 0 00-2-2H6a2 2 0 00-2 2v6a2 2 0 002 2zm10-10V7a4 4 0 00-8 0v4h8z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-2">Secure Payments</h3>
                            <p class="text-xs text-slate-500 font-medium">SSL protected checkout & trusted gateways</p>
                        </div>
                        
                        <!-- Card 3 -->
                        <div class="border border-slate-100 rounded-xl p-6 text-center hover:shadow-md transition-shadow bg-slate-50/50">
                            <div class="w-14 h-14 bg-orange-200/50 text-orange-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-2">Fast Home Delivery</h3>
                            <p class="text-xs text-slate-500 font-medium">Quick medicine delivery across your city</p>
                        </div>
                        
                        <!-- Card 4 -->
                        <div class="border border-slate-100 rounded-xl p-6 text-center hover:shadow-md transition-shadow bg-slate-50/50">
                            <div class="w-14 h-14 bg-purple-200/50 text-purple-700 rounded-xl flex items-center justify-center mx-auto mb-4">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 5.636l-3.536 3.536m0 5.656l3.536 3.536M9.172 9.172L5.636 5.636m3.536 9.192l-3.536 3.536M21 12a9 9 0 11-18 0 9 9 0 0118 0zm-5 0a4 4 0 11-8 0 4 4 0 018 0z"/></svg>
                            </div>
                            <h3 class="text-sm font-bold text-slate-900 mb-2">24/7 Customer Support</h3>
                            <p class="text-xs text-slate-500 font-medium">Always available for orders & assistance</p>
                        </div>
                    </div>
                    
                    <hr class="border-slate-100 mb-6" />
                    
                    <!-- Footer -->
                    <div class="flex flex-col gap-4">
                        <div class="flex items-center gap-2 text-sm font-bold text-slate-900">
                            <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            Trusted by 5,000+ customers
                        </div>
                        <div class="flex flex-wrap items-center gap-x-8 gap-y-3 text-xs text-slate-500 font-medium">
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M8.25 18.75a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h6m-9 0H3.375a1.125 1.125 0 0 1-1.125-1.125V14.25m17.25 4.5a1.5 1.5 0 0 1-3 0m3 0a1.5 1.5 0 0 0-3 0m3 0h1.125c.621 0 1.129-.504 1.09-1.124a17.902 17.902 0 0 0-3.213-9.193 2.056 2.056 0 0 0-1.58-.86H14.25M16.5 18.75h-2.25m0-11.177v-.958c0-.568-.422-1.048-.987-1.106a48.554 48.554 0 0 0-10.026 0 1.106 1.106 0 0 0-.987 1.106v7.635m12-6.677v6.677m0 4.5v-4.5m0 0h-12"/></svg>
                                Same-day delivery
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                                Multiple payment methods
                            </div>
                            <div class="flex items-center gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                Licensed pharmacies
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Problems We Solve Section -->
        <div class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Problems We Solve</h2>
                    <p class="text-sm text-slate-500 font-medium">We understand the daily challenges of running a pharmacy</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Card 1 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-red-50 flex items-center justify-center text-red-500 mb-5 border border-red-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Manual & Time Consuming</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Manual billing, stock counting and reports waste time and cause human errors.
                        </p>
                        <div class="text-[10px] font-bold text-red-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> Inefficient</div>
                    </div>
                    
                    <!-- Card 2 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-orange-50 flex items-center justify-center text-orange-500 mb-5 border border-orange-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Stock Loss & Expiry</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Untracked expiry and batch management leads to loss of money and trust.
                        </p>
                        <div class="text-[10px] font-bold text-red-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> High Loss</div>
                    </div>

                    <!-- Card 3 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-emerald-50 flex items-center justify-center text-emerald-500 mb-5 border border-emerald-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13.828 10.172a4 4 0 00-5.656 0l-4 4a4 4 0 105.656 5.656l1.102-1.101m-.758-4.899a4 4 0 005.656 0l4-4a4 4 0 00-5.656-5.656l-1.1 1.1"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Disconnected Branches</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Multiple branches, different systems and no real-time visibility.
                        </p>
                        <div class="text-[10px] font-bold text-emerald-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> No Control</div>
                    </div>

                    <!-- Card 4 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 shadow-[0_4px_20px_-4px_rgba(0,0,0,0.05)] flex flex-col">
                        <div class="w-12 h-12 rounded-lg bg-blue-50 flex items-center justify-center text-blue-500 mb-5 border border-blue-100">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-[13px] font-bold text-slate-900 mb-2">Complex & Expensive</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed mb-6 flex-1">
                            Traditional software is costly, complex to use and hard to maintain.
                        </p>
                        <div class="text-[10px] font-bold text-blue-500 flex items-center gap-1.5"><span class="text-[12px]">✕</span> High Cost</div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Why Choose SaaSMedi Section -->
        <div class="py-20 bg-slate-50/50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center max-w-2xl mx-auto mb-16">
                    <h2 class="text-2xl sm:text-3xl font-extrabold text-slate-900 mb-3">Why Choose SaaSMedi?</h2>
                    <p class="text-sm text-slate-500 font-medium">Powerful features to simplify your pharmacy operations</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
                    <!-- Feature 1 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Smart POS</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Lightning fast billing with barcode scanning, keyboard shortcuts and hold invoices.</p>
                    </div>

                    <!-- Feature 2 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-red-50 flex items-center justify-center text-red-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Batch & Expiry Tracking</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Track batch-wise stock, get expiry alerts and reduce stock wastage.</p>
                    </div>

                    <!-- Feature 3 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-blue-50 flex items-center justify-center text-blue-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Multi-Branch Management</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Manage unlimited branches, users and roles from a single dashboard.</p>
                    </div>

                    <!-- Feature 4 -->
                    <div class="bg-white rounded-xl border border-slate-100 p-6 text-center shadow-sm hover:shadow-md transition-shadow">
                        <div class="w-12 h-12 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500 mx-auto mb-4">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/></svg>
                        </div>
                        <h3 class="text-xs font-bold text-slate-900 mb-2">Real-time Reports</h3>
                        <p class="text-[11px] text-slate-500 leading-relaxed">Get real-time insights on sales, profit, stock and business performance.</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- CTA Section -->
        <div class="py-16 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="bg-gradient-to-r from-teal-700 to-emerald-500 rounded-2xl overflow-hidden shadow-2xl relative flex flex-col md:flex-row items-center">
                    
                    <div class="flex-1 p-10 md:p-14 z-10 relative">
                        <h2 class="text-3xl font-extrabold text-white mb-3">Ready to Transform Your Pharmacy?</h2>
                        <p class="text-emerald-100 font-medium mb-8">Join 500+ pharmacies already growing with SaaSMedi.</p>
                        
                        <div class="flex flex-col sm:flex-row items-center gap-4">
                            <Link :href="route('register')" class="bg-white text-emerald-700 hover:bg-slate-50 text-sm font-bold px-8 py-3 rounded-lg shadow-lg transition-all w-full sm:w-auto text-center">
                                Start Free Trial
                            </Link>
                            <Link :href="route('book-demo')" class="bg-transparent border border-emerald-300 text-white hover:bg-emerald-600 hover:border-emerald-600 text-sm font-bold px-8 py-3 rounded-lg transition-all flex items-center justify-center gap-2 w-full sm:w-auto">
                                <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM9.555 7.168A1 1 0 008 8v4a1 1 0 001.555.832l3-2a1 1 0 000-1.664l-3-2z" clip-rule="evenodd"/></svg>
                                Book a Demo
                            </Link>
                        </div>
                    </div>

                    <!-- Right Image Area -->
                    <div class="w-full md:w-5/12 h-64 md:h-auto self-stretch relative bg-teal-800">
                        <img src="https://images.unsplash.com/photo-1576602976047-174e57a47881?auto=format&fit=crop&q=80&w=800" alt="Pharmacist" class="absolute inset-0 w-full h-full object-cover opacity-90 object-top" />
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500/80 to-transparent md:hidden"></div>
                        <div class="absolute inset-0 bg-gradient-to-r from-emerald-500 via-transparent to-transparent hidden md:block w-32 -ml-1"></div>
                    </div>
                </div>
            </div>
        </div>

    </PublicLayout>
</template>
