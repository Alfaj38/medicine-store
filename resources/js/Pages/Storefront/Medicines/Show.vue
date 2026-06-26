<script setup>
import { ref } from 'vue';
import { Link } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import SeoHead from '@/Components/SeoHead.vue';
import MedicineIcon from '@/Components/MedicineIcon.vue';
import { useCart } from '@/Composables/useCart';

const props = defineProps({
    company: Object,
    medicine: Object,
    alternatives: {
        type: Array,
        default: () => []
    },
    seo: Object,
});

const { addToCart } = useCart();
const quantity = ref(1);

const handleAddToCart = () => {
    // Add multiple if quantity > 1
    const itemWithQty = { ...props.medicine };
    for (let i = 0; i < quantity.value; i++) {
        addToCart(itemWithQty);
    }
    // Optionally trigger a notification or open cart here
};

const handleImageError = (e) => {
    e.target.style.display = 'none';
    e.target.nextElementSibling.style.display = 'flex';
};

const getIconId = (med) => {
    const name = (med.name || '').toLowerCase();
    const generic = (med.generic_name || '').toLowerCase();
    if (name.includes('napa') || generic.includes('paracetamol')) return 'tablet-white-split';
    if (name.includes('seclo') || generic.includes('omeprazole')) return 'capsule-pink-white';
    if (name.includes('syr') || generic.includes('syrup')) return 'bottle-orange';
    if (name.includes('drop')) return 'drop-blue';
    if (name.includes('cream') || name.includes('oint')) return 'tube-orange';
    return 'default';
};
</script>

<template>
    <SeoHead v-if="seo" :seo="seo" />

    <StorefrontLayout :company="company">
        <div class="bg-[#f4f7f6] min-h-[calc(100vh-73px)] py-8">
            <div class="max-w-[1200px] mx-auto px-4 sm:px-6 lg:px-8">
                
                <!-- Breadcrumbs -->
                <nav class="flex text-sm text-slate-500 mb-6" aria-label="Breadcrumb">
                    <ol class="inline-flex items-center space-x-1 md:space-x-3">
                        <li class="inline-flex items-center">
                            <Link :href="route('storefront.show', company.slug)" class="hover:text-emerald-600 transition-colors">Home</Link>
                        </li>
                        <li>
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                <Link :href="route('storefront.medicines', company.slug)" class="hover:text-emerald-600 transition-colors">Medicines</Link>
                            </div>
                        </li>
                        <li aria-current="page">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-slate-400 mx-1" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd" /></svg>
                                <span class="text-slate-800 font-medium truncate max-w-[200px]">{{ medicine.name }}</span>
                            </div>
                        </li>
                    </ol>
                </nav>

                <!-- Product Card -->
                <div class="bg-white rounded-[24px] border border-slate-200 shadow-sm overflow-hidden mb-8">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-0">
                        
                        <!-- Left: Image -->
                        <div class="p-8 md:p-12 flex items-center justify-center bg-[#f8fafc] border-b md:border-b-0 md:border-r border-slate-200">
                            <div class="relative w-full max-w-sm aspect-square bg-white rounded-3xl border border-slate-100 shadow-sm flex items-center justify-center p-6">
                                <img v-if="medicine.image" :src="`/storage/${medicine.image}`" @error="handleImageError" class="w-full h-full object-contain mix-blend-multiply" :alt="medicine.name" />
                                <div :class="{'hidden': medicine.image, 'flex': true}" class="w-full h-full items-center justify-center">
                                    <MedicineIcon :icon-id="getIconId(medicine)" class="w-32 h-32" />
                                </div>
                            </div>
                        </div>

                        <!-- Right: Details -->
                        <div class="p-8 md:p-12 flex flex-col">
                            
                            <div class="mb-6">
                                <div class="inline-block bg-emerald-50 text-emerald-700 text-[12px] font-bold px-3 py-1 rounded-full uppercase tracking-wider mb-3">
                                    {{ medicine.category || 'Health Product' }}
                                </div>
                                <h1 class="text-3xl font-extrabold text-slate-900 mb-2">{{ medicine.name }}</h1>
                                <p class="text-lg text-slate-500 font-medium mb-1">{{ medicine.generic_name }}</p>
                                <p v-if="medicine.manufacturer" class="text-sm text-slate-400 font-medium">By: <span class="text-slate-600">{{ medicine.manufacturer }}</span></p>
                            </div>

                            <div class="flex items-end gap-4 mb-8">
                                <div class="text-[40px] font-black text-[#00a669] leading-none">৳{{ parseFloat(medicine.sell_price || 0).toFixed(2) }}</div>
                                <div v-if="medicine.strength" class="text-sm font-bold text-slate-400 mb-1 border-l-2 border-slate-200 pl-4">{{ medicine.strength }} {{ medicine.dosage_form }}</div>
                            </div>

                            <div class="mb-8">
                                <p class="text-slate-600 leading-relaxed text-sm">
                                    {{ medicine.description || `Buy ${medicine.name} online from ${company.name}. We offer genuine medicines with fast home delivery.` }}
                                </p>
                            </div>

                            <div class="flex items-center gap-4 mt-auto pt-6 border-t border-slate-100">
                                <div class="flex items-center border border-slate-300 rounded-xl h-12 w-32">
                                    <button @click="quantity > 1 ? quantity-- : null" class="w-10 h-full flex items-center justify-center text-slate-500 hover:text-slate-900 transition-colors font-bold text-xl">−</button>
                                    <input type="number" v-model="quantity" class="w-12 h-full border-0 text-center font-bold text-slate-900 focus:ring-0 p-0 text-lg" min="1" />
                                    <button @click="quantity++" class="w-10 h-full flex items-center justify-center text-slate-500 hover:text-slate-900 transition-colors font-bold text-xl">+</button>
                                </div>
                                
                                <button @click="handleAddToCart" class="flex-1 bg-[#00a669] hover:bg-emerald-600 text-white font-bold h-12 px-6 rounded-xl transition-all shadow-[0_4px_15px_rgba(0,166,105,0.2)] flex items-center justify-center gap-2">
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"/></svg>
                                    Add to Cart
                                </button>
                            </div>

                        </div>
                    </div>
                </div>

                <!-- AEO Knowledge Blocks / Product Details -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Main Content -->
                    <div class="lg:col-span-2 space-y-6">
                        
                        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
                            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#00a669]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Medicine Overview
                            </h2>
                            <div class="prose prose-slate prose-sm max-w-none text-slate-600">
                                <p><strong>{{ medicine.name }}</strong> ({{ medicine.generic_name }}) is commonly used for its therapeutic benefits. Please ensure to follow your doctor's prescription when consuming this medicine.</p>
                                
                                <h3 class="text-lg font-bold text-slate-800 mt-6 mb-3">Key Details</h3>
                                <ul class="space-y-2">
                                    <li class="flex items-center gap-2"><span class="w-2 h-2 bg-emerald-400 rounded-full"></span> <strong>Generic:</strong> {{ medicine.generic_name || 'N/A' }}</li>
                                    <li class="flex items-center gap-2"><span class="w-2 h-2 bg-emerald-400 rounded-full"></span> <strong>Manufacturer:</strong> {{ medicine.manufacturer || 'N/A' }}</li>
                                    <li class="flex items-center gap-2"><span class="w-2 h-2 bg-emerald-400 rounded-full"></span> <strong>Dosage Form:</strong> {{ medicine.dosage_form || 'Tablet' }}</li>
                                </ul>
                            </div>
                        </div>

                        <!-- FAQ Block -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-8 shadow-sm">
                            <h2 class="text-xl font-bold text-slate-900 mb-6 flex items-center gap-2">
                                <svg class="w-5 h-5 text-[#00a669]" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.228 9c.549-1.165 2.03-2 3.772-2 2.21 0 4 1.343 4 3 0 1.4-1.278 2.575-3.006 2.907-.542.104-.994.54-.994 1.093m0 3h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                Frequently Asked Questions
                            </h2>
                            <div class="space-y-4">
                                <div class="pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                                    <h4 class="font-bold text-slate-800 text-sm mb-1">What is the price of {{ medicine.name }} in Bangladesh?</h4>
                                    <p class="text-sm text-slate-600">The current price of {{ medicine.name }} is {{ parseFloat(medicine.sell_price || 0).toFixed(2) }} BDT at {{ company.name }}.</p>
                                </div>
                                <div v-if="medicine.generic_name" class="pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                                    <h4 class="font-bold text-slate-800 text-sm mb-1">What is the generic name of {{ medicine.name }}?</h4>
                                    <p class="text-sm text-slate-600">The generic name of {{ medicine.name }} is {{ medicine.generic_name }}.</p>
                                </div>
                                <div class="pb-4 border-b border-slate-100 last:border-0 last:pb-0">
                                    <h4 class="font-bold text-slate-800 text-sm mb-1">Is {{ medicine.name }} available for home delivery?</h4>
                                    <p class="text-sm text-slate-600">Yes, you can order {{ medicine.name }} online from {{ company.name }} and get fast home delivery.</p>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Right Sidebar -->
                    <div class="space-y-6">
                        
                        <!-- Trust Badges -->
                        <div class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Why buy from us?</h3>
                            <ul class="space-y-4">
                                <li class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 text-[#00a669]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">100% Genuine</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Authentic medicines guaranteed</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 text-[#00a669]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Fast Delivery</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Same-day delivery available</p>
                                    </div>
                                </li>
                                <li class="flex items-start gap-3">
                                    <div class="w-8 h-8 rounded-full bg-emerald-50 flex items-center justify-center flex-shrink-0 text-[#00a669]">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                                    </div>
                                    <div>
                                        <p class="text-sm font-bold text-slate-800">Verified Pharmacy</p>
                                        <p class="text-[11px] text-slate-500 mt-0.5">Licensed by authorities</p>
                                    </div>
                                </li>
                            </ul>
                        </div>

                        <!-- Alternatives (GEO) -->
                        <div v-if="alternatives && alternatives.length > 0" class="bg-white rounded-2xl border border-slate-200 p-6 shadow-sm">
                            <h3 class="text-sm font-bold text-slate-900 mb-4 uppercase tracking-wider">Alternative Brands</h3>
                            <div class="space-y-4">
                                <Link v-for="alt in alternatives" :key="alt.id" :href="route('storefront.medicine.details', { company: company.slug, medicine: alt.slug || alt.id })" class="flex items-center gap-3 group">
                                    <div class="w-12 h-12 rounded-xl bg-slate-50 border border-slate-100 flex items-center justify-center p-2 flex-shrink-0">
                                        <img v-if="alt.image" :src="`/storage/${alt.image}`" @error="handleImageError" class="w-full h-full object-contain mix-blend-multiply" />
                                        <div :class="{'hidden': alt.image, 'flex': true}" class="w-full h-full items-center justify-center">
                                            <MedicineIcon :icon-id="getIconId(alt)" class="w-8 h-8" />
                                        </div>
                                    </div>
                                    <div class="flex-1 min-w-0">
                                        <h4 class="text-sm font-bold text-slate-800 truncate group-hover:text-emerald-600 transition-colors">{{ alt.name }}</h4>
                                        <p class="text-[11px] text-slate-500 truncate">{{ alt.manufacturer }}</p>
                                    </div>
                                    <div class="text-sm font-black text-emerald-600">
                                        ৳{{ parseFloat(alt.sell_price || 0).toFixed(2) }}
                                    </div>
                                </Link>
                            </div>
                        </div>

                    </div>
                </div>

            </div>
        </div>
    </StorefrontLayout>
</template>
