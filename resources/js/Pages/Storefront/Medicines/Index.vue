<script setup>
import { ref, computed, watch } from 'vue';
import { Head, router } from '@inertiajs/vue3';
import { debounce } from 'lodash';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { useCart } from '@/Composables/useCart';
import MedicineIcon from '@/Components/MedicineIcon.vue';
import SeoHead from '@/Components/SeoHead.vue';

const props = defineProps({
    company: Object,
    categories: Array,
    medicines: Object,
    filters: Object,
    seo: Object,
});

const { cart, addToCart, removeFromCart, updateQuantity, cartTotal, cartCount, clearCart } = useCart();

const searchQuery = ref(props.filters.q || '');
const categorySearchQuery = ref('');
const selectedCategory = ref(props.filters.category || null);

// Favorites stored in localStorage
const favorites = ref(JSON.parse(localStorage.getItem('pos_favorites') || '[]'));
const toggleFavorite = (medicineId) => {
    const idx = favorites.value.indexOf(medicineId);
    if (idx === -1) favorites.value.push(medicineId);
    else favorites.value.splice(idx, 1);
    localStorage.setItem('pos_favorites', JSON.stringify(favorites.value));
};
const isFavorite = (medicineId) => favorites.value.includes(medicineId);

const activeFilter = ref('all');
const isMobileCategoryOpen = ref(false);
const isMobileCartOpen = ref(false);

const displayMedicines = computed(() => {
    if (activeFilter.value === 'favorites') {
        return props.medicines.data.filter(m => favorites.value.includes(m.id));
    }
    return props.medicines.data;
});

const performSearch = debounce(() => {
    router.get(route('storefront.search', props.company.slug), {
        q: searchQuery.value,
        category: selectedCategory.value,
    }, { preserveState: true });
}, 500);

watch(searchQuery, performSearch);

const selectCategory = (categorySlug) => {
    selectedCategory.value = categorySlug;
    activeFilter.value = 'all';
    router.get(route('storefront.search', props.company.slug), {
        category: categorySlug,
        q: searchQuery.value,
    }, { preserveState: true });
};

const clearCategory = () => {
    selectedCategory.value = null;
    activeFilter.value = 'all';
    router.get(route('storefront.search', props.company.slug), {
        q: searchQuery.value,
    }, { preserveState: true });
};

const filteredCategories = computed(() => {
    if (!categorySearchQuery.value.trim()) return props.categories;
    const query = categorySearchQuery.value.toLowerCase();
    return props.categories.filter(c => c.name.toLowerCase().includes(query));
});

const categoryIcon = (name) => {
    const nameLower = (name || '').toLowerCase();
    if (nameLower.includes('pain')) return '🩹';
    if (nameLower.includes('antibiotic')) return '💊';
    if (nameLower.includes('vitamin') || nameLower.includes('supp') || nameLower.includes('nutrition')) return '🫙';
    if (nameLower.includes('cough') || nameLower.includes('cold')) return '🤧';
    if (nameLower.includes('baby')) return '🍼';
    if (nameLower.includes('oral')) return '🦷';
    if (nameLower.includes('hygiene')) return '🧼';
    if (nameLower.includes('diabet')) return '💉';
    if (nameLower.includes('cardio') || nameLower.includes('pressure')) return '❤️';
    if (nameLower.includes('derma') || nameLower.includes('skin')) return '🧴';
    return '📦';
};

const checkout = () => {
    if (cartCount.value === 0) return;
    router.get(route('storefront.checkout', props.company.slug));
};

const handleImageError = (e) => {
    e.target.style.display = 'none';
    e.target.nextElementSibling.style.display = 'flex';
};

const getIconId = (med) => {
    // Basic mapping based on name or generic name to return a nice 3D icon
    const name = (med.name || '').toLowerCase();
    const generic = (med.generic_name || '').toLowerCase();
    if (name.includes('napa') || generic.includes('paracetamol')) return 'tablet-white-split';
    if (name.includes('seclo') || generic.includes('omeprazole')) return 'capsule-pink-white';
    if (name.includes('amox')) return 'capsule-red-white';
    if (name.includes('cefix')) return 'capsule-white';
    if (name.includes('cetirizine') || name.includes('fexo')) return 'tablet-blue-oblong';
    if (name.includes('syr') || generic.includes('syrup') || name.includes('vit')) return 'bottle-orange';
    if (name.includes('drop')) return 'drop-blue';
    if (name.includes('cream') || name.includes('oint')) return 'tube-orange';
    if (name.includes('syrin')) return 'syringe';
    return 'default';
};
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />

    <StorefrontLayout :company="company" :initial-search-query="filters.q" hide-cart>
        
        <!-- POS-like 3 Column Layout taking full height below Navbar -->
        <div class="flex h-[calc(100vh-73px)] w-full overflow-hidden bg-slate-50 -mt-1 shadow-inner select-none relative">
            
            <!-- ── LEFT SIDEBAR: Categories (Desktop) ── -->
            <aside class="hidden lg:flex w-56 lg:w-64 bg-white border-r border-slate-200 flex-col flex-shrink-0 overflow-hidden shadow-[4px_0_15px_rgba(0,0,0,0.02)] z-10">
                <div class="p-3 border-b border-slate-100 bg-slate-50/50">
                    <button
                        @click="clearCategory"
                        :class="['flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all rounded-xl w-full text-left shadow-sm', !selectedCategory ? 'bg-[#00a669] text-white shadow-emerald-500/20' : 'text-slate-700 bg-white border border-slate-200 hover:bg-slate-50']">
                        <span class="text-lg">🏪</span>
                        <span>All Items</span>
                    </button>
                </div>

                <!-- Category Search -->
                <div class="px-3 pt-3 pb-2 border-b border-slate-100">
                    <div class="relative">
                        <input
                            v-model="categorySearchQuery"
                            type="text"
                            placeholder="Search categories..."
                            class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-xs text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669] transition-all"
                        />
                        <span v-if="categorySearchQuery" @click="categorySearchQuery = ''"
                            class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 hover:text-slate-600 cursor-pointer text-xs"
                            >✕</span>
                    </div>
                </div>

                <!-- Category List -->
                <div class="flex-1 overflow-y-auto p-2 space-y-0.5 scrollbar-thin scrollbar-thumb-slate-200">
                    <button v-for="cat in filteredCategories" :key="cat.id"
                        @click="selectCategory(cat.slug)"
                        :class="['flex items-center gap-3 px-3 py-2.5 text-[13px] transition-all rounded-lg w-full text-left group', selectedCategory === cat.slug ? 'bg-emerald-50 text-emerald-700 font-bold shadow-sm' : 'text-slate-600 bg-transparent hover:bg-slate-50 hover:text-slate-900']">
                        <span class="text-base leading-none opacity-80 group-hover:scale-110 transition-transform">{{ categoryIcon(cat.name) }}</span>
                        <span class="truncate">{{ cat.name }}</span>
                    </button>
                    <div v-if="!filteredCategories.length" class="text-center py-6 text-xs text-slate-400">
                        No categories found
                    </div>
                </div>
            </aside>

            <!-- ── CENTRE PANEL: Medicine Grid ── -->
            <main class="flex-1 flex flex-col overflow-hidden bg-[#f4f7f6] relative">
                
                <!-- Center Header -->
                <div class="bg-white border-b border-slate-200 px-4 sm:px-5 py-4 flex-shrink-0 z-10 shadow-sm">
                    <div class="flex gap-3 sm:gap-4 items-center">
                        <!-- Mobile Category Toggle -->
                        <button @click="isMobileCategoryOpen = true" class="lg:hidden p-2.5 -ml-2 rounded-xl text-slate-600 hover:bg-slate-100 flex-shrink-0 focus:outline-none transition-colors">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M4 6h16M4 12h16M4 18h7"/></svg>
                        </button>
                        
                        <!-- Search -->
                        <div class="relative flex-1 max-w-2xl">
                            <svg class="absolute left-3.5 top-1/2 -translate-y-1/2 w-[18px] h-[18px] text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0"/></svg>
                            <input
                                v-model="searchQuery"
                                type="text"
                                placeholder="Search medicine name, generic, manufacturer..."
                                class="w-full bg-white border border-slate-300 rounded-xl pl-10 pr-4 py-3 text-sm text-slate-900 placeholder-slate-400 shadow-sm focus:outline-none focus:border-[#00a669] focus:ring-2 focus:ring-emerald-500/20 transition-all"
                            />
                        </div>
                    </div>

                    <!-- Filter Tabs -->
                    <div class="flex flex-wrap gap-2 mt-4">
                        <button @click="activeFilter = 'all'"
                            :class="['flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm', activeFilter === 'all' ? 'bg-[#00a669] text-white border border-[#00a669]' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900']">
                            <svg class="w-3.5 h-3.5" viewBox="0 0 24 24" fill="currentColor"><path d="M19 3H5c-1.1 0-2 .9-2 2v14c0 1.1.9 2 2 2h14c1.1 0 2-.9 2-2V5c0-1.1-.9-2-2-2zm-7 9h-2v2H8v-2H6v-2h2V8h2v2h2v2z"/></svg>
                            All Products
                        </button>
                        
                        <button @click="activeFilter = 'favorites'"
                            :class="['flex items-center gap-1.5 px-3 py-1.5 rounded-lg text-xs font-bold transition-all shadow-sm', activeFilter === 'favorites' ? 'bg-amber-50 text-amber-700 border border-amber-300' : 'bg-white text-slate-600 border border-slate-200 hover:bg-slate-50 hover:text-slate-900']">
                            <svg class="w-3.5 h-3.5 text-amber-400" fill="none" stroke="currentColor" viewBox="0 0 24 24" stroke-width="2.5" stroke-linecap="round" stroke-linejoin="round"><polygon points="12 2 15.09 8.26 22 9.27 17 14.14 18.18 21.02 12 17.77 5.82 21.02 7 14.14 2 9.27 8.91 8.26 12 2"></polygon></svg>
                            Favorites
                        </button>
                    </div>
                </div>

                <!-- Grid -->
                <div class="flex-1 overflow-y-auto p-5 scrollbar-thin scrollbar-thumb-slate-200">
                    <div v-if="displayMedicines.length === 0" class="flex flex-col items-center justify-center h-40 text-slate-500">
                        <div class="w-16 h-16 bg-slate-100 rounded-full flex items-center justify-center mb-3">
                            <span class="text-3xl opacity-50">💊</span>
                        </div>
                        <h3 class="text-[15px] font-bold text-slate-700">No products found</h3>
                        <p class="text-xs mt-1">Try adjusting your filters or search query.</p>
                    </div>

                    <div v-else class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-3 2xl:grid-cols-4 gap-4">
                        <div
                            v-for="med in displayMedicines" :key="med.id"
                            @click="addToCart(med)"
                            class="relative bg-white border border-slate-200 rounded-[16px] p-4 flex flex-col justify-between cursor-pointer hover:border-[#00a669] hover:shadow-[0_8px_25px_-8px_rgba(0,166,105,0.3)] transition-all group min-h-[160px] transform hover:-translate-y-1">

                            <!-- Top Area -->
                            <div class="flex items-start justify-between">
                                <!-- Image or Icon -->
                                <div class="w-14 h-14 bg-[#f8fafc] rounded-xl flex-shrink-0 flex items-center justify-center -ml-1 mt-1 border border-slate-100 overflow-hidden relative">
                                    <img v-if="med.image" :src="`/storage/${med.image}`" @error="handleImageError" class="w-full h-full object-contain p-1 mix-blend-multiply" />
                                    <div :class="{'hidden': med.image, 'flex': true}" class="w-full h-full items-center justify-center">
                                        <MedicineIcon :icon-id="getIconId(med)" />
                                    </div>
                                    <!-- Plus Hover Indicator -->
                                    <div class="absolute inset-0 bg-black/40 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-opacity">
                                        <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                                    </div>
                                </div>
                                
                                <!-- Text -->
                                <div class="text-right ml-3 min-w-0 flex-1">
                                    <div class="flex items-start justify-end gap-1 mb-1">
                                        <div class="text-[14px] font-bold text-slate-900 leading-tight line-clamp-2">{{ med.name }}</div>
                                        <button @click.stop="toggleFavorite(med.id)" class="text-lg leading-none -mt-1 ml-1 transform hover:scale-110 transition-transform" :class="isFavorite(med.id) ? 'text-amber-400' : 'text-slate-200 hover:text-slate-300'">⭐</button>
                                    </div>
                                    <div class="text-[11px] font-medium text-slate-500 truncate" :title="med.generic_name">{{ med.generic_name || 'Health Product' }}</div>
                                </div>
                            </div>
                            
                            <!-- Bottom Info -->
                            <div class="mt-4 flex items-end justify-between">
                                <div class="text-left">
                                    <span class="inline-block bg-slate-50 border border-slate-100 text-slate-600 text-[10px] font-bold px-2 py-0.5 rounded-md uppercase tracking-wider mb-1">
                                        {{ med.category || 'General' }}
                                    </span>
                                </div>
                                <div class="text-right">
                                    <div class="text-[20px] font-black text-[#00a669] leading-none">৳{{ parseFloat(med.sell_price || 0).toFixed(2) }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Mobile Sticky Cart Bar -->
                <div v-if="cartCount" class="xl:hidden absolute bottom-0 left-0 right-0 bg-white border-t border-slate-200 p-3 pb-safe z-20 shadow-[0_-4px_20px_rgba(0,0,0,0.08)]">
                    <div class="flex items-center justify-between">
                        <div class="flex flex-col">
                            <span class="text-xs text-slate-500 font-medium">{{ cartCount }} items</span>
                            <span class="text-lg font-black text-[#00a669] leading-none">৳{{ cartTotal.toFixed(2) }}</span>
                        </div>
                        <button @click="isMobileCartOpen = true" class="bg-[#1a202c] text-white px-5 py-2.5 rounded-xl font-bold text-sm shadow-md flex items-center gap-2">
                            <span>View Cart</span>
                            <span class="bg-white/20 px-2 py-0.5 rounded-full text-xs">{{ cartCount }}</span>
                        </button>
                    </div>
                </div>
            </main>

            <!-- ── RIGHT PANEL: Cart (Desktop) ── -->
            <aside class="hidden xl:flex w-[320px] xl:w-[380px] bg-white border-l border-slate-200 flex-col flex-shrink-0 z-20 shadow-[-4px_0_15px_rgba(0,0,0,0.02)]">
                
                <!-- Cart Header -->
                <div class="px-5 pt-5 pb-3 border-b border-slate-100 flex-shrink-0">
                    <div class="flex items-center justify-between mb-1">
                        <h2 class="text-[18px] font-extrabold text-slate-900 flex items-center gap-2">
                            <span>🛒</span>
                            Your Cart
                        </h2>
                        <span class="bg-emerald-100 text-emerald-800 text-[11px] font-bold px-2 py-0.5 rounded-full">{{ cartCount }} items</span>
                    </div>
                    <p class="text-[12px] text-slate-500 font-medium">Review your items before checkout</p>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-3 space-y-2 bg-[#f8fafc]/50">
                    <div v-if="!cartCount" class="flex flex-col items-center justify-center h-full text-slate-400 pb-10">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-4xl opacity-40">🛒</span>
                        </div>
                        <p class="text-[14px] font-bold text-slate-500 mb-1">Cart is empty</p>
                        <p class="text-[11px] text-slate-400">Click products from the grid to add</p>
                    </div>

                    <div v-for="(item, medicineId) in cart" :key="medicineId"
                        class="bg-white border border-slate-200 rounded-xl p-3 shadow-sm hover:border-emerald-300 transition-colors relative group">
                        
                        <!-- Remove button -->
                        <button @click="removeFromCart(medicineId)" class="absolute -top-2 -right-2 w-6 h-6 bg-white border border-slate-200 rounded-full text-slate-400 hover:text-rose-500 hover:border-rose-200 flex items-center justify-center opacity-0 group-hover:opacity-100 transition-all shadow-sm z-10">
                            ✕
                        </button>

                        <div class="flex items-start gap-3">
                            <div class="w-12 h-12 bg-slate-50 rounded-lg flex items-center justify-center border border-slate-100 overflow-hidden flex-shrink-0 p-1 relative">
                                <img v-if="item.image" :src="`/storage/${item.image}`" @error="handleImageError" class="w-full h-full object-contain mix-blend-multiply" />
                                <div :class="{'hidden': item.image, 'flex': true}" class="w-full h-full items-center justify-center">
                                    <MedicineIcon :icon-id="getIconId(item)" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <div class="text-[13px] font-bold text-slate-900 leading-tight truncate pr-2 mb-0.5">{{ item.name }}</div>
                                <div class="text-[10px] text-slate-500 truncate">{{ item.category || 'Health Product' }}</div>
                                
                                <div class="flex justify-between items-end mt-2">
                                    <div class="text-[13px] font-bold text-emerald-600">৳{{ (item.sell_price * item.quantity).toFixed(2) }}</div>
                                    <div class="flex items-center gap-2 bg-slate-50 rounded-lg p-0.5 border border-slate-200">
                                        <button @click="updateQuantity(medicineId, -1)" class="w-6 h-6 rounded bg-white text-slate-700 flex items-center justify-center text-lg leading-none hover:bg-slate-200 hover:text-slate-900 shadow-sm">−</button>
                                        <span class="w-4 text-center text-[12px] font-bold text-slate-900">{{ item.quantity }}</span>
                                        <button @click="updateQuantity(medicineId, 1)" class="w-6 h-6 rounded bg-white text-slate-700 flex items-center justify-center text-lg leading-none hover:bg-slate-200 hover:text-slate-900 shadow-sm">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div class="p-5 border-t border-slate-200 flex-shrink-0 bg-white shadow-[0_-10px_20px_rgba(0,0,0,0.02)]">
                    <div class="flex justify-between items-center mb-2">
                        <span class="text-[13px] font-medium text-slate-500">Subtotal</span>
                        <span class="text-[13px] font-bold text-slate-700">৳{{ cartTotal.toFixed(2) }}</span>
                    </div>
                    <div class="flex justify-between items-center mb-4 pb-4 border-b border-dashed border-slate-200">
                        <span class="text-[13px] font-medium text-slate-500">Delivery</span>
                        <span class="text-[11px] font-bold text-emerald-600 bg-emerald-50 px-2 py-0.5 rounded uppercase tracking-wider">Calculated at Checkout</span>
                    </div>
                    <div class="flex justify-between items-end mb-5">
                        <span class="text-[16px] font-black text-slate-900">Total</span>
                        <span class="text-[28px] font-black text-[#00a669] leading-none">৳{{ cartTotal.toFixed(2) }}</span>
                    </div>

                    <div class="flex gap-2">
                        <button v-if="cartCount" @click="clearCart" class="px-4 py-3 bg-rose-50 text-rose-600 hover:bg-rose-100 rounded-xl font-bold text-[14px] transition-colors">
                            Clear
                        </button>
                        <button @click="checkout" :disabled="!cartCount"
                            class="flex-1 bg-[#1a202c] disabled:bg-slate-300 disabled:cursor-not-allowed hover:bg-slate-800 text-white font-bold py-3 px-4 rounded-xl transition-all text-[15px] flex items-center justify-center gap-2 shadow-[0_4px_15px_rgba(26,32,44,0.15)]">
                            Proceed to Checkout
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </aside>
        </div>

        <!-- ── MOBILE CATEGORIES DRAWER ── -->
        <div v-if="isMobileCategoryOpen" class="lg:hidden fixed inset-0 z-50 flex">
            <!-- Overlay -->
            <div @click="isMobileCategoryOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <!-- Drawer -->
            <div class="relative flex w-4/5 max-w-sm flex-col bg-white shadow-2xl transition-transform transform translate-x-0">
                <div class="flex items-center justify-between p-4 border-b border-slate-100 bg-slate-50/50">
                    <h2 class="text-lg font-bold text-slate-800">Categories</h2>
                    <button @click="isMobileCategoryOpen = false" class="text-slate-400 hover:text-slate-600">
                        <svg class="w-6 h-6" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>
                
                <div class="p-3 border-b border-slate-100 bg-white">
                    <button
                        @click="clearCategory(); isMobileCategoryOpen = false"
                        :class="['flex items-center gap-3 px-4 py-3 text-sm font-semibold transition-all rounded-xl w-full text-left shadow-sm', !selectedCategory ? 'bg-[#00a669] text-white' : 'text-slate-700 bg-slate-50 border border-slate-200']">
                        <span class="text-lg">🏪</span>
                        <span>All Items</span>
                    </button>
                </div>

                <div class="px-3 pt-3 pb-2 border-b border-slate-100">
                    <div class="relative">
                        <input v-model="categorySearchQuery" type="text" placeholder="Search categories..." class="w-full bg-slate-50 border border-slate-200 rounded-lg pl-3 pr-8 py-2 text-sm text-slate-900 placeholder-slate-400 focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669]" />
                        <span v-if="categorySearchQuery" @click="categorySearchQuery = ''" class="absolute right-2 top-1/2 -translate-y-1/2 text-slate-400 cursor-pointer text-xs">✕</span>
                    </div>
                </div>

                <div class="flex-1 overflow-y-auto p-2 space-y-1">
                    <button v-for="cat in filteredCategories" :key="cat.id"
                        @click="selectCategory(cat.slug); isMobileCategoryOpen = false"
                        :class="['flex items-center gap-3 px-4 py-3 text-sm transition-all rounded-lg w-full text-left', selectedCategory === cat.slug ? 'bg-emerald-50 text-emerald-700 font-bold' : 'text-slate-700 active:bg-slate-50']">
                        <span class="text-lg leading-none opacity-80">{{ categoryIcon(cat.name) }}</span>
                        <span class="truncate">{{ cat.name }}</span>
                    </button>
                    <div v-if="!filteredCategories.length" class="text-center py-6 text-sm text-slate-400">No categories found</div>
                </div>
            </div>
        </div>

        <!-- ── MOBILE CART DRAWER ── -->
        <div v-if="isMobileCartOpen" class="xl:hidden fixed inset-0 z-50 flex justify-end">
            <!-- Overlay -->
            <div @click="isMobileCartOpen = false" class="fixed inset-0 bg-slate-900/50 backdrop-blur-sm transition-opacity"></div>
            <!-- Drawer -->
            <div class="relative flex w-[90%] max-w-md flex-col bg-white shadow-2xl transition-transform transform translate-x-0 h-full">
                <!-- Cart Header -->
                <div class="px-4 py-4 border-b border-slate-100 flex items-center justify-between bg-white z-10">
                    <div class="flex items-center gap-2">
                        <h2 class="text-xl font-extrabold text-slate-900">🛒 Your Cart</h2>
                        <span class="bg-emerald-100 text-emerald-800 text-xs font-bold px-2 py-0.5 rounded-full">{{ cartCount }} items</span>
                    </div>
                    <button @click="isMobileCartOpen = false" class="text-slate-400 hover:text-slate-600 bg-slate-100 p-1.5 rounded-full">
                        <svg class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="currentColor" stroke-width="2.5"><path stroke-linecap="round" stroke-linejoin="round" d="M6 18L18 6M6 6l12 12"/></svg>
                    </button>
                </div>

                <!-- Cart Items -->
                <div class="flex-1 overflow-y-auto p-4 space-y-3 bg-[#f8fafc]">
                    <div v-if="!cartCount" class="flex flex-col items-center justify-center h-full text-slate-400 pb-10">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                            <span class="text-4xl opacity-40">🛒</span>
                        </div>
                        <p class="text-[15px] font-bold text-slate-500 mb-1">Cart is empty</p>
                    </div>

                    <div v-for="(item, medicineId) in cart" :key="medicineId"
                        class="bg-white border border-slate-200 rounded-xl p-3 relative shadow-sm">
                        <button @click="removeFromCart(medicineId)" class="absolute -top-2 -right-2 w-7 h-7 bg-white border border-slate-200 rounded-full text-slate-400 flex items-center justify-center shadow-sm z-10">
                            ✕
                        </button>
                        <div class="flex items-start gap-3">
                            <div class="w-16 h-16 bg-slate-50 rounded-lg flex items-center justify-center border border-slate-100 overflow-hidden flex-shrink-0 p-1">
                                <img v-if="item.image" :src="`/storage/${item.image}`" @error="handleImageError" class="w-full h-full object-contain mix-blend-multiply" />
                                <div :class="{'hidden': item.image, 'flex': true}" class="w-full h-full items-center justify-center">
                                    <MedicineIcon :icon-id="getIconId(item)" />
                                </div>
                            </div>
                            <div class="flex-1 min-w-0 pt-0.5">
                                <div class="text-[14px] font-bold text-slate-900 leading-tight truncate pr-2 mb-1">{{ item.name }}</div>
                                <div class="text-[11px] text-slate-500 truncate">{{ item.category || 'Health Product' }}</div>
                                
                                <div class="flex justify-between items-end mt-3">
                                    <div class="text-[15px] font-bold text-emerald-600">৳{{ (item.sell_price * item.quantity).toFixed(2) }}</div>
                                    <div class="flex items-center gap-3 bg-slate-50 rounded-lg p-1 border border-slate-200">
                                        <button @click="updateQuantity(medicineId, -1)" class="w-8 h-8 rounded bg-white text-slate-700 flex items-center justify-center text-xl font-medium shadow-sm">−</button>
                                        <span class="w-6 text-center text-sm font-bold text-slate-900">{{ item.quantity }}</span>
                                        <button @click="updateQuantity(medicineId, 1)" class="w-8 h-8 rounded bg-white text-slate-700 flex items-center justify-center text-xl font-medium shadow-sm">+</button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Footer Summary -->
                <div class="p-5 border-t border-slate-200 bg-white shadow-[0_-10px_20px_rgba(0,0,0,0.03)] pb-safe">
                    <div class="flex justify-between items-end mb-4">
                        <span class="text-[18px] font-black text-slate-900">Total</span>
                        <span class="text-[28px] font-black text-[#00a669] leading-none">৳{{ cartTotal.toFixed(2) }}</span>
                    </div>

                    <div class="flex gap-2">
                        <button v-if="cartCount" @click="clearCart" class="px-4 py-3 bg-rose-50 text-rose-600 rounded-xl font-bold text-sm">
                            Clear
                        </button>
                        <button @click="checkout" :disabled="!cartCount"
                            class="flex-1 bg-[#1a202c] text-white font-bold py-3.5 px-4 rounded-xl text-base flex items-center justify-center gap-2">
                            Proceed to Checkout
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                        </button>
                    </div>
                </div>
            </div>
        </div>

    </StorefrontLayout>
</template>
