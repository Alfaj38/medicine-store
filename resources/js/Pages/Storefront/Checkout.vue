<script setup>
import { ref, computed } from 'vue';
import { Head, router, useForm } from '@inertiajs/vue3';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';
import { useCart } from '@/Composables/useCart';
import Swal from 'sweetalert2';

const props = defineProps({
    company: Object,
});

const { cart, cartCount, cartTotal, clearCart } = useCart();

const form = useForm({
    name: '',
    phone: '',
    email: '',
    address: '',
    city: 'Dhaka',
    prescription: null,
    payment_method: 'cod',
    delivery_method: 'home',
    cart: [],
});

const deliveryFee = computed(() => {
    return form.delivery_method === 'home' ? 60 : 0;
});

const grandTotal = computed(() => {
    return cartTotal.value + deliveryFee.value;
});

const handlePrescriptionUpload = (e) => {
    form.prescription = e.target.files[0];
};

const submitOrder = () => {
    if (!form.name || !form.phone || (form.delivery_method === 'home' && !form.address)) {
        Swal.fire({
            icon: 'error',
            title: 'Missing Information',
            text: 'Please fill in all required customer details.',
            confirmButtonColor: '#00a669',
            customClass: { popup: 'rounded-2xl' }
        });
        return;
    }

    if (cartCount.value === 0) {
        Swal.fire({
            icon: 'warning',
            title: 'Empty Cart',
            text: 'Your cart is empty. Please add items before checking out.',
            confirmButtonColor: '#00a669',
            customClass: { popup: 'rounded-2xl' }
        });
        return;
    }

    // Populate cart data
    form.cart = Object.values(cart.value);

    form.post(route('storefront.placeOrder', props.company.slug), {
        preserveScroll: true,
        onSuccess: () => {
            clearCart();
        },
        onError: (errors) => {
            Swal.fire({
                icon: 'error',
                title: 'Order Failed',
                text: Object.values(errors)[0] || 'There was an error processing your order.',
                confirmButtonColor: '#00a669',
                customClass: { popup: 'rounded-2xl' }
            });
        }
    });
};
</script>

<template>
    <Head :title="`Checkout - ${company.name}`" />

    <StorefrontLayout :company="company" hide-cart>
        <div class="w-full mx-auto px-4 sm:px-6 lg:px-8 py-8 md:py-12">
            
            <div class="mb-8 flex items-center justify-between">
                <div>
                    <h1 class="text-3xl font-extrabold text-slate-900 tracking-tight">Checkout</h1>
                    <p class="text-slate-500 mt-1">Please provide your details to complete the order</p>
                </div>
                <button type="button" @click="router.get(route('storefront.search', company.slug))" class="text-[#00a669] font-bold hover:underline flex items-center gap-2">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/></svg>
                    Back to Store
                </button>
            </div>

            <div v-if="cartCount === 0" class="bg-white rounded-2xl shadow-sm border border-slate-200 p-12 text-center">
                <span class="text-6xl mb-4 block">🛒</span>
                <h2 class="text-2xl font-bold text-slate-800 mb-2">Your cart is empty</h2>
                <p class="text-slate-500 mb-6">Looks like you haven't added anything to your cart yet.</p>
                <button type="button" @click="router.get(route('storefront.search', company.slug))" class="bg-[#00a669] text-white px-8 py-3 rounded-xl font-bold hover:bg-emerald-600 transition-colors shadow-sm">
                    Start Shopping
                </button>
            </div>

            <form v-else @submit.prevent="submitOrder" class="flex flex-col lg:flex-row gap-8">
                <!-- Left Column: Forms -->
                <div class="flex-1 space-y-6">
                    
                    <!-- Customer Details -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="bg-emerald-100 text-[#00a669] w-8 h-8 rounded-full flex items-center justify-center text-sm">1</span>
                            Customer Details
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Full Name <span class="text-rose-500">*</span></label>
                                <input v-model="form.name" type="text" placeholder="John Doe" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669] transition-all" />
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Phone Number <span class="text-rose-500">*</span></label>
                                <input v-model="form.phone" type="text" placeholder="017XXXXXXXX" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669] transition-all" />
                            </div>
                            <div class="md:col-span-2">
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Email Address (Optional)</label>
                                <input v-model="form.email" type="email" placeholder="john@example.com" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669] transition-all" />
                            </div>
                        </div>
                    </div>

                    <!-- Delivery Information -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-4 flex items-center gap-2">
                            <span class="bg-emerald-100 text-[#00a669] w-8 h-8 rounded-full flex items-center justify-center text-sm">2</span>
                            Delivery Information
                        </h2>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-4 mb-5">
                            <div @click="form.delivery_method = 'home'" :class="['border-2 rounded-xl p-4 cursor-pointer transition-all', form.delivery_method === 'home' ? 'border-[#00a669] bg-emerald-50' : 'border-slate-200 hover:border-slate-300']">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-bold text-slate-900">Home Delivery</h3>
                                    <div v-if="form.delivery_method === 'home'" class="w-5 h-5 bg-[#00a669] rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div v-else class="w-5 h-5 border-2 border-slate-300 rounded-full"></div>
                                </div>
                                <p class="text-xs text-slate-500">Delivered directly to your door</p>
                            </div>
                            
                            <div @click="form.delivery_method = 'pickup'" :class="['border-2 rounded-xl p-4 cursor-pointer transition-all', form.delivery_method === 'pickup' ? 'border-[#00a669] bg-emerald-50' : 'border-slate-200 hover:border-slate-300']">
                                <div class="flex items-center justify-between mb-1">
                                    <h3 class="font-bold text-slate-900">Store Pickup</h3>
                                    <div v-if="form.delivery_method === 'pickup'" class="w-5 h-5 bg-[#00a669] rounded-full flex items-center justify-center">
                                        <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                    </div>
                                    <div v-else class="w-5 h-5 border-2 border-slate-300 rounded-full"></div>
                                </div>
                                <p class="text-xs text-slate-500">Pick up from our pharmacy (Free)</p>
                            </div>
                        </div>

                        <div v-if="form.delivery_method === 'home'" class="space-y-4">
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">City <span class="text-rose-500">*</span></label>
                                <select v-model="form.city" class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669] transition-all">
                                    <option value="Dhaka">Dhaka</option>
                                    <option value="Chittagong">Chittagong</option>
                                    <option value="Sylhet">Sylhet</option>
                                    <option value="Other">Other</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-sm font-semibold text-slate-700 mb-1">Detailed Address <span class="text-rose-500">*</span></label>
                                <textarea v-model="form.address" rows="3" placeholder="House/Flat No, Street, Area..." class="w-full bg-slate-50 border border-slate-200 rounded-xl px-4 py-2.5 text-sm focus:outline-none focus:border-[#00a669] focus:ring-1 focus:ring-[#00a669] transition-all"></textarea>
                            </div>
                        </div>
                        <div v-else class="p-4 bg-slate-50 rounded-xl border border-slate-200">
                            <h4 class="font-bold text-slate-800 text-sm mb-1">Pharmacy Location</h4>
                            <p class="text-sm text-slate-600">{{ company.address || 'Address not provided' }}</p>
                            <p class="text-sm text-slate-600 mt-1">Phone: {{ company.phone }}</p>
                        </div>
                    </div>

                    <!-- Prescription Upload -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h2 class="text-lg font-bold text-slate-900 mb-1 flex items-center gap-2">
                            <span class="bg-emerald-100 text-[#00a669] w-8 h-8 rounded-full flex items-center justify-center text-sm">3</span>
                            Upload Prescription (Optional)
                        </h2>
                        <p class="text-sm text-slate-500 mb-4 ml-10">Required only for prescription medicines.</p>
                        
                        <div class="border-2 border-dashed border-slate-300 rounded-xl p-8 text-center bg-slate-50 hover:bg-slate-100 transition-colors cursor-pointer relative group">
                            <input type="file" @change="handlePrescriptionUpload" accept="image/*,.pdf" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer z-10" />
                            <div v-if="!form.prescription">
                                <span class="text-4xl block mb-2 opacity-60">📄</span>
                                <p class="text-sm font-bold text-slate-700">Click to upload or drag and drop</p>
                                <p class="text-xs text-slate-500 mt-1">JPG, PNG or PDF (Max 5MB)</p>
                            </div>
                            <div v-else class="flex items-center justify-center gap-3">
                                <span class="text-3xl">✅</span>
                                <div class="text-left">
                                    <p class="text-sm font-bold text-slate-800">{{ form.prescription.name }}</p>
                                    <p class="text-xs text-[#00a669]">File attached successfully</p>
                                </div>
                            </div>
                        </div>
                    </div>

                </div>

                <!-- Right Column: Summary & Payment -->
                <div class="w-full lg:w-[400px]">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 sticky top-24 overflow-hidden">
                        
                        <div class="p-6 border-b border-slate-100">
                            <h2 class="text-lg font-bold text-slate-900 mb-4">Order Summary</h2>
                            
                            <div class="space-y-4 max-h-[300px] overflow-y-auto pr-2 scrollbar-thin scrollbar-thumb-slate-200">
                                <div v-for="(item, id) in cart" :key="id" class="flex items-start justify-between">
                                    <div class="flex items-center gap-3">
                                        <div class="w-10 h-10 bg-slate-50 border border-slate-100 rounded-lg flex items-center justify-center flex-shrink-0">
                                            <span class="text-lg opacity-60">💊</span>
                                        </div>
                                        <div>
                                            <p class="text-sm font-bold text-slate-800 leading-tight">{{ item.name }}</p>
                                            <p class="text-xs text-slate-500">Qty: {{ item.quantity }}</p>
                                        </div>
                                    </div>
                                    <div class="text-sm font-bold text-slate-700">
                                        ৳{{ (item.sell_price * item.quantity).toFixed(2) }}
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="p-6 bg-slate-50 border-b border-slate-100">
                            <h3 class="font-bold text-slate-900 mb-3 text-sm">Payment Method</h3>
                            <div class="border border-[#00a669] bg-emerald-50 rounded-xl p-3 flex items-center gap-3">
                                <div class="w-5 h-5 bg-[#00a669] rounded-full flex items-center justify-center">
                                    <svg class="w-3 h-3 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M5 13l4 4L19 7"/></svg>
                                </div>
                                <div>
                                    <p class="text-sm font-bold text-slate-900">Cash on Delivery</p>
                                    <p class="text-xs text-slate-600">Pay when you receive the order</p>
                                </div>
                            </div>
                        </div>

                        <div class="p-6">
                            <div class="flex justify-between items-center mb-2">
                                <span class="text-sm text-slate-600">Subtotal</span>
                                <span class="text-sm font-bold text-slate-800">৳{{ cartTotal.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between items-center mb-4 pb-4 border-b border-dashed border-slate-200">
                                <span class="text-sm text-slate-600">Delivery Fee</span>
                                <span class="text-sm font-bold text-slate-800">৳{{ deliveryFee.toFixed(2) }}</span>
                            </div>
                            <div class="flex justify-between items-end mb-6">
                                <span class="text-lg font-bold text-slate-900">Total</span>
                                <span class="text-3xl font-black text-[#00a669]">৳{{ grandTotal.toFixed(2) }}</span>
                            </div>

                            <button type="submit" :disabled="form.processing"
                                class="w-full bg-[#1a202c] disabled:bg-slate-500 hover:bg-slate-800 text-white font-bold py-4 px-4 rounded-xl transition-all text-lg flex items-center justify-center gap-2 shadow-[0_4px_15px_rgba(26,32,44,0.15)]">
                                <span v-if="form.processing">Processing...</span>
                                <span v-else class="flex items-center gap-2">
                                    Place Order
                                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2.5" d="M14 5l7 7m0 0l-7 7m7-7H3"/></svg>
                                </span>
                            </button>
                        </div>

                    </div>
                </div>
            </form>
            
        </div>
    </StorefrontLayout>
</template>
