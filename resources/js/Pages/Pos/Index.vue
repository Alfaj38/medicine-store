<script setup>
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import MedicineIcon from '@/Components/MedicineIcon.vue';

const props = defineProps({
    customers: Array,
    categories: Array,
    recentSales: Array,
});

// ─── State ────────────────────────────────────────────────────────────────────
const searchQuery       = ref('');
const cart              = ref([]);
const selectedCustomerId = ref('');
const selectedCategory  = ref(null);
const activeFilter      = ref('all');
const browseResults     = ref([]);
const searchResults     = ref([]);
const recentScans       = ref([]);
const isLoadingBrowse   = ref(false);
const isLoadingSearch   = ref(false);
const paymentMethod     = ref('cash');
const discountOpen      = ref(false);
const discountAmount    = ref(0);
const taxPercent        = ref(0);
const saleNote          = ref('');
const showRecentBills   = ref(false);
const isSubmitting      = ref(false);
const now               = ref(new Date());

// New Customer Modal
const showCustomerModal = ref(false);
const customerForm = useForm({
    name: '',
    phone: '',
    address: '',
    opening_balance: 0,
});
const customerList = ref([...props.customers]);

const openCustomerModal = () => {
    customerForm.reset();
    customerForm.clearErrors();
    showCustomerModal.value = true;
};

const closeCustomerModal = () => {
    showCustomerModal.value = false;
};

const saveCustomer = () => {
    customerForm.post(route('pos.quick-customer'), {
        preserveScroll: true,
        onSuccess: (page) => {
            const newCustomer = page.props.flash?.newCustomer;
            if (newCustomer) {
                customerList.value.unshift(newCustomer);
                selectedCustomerId.value = newCustomer.id;
            }
            closeCustomerModal();
        },
    });
};

// Favorites stored in localStorage
const favorites = ref(JSON.parse(localStorage.getItem('pos_favorites') || '[]'));

const toggleFavorite = (medicineId) => {
    const idx = favorites.value.indexOf(medicineId);
    if (idx === -1) favorites.value.push(medicineId);
    else favorites.value.splice(idx, 1);
    localStorage.setItem('pos_favorites', JSON.stringify(favorites.value));
};
const isFavorite = (medicineId) => favorites.value.includes(medicineId);

// Clock
let clockInterval;
onMounted(() => {
    clockInterval = setInterval(() => { now.value = new Date(); }, 1000);
    loadMedicines();
    window.addEventListener('keydown', handleKeyboard);
});
onUnmounted(() => {
    clearInterval(clockInterval);
    window.removeEventListener('keydown', handleKeyboard);
});

const currentTime = computed(() => now.value.toLocaleString('en-US', {
    month: 'numeric', day: 'numeric', year: 'numeric',
    hour: 'numeric', minute: '2-digit', hour12: true
}));

// ─── Medicine Browsing ─────────────────────────────────────────────────────────
const displayMedicines = computed(() => {
    if (searchQuery.value.trim()) return searchResults.value;
    if (activeFilter.value === 'favorites') {
        return browseResults.value.filter(m => favorites.value.includes(m.id));
    }
    return browseResults.value;
});

const loadMedicines = async () => {
    isLoadingBrowse.value = true;
    try {
        const params = new URLSearchParams();
        if (selectedCategory.value) params.set('category_id', selectedCategory.value);
        if (activeFilter.value !== 'favorites') params.set('filter', activeFilter.value);
        const res = await fetch(route('pos.browse') + '?' + params.toString());
        browseResults.value = await res.json();
    } finally {
        isLoadingBrowse.value = false;
    }
};

watch([selectedCategory, activeFilter], () => {
    if (activeFilter.value !== 'favorites') loadMedicines();
});

// Debounce search
let searchTimer;
watch(searchQuery, (val) => {
    clearTimeout(searchTimer);
    if (!val.trim()) { searchResults.value = []; return; }
    isLoadingSearch.value = true;
    searchTimer = setTimeout(async () => {
        const res = await fetch(route('pos.search', { q: val }));
        searchResults.value = await res.json();
        isLoadingSearch.value = false;
    }, 300);
});

// ─── Cart Logic ───────────────────────────────────────────────────────────────
const addToCart = (medicine) => {
    if (!medicine.inventory || medicine.inventory.length === 0) return;
    const batch = medicine.inventory[0];
    const existing = cart.value.find(i => i.medicine_id === medicine.id && i.batch_no === batch.batch_no);
    if (existing) {
        if (existing.quantity < batch.quantity) existing.quantity++;
    } else {
        cart.value.push({
            medicine_id: medicine.id,
            name: medicine.name,
            icon: medicine.icon,
            strength: medicine.strength,
            type: medicine.type,
            batch_no: batch.batch_no,
            expiry_date: batch.expiry_date ? batch.expiry_date.substring(0, 7) : '',
            available_qty: batch.quantity,
            quantity: 1,
            unit_price: parseFloat(batch.mrp || medicine.sell_price || 0),
        });
    }
    // Track recent scans
    if (!recentScans.value.find(r => r.id === medicine.id)) {
        recentScans.value.unshift({ id: medicine.id, name: medicine.name });
        if (recentScans.value.length > 6) recentScans.value.pop();
    }
    searchQuery.value = '';
};

const removeFromCart = (idx) => cart.value.splice(idx, 1);

const updateQty = (idx, delta) => {
    const item = cart.value[idx];
    const newQty = item.quantity + delta;
    if (newQty < 1) { removeFromCart(idx); return; }
    if (newQty > item.available_qty) return;
    item.quantity = newQty;
};

const clearCart = () => {
    cart.value = [];
    discountAmount.value = 0;
    taxPercent.value = 0;
    saleNote.value = '';
};

// ─── Totals ───────────────────────────────────────────────────────────────────
const subtotal = computed(() => cart.value.reduce((s, i) => s + i.unit_price * i.quantity, 0));
const taxAmount = computed(() => subtotal.value * (taxPercent.value / 100));
const total = computed(() => Math.max(0, subtotal.value - discountAmount.value + taxAmount.value));

// ─── Checkout ─────────────────────────────────────────────────────────────────
const completeSale = () => {
    if (!cart.value.length || isSubmitting.value) return;
    isSubmitting.value = true;

    const payload = {
        customer_id: selectedCustomerId.value || null,
        subtotal: subtotal.value,
        discount: discountAmount.value,
        tax: taxAmount.value,
        total_amount: total.value,
        paid_amount: total.value,
        payment_method: paymentMethod.value,
        notes: saleNote.value,
        items: cart.value.map(i => ({
            medicine_id: i.medicine_id,
            batch_no: i.batch_no,
            quantity: i.quantity,
            unit_price: i.unit_price,
            subtotal: i.unit_price * i.quantity,
        })),
    };

    router.post(route('pos.store'), payload, {
        onFinish: () => { isSubmitting.value = false; },
    });
};

const handleHoldInvoice = () => {
    window.alert('Hold invoice feature coming soon!');
};

// ─── Keyboard Shortcuts ───────────────────────────────────────────────────────
const handleKeyboard = (e) => {
    if (e.target.tagName === 'INPUT' || e.target.tagName === 'TEXTAREA') return;
    if (e.key === 'F5')  { e.preventDefault(); paymentMethod.value = 'cash'; }
    if (e.key === 'F6')  { e.preventDefault(); paymentMethod.value = 'card'; }
    if (e.key === 'F7')  { e.preventDefault(); paymentMethod.value = 'other'; }
    if (e.key === 'F8')  { e.preventDefault(); paymentMethod.value = 'credit'; }
    if (e.key === 'F9')  { e.preventDefault(); completeSale(); }
    if (e.key === 'F10') { e.preventDefault(); showRecentBills.value = !showRecentBills.value; }
    if (e.key === 'F2')  { e.preventDefault(); document.getElementById('pos-search')?.focus(); }
};

// ─── Pill icon by type ─────────────────────────────────────────────────────────
const typeColor = (type) => {
    const map = {
        'Tablet': 'bg-blue-100 text-blue-700',
        'Capsule': 'bg-purple-100 text-purple-700',
        'Syrup': 'bg-amber-100 text-amber-700',
        'Injection': 'bg-rose-100 text-rose-700',
        'Drop': 'bg-cyan-100 text-cyan-700',
        'Cream': 'bg-green-100 text-green-700',
        'Sachet': 'bg-orange-100 text-orange-700',
    };
    return map[type] || 'bg-slate-100 text-slate-700';
};

const typeIcon = (type) => {
    const map = {
        'Tablet': '💊',
        'Capsule': '💉',
        'Syrup': '🍶',
        'Injection': '💉',
        'Drop': '💧',
        'Cream': '🧴',
        'Sachet': '📦',
    };
    return map[type] || '💊';
};

const totalStock = (medicine) => {
    return (medicine.inventory || []).reduce((s, i) => s + i.quantity, 0);
};

// ─── Category icon by name ────────────────────────────────────────────────────
const categoryIcon = (name) => {
    const nameLower = (name || '').toLowerCase();
    if (nameLower.includes('pain')) return '🩹';
    if (nameLower.includes('antibiotic')) return '💊';
    if (nameLower.includes('vitamin') || nameLower.includes('supp')) return '🪫'; // close enough to pill box
    if (nameLower.includes('cough') || nameLower.includes('cold')) return '🧴';
    if (nameLower.includes('digest')) return '🫄'; // stomach-ish
    if (nameLower.includes('diabet')) return '💉';
    if (nameLower.includes('cardio')) return '❤️';
    if (nameLower.includes('derma')) return '🧴'; // ointment
    if (nameLower.includes('eye')) return '👁️';
    if (nameLower.includes('baby')) return '🍼';
    return '💊';
};
</script>

<template>
    <Head title="POS - MediSaaS" />

    <div class="flex flex-col h-screen bg-slate-50 text-slate-900 overflow-hidden select-none">

        <!-- ── TOP NAV ── -->
        <header class="flex items-center justify-between bg-white border-b border-slate-200 px-4 h-12 flex-shrink-0 z-20">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-emerald-500 flex items-center justify-center">
                        <span class="text-xs font-bold text-white">M</span>
                    </div>
                    <span class="font-bold text-sm text-slate-900 tracking-wide">MediSaaS POS</span>
                </div>
                <Link :href="route('dashboard')" class="text-xs text-slate-500 hover:text-slate-900 flex items-center gap-1 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Exit POS
                </Link>
            </div>
            <div class="flex items-center gap-6 text-xs text-slate-500">
                <span>{{ currentTime }}</span>
                <span class="text-emerald-600 font-semibold">Terminal 1</span>
                <div class="flex items-center gap-2">
                    <div class="w-6 h-6 rounded-full bg-emerald-600 flex items-center justify-center text-xs font-bold">P</div>
                    <span>Pharmacist</span>
                </div>
            </div>
        </header>

        <!-- ── MAIN BODY ── -->
        <div class="flex flex-1 overflow-hidden">

            <!-- ── LEFT SIDEBAR: Categories ── -->
            <aside class="w-56 bg-white border-r border-slate-200 flex flex-col flex-shrink-0 overflow-y-auto pt-4 px-3">
                <button
                    @click="selectedCategory = null; activeFilter = 'all'"
                    :class="['flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all rounded-xl mb-4 w-full text-left shadow-sm', selectedCategory === null && activeFilter === 'all' ? 'bg-emerald-600 text-white' : 'text-slate-700 bg-white border border-slate-200 hover:bg-slate-50']">
                    <span class="text-lg">💊</span> 
                    <span>All Categories</span>
                </button>

                <div class="space-y-1 pb-4">
                    <button v-for="cat in categories" :key="cat.id"
                        @click="selectedCategory = cat.id; activeFilter = 'all'"
                        :class="['flex items-center gap-3 px-4 py-2.5 text-sm transition-all rounded-xl w-full text-left', selectedCategory === cat.id ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm' : 'text-slate-600 bg-transparent hover:bg-slate-50 hover:text-slate-900']">
                        <span class="text-lg">{{ categoryIcon(cat.name) }}</span>
                        <span class="truncate">{{ cat.name }}</span>
                    </button>
                </div>
            </aside>

            <!-- ── CENTRE PANEL ── -->
            <main class="flex-1 flex flex-col overflow-hidden bg-slate-50">

                <!-- Search + Filter Tabs -->
                <div class="bg-white border-b border-slate-200 px-4 py-3 flex-shrink-0">
                    <div class="flex gap-3 items-center">
                        <!-- Search -->
                        <div class="relative flex-1">
                            <svg class="absolute left-3 top-1/2 -translate-y-1/2 w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                            <input
                                id="pos-search"
                                v-model="searchQuery"
                                type="text"
                                placeholder="Scan barcode or search medicine name, generic, manufacturer..."
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl pl-10 pr-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-colors"
                            />
                            <span v-if="isLoadingSearch" class="absolute right-3 top-1/2 -translate-y-1/2 text-xs text-slate-500 animate-pulse">Searching...</span>
                        </div>
                        <!-- Quick Add -->
                        <button class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            Quick Add <kbd class="ml-1 text-xs bg-emerald-700 px-1.5 py-0.5 rounded">F2</kbd>
                        </button>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-3 mt-3 px-1">
                        <!-- All Medicines -->
                        <button @click="activeFilter = 'all'; selectedCategory = null"
                            :class="['flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-[13px] font-semibold transition-all shadow-sm', activeFilter === 'all' ? 'bg-[#00a669] text-white border border-[#00a669]' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50']">
                            <svg class="w-4 h-4" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2v2H8v-2H6v-2h2V8h2v2h2v2z"/></svg>
                            All Medicines
                        </button>
                        
                        <!-- Favorites -->
                        <button @click="activeFilter = 'favorites'; selectedCategory = null"
                            :class="['flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-[13px] font-semibold transition-all shadow-sm', activeFilter === 'favorites' ? 'bg-slate-50 text-slate-900 border border-slate-300' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50']">
                            <svg class="w-4 h-4 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            Favorites
                        </button>

                        <!-- Low Stock -->
                        <button @click="activeFilter = 'low_stock'; selectedCategory = null"
                            :class="['flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-[13px] font-semibold transition-all shadow-sm', activeFilter === 'low_stock' ? 'bg-slate-50 text-slate-900 border border-slate-300' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50']">
                            <svg class="w-4 h-4 text-amber-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M10.29 3.86L1.82 18a2 2 0 0 0 1.71 3h16.94a2 2 0 0 0 1.71-3L13.71 3.86a2 2 0 0 0-3.42 0z"></path><line x1="12" y1="9" x2="12" y2="13"></line><line x1="12" y1="17" x2="12.01" y2="17"></line></svg>
                            Low Stock
                        </button>

                        <!-- Expiring Soon -->
                        <button @click="activeFilter = 'expiring_soon'; selectedCategory = null"
                            :class="['flex items-center gap-2 px-3.5 py-1.5 rounded-lg text-[13px] font-semibold transition-all shadow-sm', activeFilter === 'expiring_soon' ? 'bg-slate-50 text-slate-900 border border-slate-300' : 'bg-white text-slate-700 border border-slate-200 hover:bg-slate-50']">
                            <svg class="w-4 h-4 text-rose-500" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><rect x="3" y="4" width="18" height="18" rx="2" ry="2"></rect><line x1="16" y1="2" x2="16" y2="6"></line><line x1="8" y1="2" x2="8" y2="6"></line><line x1="3" y1="10" x2="21" y2="10"></line></svg>
                            Expiring Soon
                        </button>
                    </div>
                </div>

                <!-- Medicine Grid -->
                <div class="flex-1 overflow-y-auto p-4">
                    <div v-if="isLoadingBrowse" class="flex items-center justify-center h-40 text-slate-500">
                        <div class="animate-spin w-6 h-6 border-2 border-emerald-500 border-t-transparent rounded-full mr-3"></div>
                        Loading medicines...
                    </div>

                    <div v-else-if="displayMedicines.length === 0" class="flex flex-col items-center justify-center h-40 text-slate-600">
                        <span class="text-4xl mb-2">💊</span>
                        <p class="text-sm">{{ searchQuery ? 'No medicines found' : 'No stock available for this filter' }}</p>
                    </div>

                    <div v-else class="grid grid-cols-2 sm:grid-cols-3 lg:grid-cols-4 xl:grid-cols-4 gap-3">
                        <div
                            v-for="med in displayMedicines" :key="med.id"
                            @click="addToCart(med)"
                            class="relative bg-white border border-slate-100 rounded-xl p-4 flex flex-col justify-between cursor-pointer hover:border-emerald-500 hover:shadow-md transition-all group min-h-[160px]">

                            <!-- Top Area -->
                            <div class="flex items-start justify-between">
                                <!-- Icon on the Left -->
                                <div class="w-14 h-14 flex-shrink-0 flex items-center justify-center -ml-1 mt-1">
                                    <MedicineIcon :icon-id="med.icon" />
                                </div>
                                
                                <!-- Text on the Right -->
                                <div class="text-right ml-3 min-w-0 flex-1">
                                    <div class="flex items-center justify-end gap-1 mb-0.5">
                                        <div class="text-[15px] font-bold text-slate-900 truncate">{{ med.name }}</div>
                                        <button @click.stop="toggleFavorite(med.id)" class="text-lg leading-none" :class="isFavorite(med.id) ? 'text-yellow-400' : 'text-slate-200 hover:text-slate-300'">⭐</button>
                                    </div>
                                    <div class="text-[13px] text-slate-500 truncate">{{ med.type }}{{ med.strength ? ' · ' + med.strength : '' }}</div>
                                </div>
                            </div>
                            
                            <!-- Price -->
                            <div class="text-right mt-1 mb-3">
                                <span class="text-[17px] font-black text-slate-900">${{ parseFloat((med.inventory && med.inventory.length ? med.inventory[0].mrp : med.sell_price) || 0).toFixed(2) }}</span>
                            </div>

                            <!-- Divider & Bottom Info -->
                            <div class="border-t border-slate-100 pt-2.5 text-[12px] text-slate-500 space-y-0.5 text-left">
                                <div class="flex items-center gap-1">
                                    <span class="font-medium text-slate-600">B:</span> {{ med.inventory && med.inventory.length ? med.inventory[0].batch_no : '—' }} <span class="text-slate-300 mx-0.5">|</span> <span class="font-medium text-slate-600">Exp:</span> {{ (med.inventory && med.inventory.length && med.inventory[0].expiry_date) ? med.inventory[0].expiry_date.substring(5,10).replace('-', '/') : '—' }}
                                </div>
                                <div>
                                    <span class="font-medium text-slate-600">Stock:</span> <span :class="totalStock(med) <= 10 ? 'text-rose-600 font-bold' : 'text-slate-700 font-bold'">{{ totalStock(med) }}</span>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Recent Scans -->
                <div v-if="recentScans.length" class="border-t border-slate-200 bg-white px-4 py-2 flex-shrink-0">
                    <div class="flex items-center gap-3 overflow-x-auto">
                        <span class="text-xs text-slate-500 flex-shrink-0">Recent Scans</span>
                        <button v-for="scan in recentScans" :key="scan.id"
                            @click="searchQuery = scan.name"
                            class="flex-shrink-0 bg-slate-50 hover:bg-slate-200 text-slate-700 text-xs px-3 py-1.5 rounded-lg transition-colors">
                            {{ scan.name }}
                        </button>
                    </div>
                </div>
            </main>

            <!-- ── RIGHT PANEL: Cart ── -->
            <aside class="w-80 xl:w-96 bg-white border-l border-slate-200 flex flex-col flex-shrink-0">

                <!-- Customer -->
                <div class="px-4 pt-3 pb-2 border-b border-slate-200 flex-shrink-0">
                    <div class="flex items-center gap-2">
                        <div class="flex-1">
                            <select v-model="selectedCustomerId"
                                class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-emerald-500">
                                <option value="">👤 Walk-in Customer</option>
                                <option v-for="c in customerList" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                        </div>
                        <button @click="openCustomerModal"
                            class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-500 text-white text-xs font-semibold px-3 py-2 rounded-lg transition-colors flex items-center gap-1">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            New
                        </button>
                    </div>
                </div>

                <!-- Cart Header -->
                <div class="flex items-center justify-between px-4 py-2 flex-shrink-0">
                    <span class="text-sm font-semibold text-slate-900">{{ cart.length }} Item{{ cart.length !== 1 ? 's' : '' }} in Cart</span>
                    <button v-if="cart.length" @click="clearCart" class="text-xs text-rose-600 hover:text-rose-300 font-semibold transition-colors">Clear Cart</button>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto px-3 space-y-2">
                    <div v-if="!cart.length" class="flex flex-col items-center justify-center h-full text-slate-600">
                        <span class="text-4xl mb-2">🛒</span>
                        <p class="text-sm">Cart is empty</p>
                        <p class="text-xs mt-1">Click a medicine to add it</p>
                    </div>

                    <div v-for="(item, idx) in cart" :key="idx"
                        class="flex items-center gap-2 bg-white border border-slate-200 rounded-xl p-2.5">
                        <!-- Icon -->
                        <div class="w-10 h-10 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center flex-shrink-0 p-0.5">
                            <MedicineIcon :icon-id="item.icon" />
                        </div>
                        <!-- Info -->
                        <div class="flex-1 min-w-0">
                            <div class="text-sm font-medium text-slate-900 truncate">{{ item.name }}</div>
                            <div class="text-xs text-slate-500">{{ item.type }} · B-{{ item.batch_no }} {{ item.expiry_date }}</div>
                        </div>
                        <!-- Qty -->
                        <div class="flex items-center gap-1 flex-shrink-0">
                            <button @click="updateQty(idx, -1)" class="w-6 h-6 rounded-md bg-slate-200 hover:bg-slate-600 text-slate-900 flex items-center justify-center text-sm transition-colors">−</button>
                            <span class="w-7 text-center text-sm font-bold text-slate-900">{{ item.quantity }}</span>
                            <button @click="updateQty(idx, +1)" class="w-6 h-6 rounded-md bg-slate-200 hover:bg-slate-600 text-slate-900 flex items-center justify-center text-sm transition-colors">+</button>
                        </div>
                        <!-- Price + Delete -->
                        <div class="text-right flex-shrink-0 flex flex-col justify-between items-end">
                            <div class="text-sm font-bold text-emerald-600">${{ (item.unit_price * item.quantity).toFixed(2) }}</div>
                            <button @click="removeFromCart(idx)" class="text-rose-400 hover:text-rose-600 transition-colors mt-1" title="Remove Item">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16" /></svg>
                            </button>
                        </div>
                    </div>
                </div>

                <!-- Discount + Note -->
                <div class="px-4 pt-3 border-t border-slate-200 flex-shrink-0 space-y-2">
                    <button @click="discountOpen = !discountOpen"
                        class="flex items-center gap-2 text-sm text-emerald-600 hover:text-emerald-300 font-semibold transition-colors">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Add Discount
                        <svg :class="['w-3.5 h-3.5 transition-transform', discountOpen ? 'rotate-180' : '']" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                    </button>
                    <div v-if="discountOpen" class="grid grid-cols-2 gap-2">
                        <div>
                            <label class="text-xs text-slate-500">Discount ($)</label>
                            <input v-model.number="discountAmount" type="number" min="0" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2 py-1.5 text-sm text-slate-900 focus:outline-none focus:border-emerald-500">
                        </div>
                        <div>
                            <label class="text-xs text-slate-500">Tax (%)</label>
                            <input v-model.number="taxPercent" type="number" min="0" max="100" class="w-full bg-slate-50 border border-slate-300 rounded-lg px-2 py-1.5 text-sm text-slate-900 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                    <textarea v-model="saleNote" rows="1" placeholder="Note (Optional)"
                        class="w-full bg-slate-50 border border-slate-300 rounded-lg px-3 py-2 text-sm text-slate-900 placeholder-slate-600 focus:outline-none focus:border-emerald-500 resize-none"></textarea>
                </div>

                <!-- Totals -->
                <div class="px-4 pb-2 pt-2 border-t border-slate-200 flex-shrink-0 space-y-1.5">
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Subtotal</span>
                        <span class="text-slate-900">${{ subtotal.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Discount</span>
                        <span class="text-rose-600">-${{ discountAmount.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between text-sm text-slate-500">
                        <span>Tax ({{ taxPercent }}%)</span>
                        <span class="text-slate-900">${{ taxAmount.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between items-baseline border-t border-slate-200 pt-2 mt-1">
                        <span class="text-base font-bold text-slate-900">Total Payable</span>
                        <span class="text-2xl font-bold text-emerald-600">${{ total.toFixed(2) }}</span>
                    </div>
                </div>

                <!-- Pay Button -->
                <div class="px-4 pb-3 flex-shrink-0">
                    <button @click="completeSale" :disabled="!cart.length || isSubmitting"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 disabled:text-slate-500 text-slate-900 font-bold py-3.5 rounded-xl transition-all text-lg flex items-center justify-center gap-3 shadow-lg shadow-emerald-900/30">
                        <span>{{ isSubmitting ? 'Processing...' : `Pay ($${total.toFixed(2)})` }}</span>
                        <kbd v-if="!isSubmitting" class="text-xs bg-emerald-700 px-1.5 py-0.5 rounded font-semibold">F9</kbd>
                    </button>

                    <!-- Payment Methods -->
                    <div class="grid grid-cols-4 gap-1.5 mt-2">
                        <button v-for="pm in [
                            { key: 'cash', label: 'Cash', icon: '💵', fkey: 'F5' },
                            { key: 'card', label: 'Card', icon: '💳', fkey: 'F6' },
                            { key: 'other', label: 'Other', icon: '🏦', fkey: 'F7' },
                            { key: 'credit', label: 'Credit', icon: '📋', fkey: 'F8' },
                        ]" :key="pm.key"
                            @click="paymentMethod = pm.key"
                            :class="['flex flex-col items-center py-2 px-1 rounded-xl text-center transition-all border', paymentMethod === pm.key ? 'bg-emerald-600/20 border-emerald-500 text-emerald-600' : 'bg-slate-50 border-slate-300 text-slate-500 hover:bg-slate-200 hover:text-slate-900']">
                            <span class="text-lg">{{ pm.icon }}</span>
                            <span class="text-xs font-semibold mt-0.5">{{ pm.label }}</span>
                            <span class="text-xs text-slate-600">{{ pm.fkey }}</span>
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ── BOTTOM BAR ── -->
        <footer class="flex items-center justify-between bg-white border-t border-slate-200 px-4 h-10 flex-shrink-0 z-20">
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <button type="button" @click="handleHoldInvoice" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <kbd class="bg-slate-50 px-1 rounded">F1</kbd> Hold Invoice
                </button>
                <Link :href="route('customers.index')" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <kbd class="bg-slate-50 px-1 rounded">F4</kbd> Search Patient
                </Link>
                <button @click="showRecentBills = !showRecentBills" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <kbd class="bg-slate-50 px-1 rounded">F10</kbd> Recent Bills
                </button>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    Online
                </div>
                <span>Session: CS-{{ String(Math.floor(Math.random() * 90000) + 10000) }}</span>
            </div>
        </footer>

        <!-- ── RECENT BILLS PANEL ── -->
        <div v-if="showRecentBills"
            class="absolute bottom-10 left-1/2 -translate-x-1/2 w-[600px] bg-white border border-slate-300 rounded-2xl shadow-2xl z-50 overflow-hidden">
            <div class="flex items-center justify-between px-5 py-3 border-b border-slate-200">
                <span class="font-bold text-slate-900">Today's Recent Bills</span>
                <button @click="showRecentBills = false" class="text-slate-500 hover:text-slate-900 text-xl leading-none">×</button>
            </div>
            <div v-if="!recentSales.length" class="text-center py-8 text-slate-600 text-sm">No sales today yet.</div>
            <div v-else class="max-h-72 overflow-y-auto divide-y divide-slate-800">
                <div v-for="sale in recentSales" :key="sale.id"
                    class="flex items-center justify-between px-5 py-3 hover:bg-slate-50 transition-colors">
                    <div>
                        <div class="text-sm font-semibold text-slate-900">{{ sale.invoice_no }}</div>
                        <div class="text-xs text-slate-500">{{ sale.customer?.name || 'Walk-in' }}</div>
                    </div>
                    <div class="text-right">
                        <div class="text-sm font-bold text-emerald-600">${{ parseFloat(sale.total_amount).toFixed(2) }}</div>
                        <Link :href="route('sales.show', sale.id)" class="text-xs text-indigo-600 hover:text-indigo-300 transition-colors">View Receipt →</Link>
                    </div>
                </div>
            </div>
        </div>

        <!-- ── NEW CUSTOMER MODAL ── -->
        <Teleport to="body">
            <div v-if="showCustomerModal" class="fixed inset-0 z-[100] flex items-center justify-center">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="closeCustomerModal"></div>

                <!-- Modal -->
                <div class="relative bg-white border border-slate-300 rounded-2xl shadow-2xl w-full max-w-md mx-4 overflow-hidden">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-6 py-4 border-b border-slate-200">
                        <div class="flex items-center gap-3">
                            <div class="w-9 h-9 rounded-xl bg-emerald-600/20 border border-emerald-600/30 flex items-center justify-center">
                                <svg class="w-5 h-5 text-emerald-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"/></svg>
                            </div>
                            <div>
                                <h2 class="text-base font-bold text-slate-900">New Customer</h2>
                                <p class="text-xs text-slate-500">Will be selected immediately after saving</p>
                            </div>
                        </div>
                        <button @click="closeCustomerModal" class="text-slate-500 hover:text-slate-900 text-2xl leading-none transition-colors">×</button>
                    </div>

                    <!-- Body -->
                    <form @submit.prevent="saveCustomer" class="px-6 py-5 space-y-4">
                        <!-- Name -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Full Name <span class="text-rose-600">*</span></label>
                            <input v-model="customerForm.name" type="text" required autofocus
                                placeholder="e.g. John Doe"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-colors"/>
                            <p v-if="customerForm.errors.name" class="mt-1 text-xs text-rose-600">{{ customerForm.errors.name }}</p>
                        </div>

                        <!-- Phone -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Phone Number</label>
                            <input v-model="customerForm.phone" type="tel"
                                placeholder="e.g. +880 1700-000000"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-colors"/>
                            <p v-if="customerForm.errors.phone" class="mt-1 text-xs text-rose-600">{{ customerForm.errors.phone }}</p>
                        </div>

                        <!-- Address -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Address</label>
                            <textarea v-model="customerForm.address" rows="2"
                                placeholder="Street, City..."
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-colors resize-none"></textarea>
                        </div>

                        <!-- Opening Balance -->
                        <div>
                            <label class="block text-sm font-medium text-slate-700 mb-1">Opening Balance ($)</label>
                            <input v-model.number="customerForm.opening_balance" type="number" step="0.01" min="0"
                                class="w-full bg-slate-50 border border-slate-300 rounded-xl px-4 py-2.5 text-sm text-slate-900 focus:outline-none focus:border-emerald-500 transition-colors"/>
                        </div>

                        <!-- Actions -->
                        <div class="flex gap-3 pt-2">
                            <button type="button" @click="closeCustomerModal"
                                class="flex-1 bg-slate-50 hover:bg-slate-200 text-slate-700 font-semibold py-2.5 rounded-xl transition-colors text-sm">
                                Cancel
                            </button>
                            <button type="submit" :disabled="customerForm.processing"
                                class="flex-1 bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 disabled:text-slate-500 text-slate-900 font-bold py-2.5 rounded-xl transition-all text-sm flex items-center justify-center gap-2">
                                <svg v-if="customerForm.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8v8H4z"/></svg>
                                {{ customerForm.processing ? 'Saving...' : 'Save & Select Customer' }}
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </Teleport>

    </div>
</template>
