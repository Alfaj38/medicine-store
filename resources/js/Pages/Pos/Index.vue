<script setup>
import { Head, Link, useForm, router, usePage } from '@inertiajs/vue3';
import { ref, computed, watch, onMounted, onUnmounted } from 'vue';
import MedicineIcon from '@/Components/MedicineIcon.vue';

const props = defineProps({
    customers: Array,
    categories: Array,
    itemTypes: Array,
    recentSales: Array,
});

// ─── State ────────────────────────────────────────────────────────────────────
const searchQuery       = ref('');
const categorySearchQuery = ref('');
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
const mobileCartOpen    = ref(false);
const mobileCategoriesOpen = ref(false);
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
    const isStockItem = medicine.inventory && medicine.inventory.length > 0;
    const isService = !isStockItem; // Services, Assets, Non-Stock items have no batches

    // Stock items must have inventory; services can always be added
    if (!isService && !isStockItem) return;

    if (isStockItem) {
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
                available_units: medicine.units || [],
                unit_id: medicine.default_unit?.id || null,
                unit_factor: medicine.default_unit?.factor || 1,
                unit_name: medicine.default_unit?.unit_name || 'Unit',
                quantity: 1,
                unit_price: parseFloat(batch.mrp || medicine.sell_price || 0) * (medicine.default_unit?.factor || 1),
                base_price: parseFloat(batch.mrp || medicine.sell_price || 0),
            });
        }
    } else {
        // Service / Non-Stock — no batch, no stock limit
        const existing = cart.value.find(i => i.medicine_id === medicine.id && !i.batch_no);
        if (existing) {
            existing.quantity++;
        } else {
            cart.value.push({
                medicine_id: medicine.id,
                name: medicine.name,
                icon: medicine.icon,
                strength: '',
                type: 'Service',
                batch_no: null,
                expiry_date: '',
                available_qty: 9999,
                available_units: medicine.units || [],
                unit_id: medicine.default_unit?.id || null,
                unit_factor: medicine.default_unit?.factor || 1,
                unit_name: medicine.default_unit?.unit_name || 'Unit',
                quantity: 1,
                unit_price: parseFloat(medicine.sell_price || 0) * (medicine.default_unit?.factor || 1),
                base_price: parseFloat(medicine.sell_price || 0),
            });
        }
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
    if (newQty * item.unit_factor > item.available_qty) return;
    item.quantity = newQty;
};

const onUnitChange = (item) => {
    if (!item.unit_id) return;
    let selectedUnit = item.available_units.find(u => u.id === item.unit_id);
    if (selectedUnit) {
        item.unit_factor = selectedUnit.factor;
        item.unit_name = selectedUnit.unit_name;
        item.unit_price = item.base_price * selectedUnit.factor;
        
        if (item.quantity * item.unit_factor > item.available_qty) {
            item.quantity = Math.floor(item.available_qty / item.unit_factor);
            if (item.quantity < 1) item.quantity = 1;
        }
    }
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
            unit_id: i.unit_id === 'fallback' ? null : i.unit_id,
            unit_factor: i.unit_factor,
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
    Swal.fire({
        icon: 'info',
        title: 'Coming Soon',
        text: 'Hold invoice feature coming soon!'
    });
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
    // Medicine
    if (nameLower.includes('pain')) return '🩹';
    if (nameLower.includes('antibiotic')) return '💊';
    if (nameLower.includes('vitamin') || nameLower.includes('supp') || nameLower.includes('nutrition')) return '🫙';
    if (nameLower.includes('cough') || nameLower.includes('cold')) return '🤧';
    if (nameLower.includes('digest')) return '🫄';
    if (nameLower.includes('diabet')) return '💉';
    if (nameLower.includes('cardio')) return '❤️';
    if (nameLower.includes('derma') || nameLower.includes('skin')) return '🧴';
    if (nameLower.includes('eye')) return '👁️';
    // Consumer Good
    if (nameLower.includes('baby')) return '🍼';
    if (nameLower.includes('oral')) return '🦷';
    if (nameLower.includes('hygiene')) return '🧼';
    if (nameLower.includes('toiletries')) return '🧻';
    if (nameLower.includes('first aid')) return '🚑';
    if (nameLower.includes('family planning')) return '💊';
    // Surgical
    if (nameLower.includes('bandage') || nameLower.includes('dressing')) return '🩹';
    if (nameLower.includes('syringe') || nameLower.includes('needle')) return '💉';
    if (nameLower.includes('glove')) return '🧤';
    if (nameLower.includes('suture') || nameLower.includes('catheter')) return '🧵';
    if (nameLower.includes('instrument')) return '🔪';
    if (nameLower.includes('orthopedic')) return '🦴';
    // Medical Device
    if (nameLower.includes('blood pressure') || nameLower.includes('monitor')) return '🩺';
    if (nameLower.includes('thermometer')) return '🌡️';
    if (nameLower.includes('glucometer')) return '🩸';
    if (nameLower.includes('oximeter') || nameLower.includes('nebulizer')) return '💨';
    if (nameLower.includes('mobility') || nameLower.includes('weighing')) return '⚖️';
    // Asset
    if (nameLower.includes('computer') || nameLower.includes('it')) return '💻';
    if (nameLower.includes('furniture')) return '🪑';
    if (nameLower.includes('refrigerator')) return '🧊';
    if (nameLower.includes('air condition')) return '❄️';
    if (nameLower.includes('fixture') || nameLower.includes('equipment')) return '🏥';
    // Service
    if (nameLower.includes('delivery')) return '🚚';
    if (nameLower.includes('checkup') || nameLower.includes('consultation')) return '🩺';
    if (nameLower.includes('test') || nameLower.includes('sugar')) return '🩸';
    if (nameLower.includes('injection') || nameLower.includes('administration')) return '💉';
    return '📦';
};

// ─── Item type icon ────────────────────────────────────────────────────────────
const typeGroupIcon = (typeName) => {
    const map = {
        'Medicine': '💊',
        'Consumer Good': '🛒',
        'Surgical': '🔬',
        'Medical Device': '🩺',
        'Asset': '🏥',
        'Service': '🛎️',
    };
    return map[typeName] || '📦';
};

// ─── Categories grouped by item type ─────────────────────────────────────────
const categoriesByType = computed(() => {
    const groups = {};
    props.categories?.forEach(cat => {
        const typeName = cat.item_type?.name || 'Other';
        if (!groups[typeName]) groups[typeName] = [];
        groups[typeName].push(cat);
    });
    return groups;
});

// Filtered categories based on search input
const filteredCategoriesByType = computed(() => {
    if (!categorySearchQuery.value.trim()) return categoriesByType.value;
    const query = categorySearchQuery.value.toLowerCase();
    const filtered = {};
    Object.entries(categoriesByType.value).forEach(([typeName, cats]) => {
        const matched = cats.filter(c => c.name.toLowerCase().includes(query));
        if (matched.length) filtered[typeName] = matched;
    });
    return filtered;
});
</script>

<template>
    <Head title="POS - SaaSMedi" />

    <div class="flex flex-col h-screen bg-slate-50 text-slate-900 overflow-hidden select-none">

        <!-- ── TOP NAV ── -->
        <header class="flex items-center justify-between bg-white border-b border-slate-200 px-4 h-12 flex-shrink-0 z-20">
            <div class="flex items-center gap-4">
                <div class="flex items-center gap-2">
                    <div class="w-7 h-7 rounded-md bg-emerald-500 flex items-center justify-center">
                        <span class="text-xs font-bold text-white">M</span>
                    </div>
                    <span class="font-bold text-sm text-slate-900 tracking-wide">SaaSMedi POS</span>
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

            <!-- ── LEFT SIDEBAR: Categories (Hidden on mobile) ── -->
            <aside class="hidden lg:flex w-56 bg-white border-r border-slate-200 flex-col flex-shrink-0 overflow-y-auto pt-4 px-3">
                <button
                    @click="selectedCategory = null; activeFilter = 'all'"
                    :class="['flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all rounded-xl mb-3 w-full text-left shadow-sm', selectedCategory === null && activeFilter === 'all' ? 'bg-emerald-600 text-white' : 'text-slate-700 bg-white border border-slate-200 hover:bg-slate-50']">
                    <span class="text-lg">🏪</span>
                    <span>All Items</span>
                </button>

                <!-- Category Search -->
                <div class="relative mb-2 px-2">
                    <input
                        v-model="categorySearchQuery"
                        type="text"
                        placeholder="Search categories..."
                        class="w-full bg-slate-50 border border-slate-300 rounded-md pl-3 pr-8 py-1.5 text-sm text-slate-900 placeholder-slate-500 focus:outline-none focus:border-emerald-500 transition-colors"
                    />
                    <!-- Clear icon -->
                    <span v-if="categorySearchQuery" @click="categorySearchQuery = ''"
                        class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer"
                        >✕</span>
                </div>

                <!-- Grouped Categories -->
                <div class="space-y-3 pb-4">
                    <template v-for="(cats, typeName) in filteredCategoriesByType" :key="typeName">
                        <!-- Type Group Header -->
                        <div>
                            <div class="flex items-center gap-1.5 px-2 mb-1">
                                <span class="text-sm">{{ typeGroupIcon(typeName) }}</span>
                                <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ typeName }}</span>
                            </div>
                            <div class="space-y-0.5">
                                <button v-for="cat in cats" :key="cat.id"
                                    @click="selectedCategory = cat.id; activeFilter = 'all'"
                                    :class="['flex items-center gap-2.5 px-3 py-2 text-sm transition-all rounded-lg w-full text-left', selectedCategory === cat.id ? 'bg-emerald-50 text-emerald-700 font-semibold shadow-sm' : 'text-slate-600 bg-transparent hover:bg-slate-50 hover:text-slate-900']">
                                    <span class="text-base leading-none">{{ categoryIcon(cat.name) }}</span>
                                    <span class="truncate">{{ cat.name }}</span>
                                </button>
                            </div>
                        </div>
                    </template>
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
                        <!-- Mobile Categories Toggle -->
                        <button @click="mobileCategoriesOpen = true" class="lg:hidden flex items-center justify-center w-10 h-10 bg-slate-100 hover:bg-slate-200 text-slate-700 rounded-xl transition-colors flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"/></svg>
                        </button>
                        <!-- Quick Add -->
                        <button class="flex items-center gap-2 bg-emerald-600 hover:bg-emerald-500 text-white text-sm font-semibold px-4 py-2.5 rounded-xl transition-colors flex-shrink-0">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                            <span class="hidden sm:inline">Quick Add</span> <kbd class="hidden sm:inline ml-1 text-xs bg-emerald-700 px-1.5 py-0.5 rounded">F2</kbd>
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

            <!-- ── RIGHT PANEL: Cart (Desktop: fixed side, Mobile: sliding panel) ── -->
            <aside :class="['w-80 xl:w-96 bg-white border-l border-slate-200 flex flex-col flex-shrink-0 z-40 transition-transform duration-300', mobileCartOpen ? 'fixed inset-y-0 right-0 shadow-2xl' : 'hidden lg:flex']">
                <!-- Mobile close button -->
                <button v-if="mobileCartOpen" @click="mobileCartOpen = false" class="absolute top-2 left-[-40px] bg-white w-10 h-10 flex items-center justify-center rounded-l-xl shadow-l text-slate-500 hover:text-slate-900 border border-slate-200 border-r-0 lg:hidden">
                    ✕
                </button>

                <!-- Customer -->
                <div class="px-3 pt-2 pb-1.5 border-b border-slate-200 flex-shrink-0">
                    <div class="flex items-center gap-1.5">
                        <div class="flex-1 relative">
                            <div class="absolute inset-y-0 left-0 pl-2.5 flex items-center pointer-events-none text-slate-400">👤</div>
                            <select v-model="selectedCustomerId"
                                class="w-full bg-slate-50 border border-slate-300 rounded-md pl-7 pr-2 py-1.5 text-xs text-slate-900 focus:outline-none focus:border-emerald-500 appearance-none">
                                <option value="">Walk-in Customer</option>
                                <option v-for="c in customerList" :key="c.id" :value="c.id">{{ c.name }}</option>
                            </select>
                            <div class="absolute inset-y-0 right-0 pr-2 flex items-center pointer-events-none text-slate-400">
                                <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"/></svg>
                            </div>
                        </div>
                        <button @click="openCustomerModal" title="New Customer"
                            class="flex-shrink-0 bg-emerald-600 hover:bg-emerald-500 text-white p-1.5 rounded-md transition-colors flex items-center justify-center">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Cart Header -->
                <div class="flex items-center justify-between px-3 py-1.5 flex-shrink-0 bg-slate-50 border-b border-slate-200">
                    <span class="text-xs font-bold uppercase tracking-wider text-slate-500">Cart ({{ cart.length }})</span>
                    <button v-if="cart.length" @click="clearCart" class="text-xs text-rose-500 hover:text-rose-700 font-semibold transition-colors">Clear All</button>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto px-2 py-1.5 space-y-1.5 bg-slate-50/50">
                    <div v-if="!cart.length" class="flex flex-col items-center justify-center h-full text-slate-400">
                        <span class="text-3xl mb-1 opacity-50">🛒</span>
                        <p class="text-xs font-medium">Cart is empty</p>
                    </div>

                    <div v-for="(item, idx) in cart" :key="idx"
                        class="flex items-start gap-2 bg-white border border-slate-200 rounded-lg p-1.5 shadow-sm hover:border-emerald-300 transition-colors">
                        <!-- Info -->
                        <div class="flex-1 min-w-0 pt-0.5">
                            <div class="flex justify-between items-start">
                                <div class="text-[13px] font-bold text-slate-900 leading-tight truncate pr-2">{{ item.name }}</div>
                                <div class="text-[13px] font-bold text-emerald-600 flex-shrink-0">${{ (item.unit_price * item.quantity).toFixed(2) }}</div>
                            </div>
                            <div class="flex justify-between items-center mt-1">
                                <div class="text-[10px] text-slate-500 truncate flex items-center gap-1">
                                    <span>{{ item.type }} · B:{{ item.batch_no || '-' }}</span>
                                    <select v-if="item.available_units && item.available_units.length > 0" v-model="item.unit_id" @change="onUnitChange(item)" class="bg-slate-100 border border-slate-200 rounded px-1 py-0.5 text-[10px] text-slate-700 ml-1 focus:outline-none">
                                        <option v-for="u in item.available_units" :key="u.id" :value="u.id">{{ u.unit_name }}</option>
                                    </select>
                                </div>
                                <div class="flex items-center gap-1.5 flex-shrink-0 bg-slate-100 rounded-md p-0.5">
                                    <button @click="updateQty(idx, -1)" class="w-5 h-5 rounded bg-white border border-slate-200 text-slate-700 flex items-center justify-center leading-none hover:bg-slate-200">−</button>
                                    <span class="w-5 text-center text-xs font-bold text-slate-900">{{ item.quantity }}</span>
                                    <button @click="updateQty(idx, +1)" class="w-5 h-5 rounded bg-white border border-slate-200 text-slate-700 flex items-center justify-center leading-none hover:bg-slate-200">+</button>
                                </div>
                            </div>
                        </div>
                        <button @click="removeFromCart(idx)" class="mt-1 text-slate-300 hover:text-rose-500 transition-colors flex-shrink-0" title="Remove Item">
                            <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/></svg>
                        </button>
                    </div>
                </div>

                <!-- Discount + Note Toggles -->
                <div class="px-3 py-1.5 border-t border-slate-200 flex-shrink-0 flex items-center justify-between bg-slate-50">
                    <button @click="discountOpen = !discountOpen"
                        :class="['flex items-center gap-1 text-[11px] font-semibold transition-colors px-2 py-1 rounded', discountOpen || discountAmount > 0 || taxPercent > 0 ? 'bg-emerald-100 text-emerald-700' : 'text-slate-500 hover:bg-slate-200']">
                        <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"/></svg>
                        Discount / Tax
                    </button>
                    <div class="flex-1 max-w-[140px]">
                        <input v-model="saleNote" type="text" placeholder="Add note..." class="w-full bg-white border border-slate-200 rounded px-2 py-1 text-[11px] text-slate-700 placeholder-slate-400 focus:outline-none focus:border-emerald-500" />
                    </div>
                </div>

                <!-- Discount Popup Panel -->
                <div v-if="discountOpen" class="px-3 py-2 bg-white border-b border-slate-200 flex-shrink-0">
                    <div class="flex gap-2">
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5 uppercase">Discount ($)</label>
                            <input v-model.number="discountAmount" type="number" min="0" class="w-full bg-slate-50 border border-slate-200 rounded p-1 text-xs text-slate-900 focus:outline-none focus:border-emerald-500">
                        </div>
                        <div class="flex-1">
                            <label class="block text-[10px] font-semibold text-slate-500 mb-0.5 uppercase">Tax (%)</label>
                            <input v-model.number="taxPercent" type="number" min="0" max="100" class="w-full bg-slate-50 border border-slate-200 rounded p-1 text-xs text-slate-900 focus:outline-none focus:border-emerald-500">
                        </div>
                    </div>
                </div>

                <!-- Compact Totals & Pay Section -->
                <div class="px-3 py-2 border-t border-slate-200 flex-shrink-0 bg-white">
                    <div class="flex items-end justify-between mb-2">
                        <div class="space-y-0.5">
                            <div class="text-[11px] text-slate-500 flex justify-between gap-3"><span>Sub:</span> <span>${{ subtotal.toFixed(2) }}</span></div>
                            <div v-if="discountAmount > 0" class="text-[11px] text-rose-500 flex justify-between gap-3"><span>Disc:</span> <span>-${{ discountAmount.toFixed(2) }}</span></div>
                            <div v-if="taxPercent > 0" class="text-[11px] text-slate-500 flex justify-between gap-3"><span>Tax:</span> <span>${{ taxAmount.toFixed(2) }}</span></div>
                            <div class="text-[13px] font-bold text-slate-900 mt-0.5">Total</div>
                        </div>
                        <div class="text-right">
                            <div class="text-3xl font-black text-emerald-600 leading-none">${{ total.toFixed(2) }}</div>
                        </div>
                    </div>

                    <!-- Payment Methods (Segment Control) -->
                    <div class="flex p-0.5 bg-slate-100 rounded-lg mb-2">
                        <button v-for="pm in [
                            { key: 'cash', label: 'Cash' },
                            { key: 'card', label: 'Card' },
                            { key: 'other', label: 'Other' },
                            { key: 'credit', label: 'Credit' },
                        ]" :key="pm.key"
                            @click="paymentMethod = pm.key"
                            :class="['flex-1 py-1.5 text-xs font-bold rounded-md transition-all', paymentMethod === pm.key ? 'bg-white text-emerald-700 shadow-sm' : 'text-slate-500 hover:text-slate-700']">
                            {{ pm.label }}
                        </button>
                    </div>

                    <!-- Pay Button -->
                    <button @click="completeSale" :disabled="!cart.length || isSubmitting"
                        class="w-full bg-emerald-600 hover:bg-emerald-500 disabled:bg-slate-200 disabled:text-slate-500 text-white font-bold py-2.5 rounded-lg transition-all text-sm flex items-center justify-center gap-2 shadow-sm">
                        <span>{{ isSubmitting ? 'Processing...' : 'Pay Now' }}</span>
                        <kbd v-if="!isSubmitting" class="text-[10px] bg-emerald-700/50 px-1.5 py-0.5 rounded ml-1">F9</kbd>
                    </button>
                </div>
            </aside>
        </div>

        <!-- ── BOTTOM BAR ── -->
        <footer class="flex items-center justify-between bg-white border-t border-slate-200 px-4 h-10 flex-shrink-0 z-20 overflow-x-auto whitespace-nowrap scrollbar-hide">
            <div class="flex items-center gap-4 text-xs text-slate-500">
                <button type="button" @click="handleHoldInvoice" class="hidden sm:flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"/></svg>
                    <kbd class="bg-slate-50 px-1 rounded">F1</kbd> <span class="hidden md:inline">Hold Invoice</span>
                </button>
                <Link :href="route('customers.index')" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"/></svg>
                    <kbd class="bg-slate-50 px-1 rounded">F4</kbd> Search <span class="hidden md:inline">Patient</span>
                </Link>
                <button @click="showRecentBills = !showRecentBills" class="flex items-center gap-1.5 hover:text-slate-900 transition-colors">
                    <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/></svg>
                    <kbd class="bg-slate-50 px-1 rounded">F10</kbd> Recent <span class="hidden md:inline">Bills</span>
                </button>
            </div>
            <div class="flex items-center gap-3 text-xs text-slate-500 ml-4">
                <div class="flex items-center gap-1.5">
                    <div class="w-2 h-2 rounded-full bg-emerald-500 animate-pulse"></div>
                    <span class="hidden sm:inline">Online</span>
                </div>
                <span class="hidden sm:inline">Session: CS-{{ String(Math.floor(Math.random() * 90000) + 10000) }}</span>
            </div>
        </footer>

        <!-- ── FLOATING CART BUTTON (Mobile Only) ── -->
        <button v-if="!mobileCartOpen" @click="mobileCartOpen = true" class="lg:hidden fixed bottom-14 right-4 z-30 bg-emerald-600 hover:bg-emerald-500 transition-colors text-white w-14 h-14 rounded-full shadow-2xl flex items-center justify-center">
            <span class="relative">
                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                <span v-if="cart.length" class="absolute -top-2 -right-2 bg-rose-500 text-white text-[10px] font-bold w-5 h-5 flex items-center justify-center rounded-full border-2 border-white">{{ cart.length }}</span>
            </span>
        </button>

        <!-- ── MOBILE CATEGORIES MODAL ── -->
        <Teleport to="body">
            <div v-if="mobileCategoriesOpen" class="fixed inset-0 z-[100] flex items-end sm:items-center justify-center lg:hidden">
                <!-- Backdrop -->
                <div class="absolute inset-0 bg-slate-900/60 backdrop-blur-sm" @click="mobileCategoriesOpen = false"></div>

                <!-- Modal -->
                <div class="relative bg-white w-full sm:w-80 h-[80vh] sm:h-auto sm:max-h-[80vh] rounded-t-3xl sm:rounded-2xl shadow-2xl overflow-hidden flex flex-col transform transition-transform animate-slide-up sm:animate-none">
                    <!-- Header -->
                    <div class="flex items-center justify-between px-5 py-4 border-b border-slate-200">
                        <h3 class="font-bold text-slate-900">Categories</h3>
                        <button @click="mobileCategoriesOpen = false" class="w-8 h-8 rounded-full bg-slate-100 flex items-center justify-center text-slate-500 hover:bg-slate-200">
                            ✕
                        </button>
                    </div>

                    <!-- Search -->
                    <div class="p-3 border-b border-slate-100">
                        <input v-model="categorySearchQuery" type="text" placeholder="Search categories..." class="w-full bg-slate-50 border border-slate-300 rounded-md px-3 py-2 text-sm text-slate-900 focus:outline-none focus:border-emerald-500" />
                    </div>

                    <!-- Categories List -->
                    <div class="flex-1 overflow-y-auto p-3 space-y-3">
                        <button @click="selectedCategory = null; activeFilter = 'all'; mobileCategoriesOpen = false"
                            :class="['flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all rounded-xl mb-3 w-full text-left shadow-sm', selectedCategory === null && activeFilter === 'all' ? 'bg-emerald-600 text-white' : 'text-slate-700 bg-white border border-slate-200']">
                            <span class="text-lg">🏪</span>
                            <span>All Items</span>
                        </button>

                        <template v-for="(cats, typeName) in filteredCategoriesByType" :key="typeName">
                            <div>
                                <div class="flex items-center gap-1.5 px-2 mb-1">
                                    <span class="text-sm">{{ typeGroupIcon(typeName) }}</span>
                                    <span class="text-[10px] font-bold uppercase tracking-widest text-slate-400">{{ typeName }}</span>
                                </div>
                                <div class="space-y-0.5">
                                    <button v-for="cat in cats" :key="cat.id"
                                        @click="selectedCategory = cat.id; activeFilter = 'all'; mobileCategoriesOpen = false"
                                        :class="['flex items-center gap-2.5 px-3 py-2 text-sm transition-all rounded-lg w-full text-left', selectedCategory === cat.id ? 'bg-emerald-50 text-emerald-700 font-semibold' : 'text-slate-600']">
                                        <span class="text-base leading-none">{{ categoryIcon(cat.name) }}</span>
                                        <span class="truncate">{{ cat.name }}</span>
                                    </button>
                                </div>
                            </div>
                        </template>
                    </div>
                </div>
            </div>
        </Teleport>

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
