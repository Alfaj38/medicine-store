<script setup>
import { ref, computed, watch } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import Layout from './Layout.vue';

const props = defineProps({
    company: Object,
    banks: {
        type: Array,
        default: () => []
    }
});

const page = usePage();

const getTabFromUrl = () => {
    if (typeof window !== 'undefined') {
        const params = new URLSearchParams(window.location.search);
        return params.get('tab') || 'general';
    }
    return 'general';
};

const activeTab = ref(getTabFromUrl());

watch(
    () => page.url,
    () => {
        const urlTab = getTabFromUrl();
        if (activeTab.value !== urlTab) {
            activeTab.value = urlTab;
        }
    }
);

watch(activeTab, (newTab) => {
    const currentUrlTab = getTabFromUrl();
    if (currentUrlTab !== newTab) {
        router.get(
            route('company.settings.index', { tab: newTab === 'general' ? undefined : newTab }), 
            {}, 
            { preserveState: true, preserveScroll: true, replace: true }
        );
    }
});

// Main Profile Form
const form = useForm({
    name: props.company.name || '',
    email: props.company.email || '',
    phone: props.company.phone || '',
    alternative_phone: props.company.alternative_phone || '',
    website: props.company.website || '',
    address: props.company.address || '',
    business_type: props.company.business_type || '',
    year_established: props.company.year_established || '',
    registration_number: props.company.registration_number || '',
    tax_id: props.company.tax_id || '',
    trade_license_number: props.company.trade_license_number || '',
    vat_registration_number: props.company.vat_registration_number || '',
    bin_number: props.company.bin_number || '',
    facebook_page: props.company.facebook_page || '',
    about_company: props.company.about_company || '',
    
    // Regulatory
    drug_license_number: props.company.drug_license_number || '',
    drug_license_issue_date: props.company.drug_license_issue_date || '',
    drug_license_expiry_date: props.company.drug_license_expiry_date || '',
    issuing_authority: props.company.issuing_authority || '',
    pharmacist_name: props.company.pharmacist_name || '',
    pharmacist_registration_number: props.company.pharmacist_registration_number || '',
    pharmacist_mobile: props.company.pharmacist_mobile || '',

    // Subscription
    subscription_package: props.company.subscription_package || 'Enterprise',
    subscription_start: props.company.subscription_start || '',
    subscription_expiry: props.company.subscription_expiry || '',
    max_users: props.company.max_users || '',
    max_branches: props.company.max_branches || '',
    storage_limit: props.company.storage_limit || '',

    // Preferences
    language: props.company.language || 'English',
    timezone: props.company.timezone || 'Asia/Dhaka',
    currency: props.company.currency || 'BDT',
    email_notifications: props.company.email_notifications !== false,
    sms_notifications: props.company.sms_notifications !== false,
    
    logo: null,
});

const logoPreview = ref(props.company.logo ? `/storage/${props.company.logo}` : null);

const handleLogoUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    form.logo = file;
    logoPreview.value = URL.createObjectURL(file);
};

const saveProfile = () => {
    form.post(route('company.settings.update'), {
        preserveScroll: true,
        onSuccess: () => {
            // Optional: Show a nice toast here
        }
    });
};

// Bank Account Form
const showBankModal = ref(false);
const editingBank = ref(null);
const bankForm = useForm({
    bank_name: '',
    account_name: '',
    account_number: '',
    account_type: 'Current',
    branch_name: '',
    routing_number: '',
    is_active: true
});

const availableBranches = computed(() => {
    if (!bankForm.bank_name) return [];
    const selectedBank = props.banks.find(b => b.name === bankForm.bank_name);
    return selectedBank && selectedBank.branches ? selectedBank.branches : [];
});

watch(() => bankForm.routing_number, (newVal) => {
    if (newVal) {
        // Search across all banks
        for (const bank of props.banks) {
            if (bank.branches) {
                const matchedBranch = bank.branches.find(b => b.routing_number === newVal);
                if (matchedBranch) {
                    bankForm.bank_name = bank.name;
                    // Need to wait for next tick or availableBranches to update before setting branch
                    setTimeout(() => {
                        bankForm.branch_name = matchedBranch.name;
                    }, 50);
                    break;
                }
            }
        }
    }
});

watch(() => bankForm.branch_name, (newVal) => {
    if (newVal && availableBranches.value) {
        const branch = availableBranches.value.find(b => b.name === newVal);
        if (branch && branch.routing_number) {
            bankForm.routing_number = branch.routing_number;
        }
    }
});

const openBankModal = (bank = null) => {
    if (bank) {
        editingBank.value = bank;
        bankForm.bank_name = bank.bank_name;
        bankForm.account_name = bank.account_name;
        bankForm.account_number = bank.account_number;
        bankForm.account_type = bank.account_type;
        bankForm.branch_name = bank.branch_name;
        bankForm.routing_number = bank.routing_number;
        bankForm.is_active = bank.is_active;
    } else {
        editingBank.value = null;
        bankForm.reset();
    }
    showBankModal.value = true;
};

const saveBankAccount = () => {
    if (editingBank.value) {
        bankForm.put(route('company.bank-accounts.update', editingBank.value.id), {
            preserveScroll: true,
            onSuccess: () => { showBankModal.value = false; }
        });
    } else {
        bankForm.post(route('company.bank-accounts.store'), {
            preserveScroll: true,
            onSuccess: () => { showBankModal.value = false; }
        });
    }
};

const deleteBankAccount = (id) => {
    if (confirm('Delete this bank account?')) {
        router.delete(route('company.bank-accounts.destroy', id), { preserveScroll: true });
    }
};

// Document Upload Form
const docForm = useForm({
    document_type: 'Trade License',
    file: null
});

const handleDocUpload = (e) => {
    const file = e.target.files[0];
    if (!file) return;
    docForm.file = file;
    docForm.post(route('company.documents.store'), {
        preserveScroll: true,
        onSuccess: () => {
            docForm.reset();
            e.target.value = null; // Clear input
        }
    });
};

const deleteDocument = (id) => {
    if (confirm('Delete this document?')) {
        router.delete(route('company.documents.destroy', id), { preserveScroll: true });
    }
};

// Formatting helpers
const formatDate = (dateString) => {
    if (!dateString) return 'N/A';
    const d = new Date(dateString);
    return d.toLocaleDateString('en-US', { year: 'numeric', month: 'short', day: 'numeric' });
};
</script>

<template>
    <Layout title="Company Profile">
        <!-- Header Section -->
        <div class="flex justify-between items-end mb-6">
            <div>
                <h1 class="text-2xl font-bold text-slate-900">Company Profile</h1>
                <p class="text-sm text-slate-500 mt-1">Update your company's general information and contact details.</p>
            </div>
            <div class="flex items-center gap-4">
                <div v-if="form.recentlySuccessful || bankForm.recentlySuccessful" class="flex items-center text-emerald-600 text-sm font-medium transition-all duration-300">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    <span v-if="bankForm.recentlySuccessful">Bank account saved!</span>
                    <span v-else>All changes saved</span>
                </div>
                <a :href="route('storefront.show', company.slug)" target="_blank" class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-slate-50 flex items-center transition-colors">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    View Profile
                </a>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-slate-200 mb-6 overflow-x-auto overflow-y-hidden no-scrollbar">
            <nav class="-mb-px flex space-x-8 min-w-max px-2">
                <a href="#general" @click.prevent="activeTab = 'general'" :class="activeTab === 'general' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">General Information</a>
                <a href="#business" @click.prevent="activeTab = 'business'" :class="activeTab === 'business' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Business Details</a>
                <a href="#regulatory" @click.prevent="activeTab = 'regulatory'" :class="activeTab === 'regulatory' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Regulatory Information</a>
                <a href="#bank" @click.prevent="activeTab = 'bank'" :class="activeTab === 'bank' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Bank Accounts</a>
                <a href="#docs" @click.prevent="activeTab = 'docs'" :class="activeTab === 'docs' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Documents</a>
                <a href="#subscription" @click.prevent="activeTab = 'subscription'" :class="activeTab === 'subscription' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Subscription & Package</a>
                <a href="#preferences" @click.prevent="activeTab = 'preferences'" :class="activeTab === 'preferences' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Preferences</a>
            </nav>
        </div>

        <div class="pb-20">
            <!-- General Information Tab -->
            <div v-show="activeTab === 'general'" class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Left Column -->
                <div class="lg:col-span-2 space-y-6">
                    <!-- Basic Information Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Basic Information</h3>
                        
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                                    <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.name}">
                                    <p v-if="form.errors.name" class="mt-1 text-xs text-red-500">{{ form.errors.name }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                    <input v-model="form.email" type="email" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.email}">
                                    <p v-if="form.errors.email" class="mt-1 text-xs text-red-500">{{ form.errors.email }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Website</label>
                                    <input v-model="form.website" type="text" placeholder="https://www.example.com" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.website}">
                                    <p v-if="form.errors.website" class="mt-1 text-xs text-red-500">{{ form.errors.website }}</p>
                                </div>
                            </div>

                            <!-- Right Side Inputs & Logo -->
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Company Logo</label>
                                    <div class="flex items-center gap-4">
                                        <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden flex-shrink-0" :class="{'border-red-500': form.errors.logo}">
                                            <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain">
                                            <div v-else class="text-emerald-600 font-bold text-xl">{{ form.name ? form.name.charAt(0) : 'C' }}</div>
                                        </div>
                                        <div class="flex-1">
                                            <label class="cursor-pointer border border-dashed rounded-lg flex flex-col items-center justify-center py-3 hover:bg-slate-50 transition-colors" :class="form.errors.logo ? 'border-red-500 bg-red-50 hover:bg-red-100' : 'border-slate-300'">
                                                <svg class="w-5 h-5 text-slate-400 mb-1" :class="{'text-red-400': form.errors.logo}" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                                <span class="text-xs font-medium text-slate-600" :class="{'text-red-600': form.errors.logo}">Upload Logo</span>
                                                <span class="text-[10px] text-slate-400" :class="{'text-red-400': form.errors.logo}">PNG, JPG (Max 2MB)</span>
                                                <input type="file" class="hidden" @change="handleLogoUpload" accept="image/*">
                                            </label>
                                        </div>
                                    </div>
                                    <p v-if="form.errors.logo" class="mt-1 text-xs text-red-500">{{ form.errors.logo }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                    <input v-model="form.phone" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.phone}">
                                    <p v-if="form.errors.phone" class="mt-1 text-xs text-red-500">{{ form.errors.phone }}</p>
                                </div>
                                <div>
                                    <label class="block text-xs font-semibold text-slate-700 mb-1">Alternative Phone</label>
                                    <input v-model="form.alternative_phone" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.alternative_phone}">
                                    <p v-if="form.errors.alternative_phone" class="mt-1 text-xs text-red-500">{{ form.errors.alternative_phone }}</p>
                                </div>
                            </div>

                            <!-- Full Width Address -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Physical Address <span class="text-red-500">*</span></label>
                                <textarea v-model="form.address" rows="2" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm" :class="{'border-red-500 focus:border-red-500 focus:ring-red-500': form.errors.address}"></textarea>
                                <p v-if="form.errors.address" class="mt-1 text-xs text-red-500">{{ form.errors.address }}</p>
                            </div>
                        </div>
                    </div>

                    <!-- Additional Information Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Additional Information</h3>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">About Company</label>
                            <div class="relative">
                                <textarea v-model="form.about_company" rows="4" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm text-slate-600 placeholder-slate-400"></textarea>
                                <div class="absolute bottom-2 right-2 text-xs text-slate-400">{{ form.about_company?.length || 0 }}/500</div>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Right Column -->
                <div class="space-y-6">
                    <!-- Company Status -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Company Status</h3>
                            <span class="px-2 py-0.5 rounded text-[10px] font-bold bg-emerald-100 text-emerald-800 uppercase">Active</span>
                        </div>
                        <ul class="space-y-3">
                            <li class="flex items-start justify-between text-sm">
                                <div class="flex items-center text-slate-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                    Member Since
                                </div>
                                <div class="font-medium text-slate-800">{{ formatDate(company.created_at) }}</div>
                            </li>
                            <li class="flex items-start justify-between text-sm">
                                <div class="flex items-center text-slate-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                    Last Updated
                                </div>
                                <div class="font-medium text-slate-800">{{ formatDate(company.updated_at) }}</div>
                            </li>
                            <li class="flex items-start justify-between text-sm">
                                <div class="flex items-center text-slate-500">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path></svg>
                                    Updated By
                                </div>
                                <div class="font-medium text-slate-800">Admin User</div>
                            </li>
                        </ul>
                    </div>

                    <!-- Quick Actions -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <h3 class="text-sm font-bold text-slate-800 mb-4">Quick Actions</h3>
                        <div class="space-y-2">
                            <a :href="route('storefront.show', company.slug)" target="_blank" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-slate-400 mr-3 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                    <div class="text-left">
                                        <div class="text-sm font-semibold text-slate-700">View Company Profile</div>
                                        <div class="text-[10px] text-slate-500">See how your profile appears to others</div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                            <a :href="route('company.settings.export')" target="_blank" class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                                <div class="flex items-center">
                                    <svg class="w-5 h-5 text-slate-400 mr-3 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                    <div class="text-left">
                                        <div class="text-sm font-semibold text-slate-700">Export Profile</div>
                                        <div class="text-[10px] text-slate-500">Download company profile as PDF</div>
                                    </div>
                                </div>
                                <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                            </a>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Business Details Tab -->
            <div v-show="activeTab === 'business'" class="grid grid-cols-1 gap-6">
                <div class="max-w-4xl space-y-6">
                    <!-- Business Details Card -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Business Details</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <!-- Business Type -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Type <span class="text-red-500">*</span></label>
                                <select v-model="form.business_type" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10 bg-white">
                                    <option value="">Select Business Type</option>
                                    <option value="Retail Pharmacy">Retail Pharmacy</option>
                                    <option value="Wholesale Pharmacy">Wholesale Pharmacy</option>
                                    <option value="Distributor">Distributor</option>
                                    <option value="Manufacturer">Manufacturer</option>
                                    <option value="Hospital Pharmacy">Hospital Pharmacy</option>
                                    <option value="Clinic Pharmacy">Clinic Pharmacy</option>
                                </select>
                            </div>

                            <!-- Year Established -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Year Established</label>
                                <input v-model="form.year_established" type="number" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Company Registration No -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Company Registration No</label>
                                <input v-model="form.registration_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Trade License No -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Trade License No</label>
                                <input v-model="form.trade_license_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- TIN Number -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">TIN Number</label>
                                <input v-model="form.tax_id" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- VAT Registration No -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">VAT Registration No</label>
                                <input v-model="form.vat_registration_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- BIN Number -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">BIN Number</label>
                                <input v-model="form.bin_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Website -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Website</label>
                                <input v-model="form.website" type="url" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Facebook Page -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Facebook Page</label>
                                <input v-model="form.facebook_page" type="url" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Business Description -->
                            <div class="md:col-span-2">
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Business Description</label>
                                <textarea v-model="form.about_company" rows="4" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Regulatory Information Tab -->
            <div v-show="activeTab === 'regulatory'" class="grid grid-cols-1 gap-6">
                <div class="max-w-4xl space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Regulatory Information</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <!-- Drug License No -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Drug License No</label>
                                <input v-model="form.drug_license_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Drug License Issue Date -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Issue Date</label>
                                <input v-model="form.drug_license_issue_date" type="date" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Drug License Expiry Date -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Expiry Date</label>
                                <input v-model="form.drug_license_expiry_date" type="date" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Issuing Authority -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Issuing Authority</label>
                                <input v-model="form.issuing_authority" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Responsible Pharmacist -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Responsible Pharmacist</label>
                                <input v-model="form.pharmacist_name" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Pharmacist Registration No -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pharmacist Registration No</label>
                                <input v-model="form.pharmacist_registration_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>

                            <!-- Pharmacist Mobile -->
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Pharmacist Mobile</label>
                                <input v-model="form.pharmacist_mobile" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Bank Details Tab -->
            <div v-show="activeTab === 'bank'" class="grid grid-cols-1 gap-6">
                <div class="max-w-4xl space-y-6">
                    <!-- Bank Accounts -->
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Bank Accounts</h3>
                            <button @click="openBankModal()" class="text-xs font-semibold text-emerald-600 bg-emerald-50 border border-emerald-100 rounded-md px-2 py-1 hover:bg-emerald-100 transition-colors flex items-center">
                                <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path></svg> Add Account
                            </button>
                        </div>
                        
                        <div class="space-y-3">
                            <div v-for="bank in company.bank_accounts" :key="bank.id" class="flex justify-between items-center border-b border-slate-100 pb-3 last:border-0 last:pb-0">
                                <div>
                                    <div class="text-xs font-bold text-slate-800">{{ bank.bank_name }} <span v-if="bank.branch_name" class="text-slate-500 font-normal">- {{ bank.branch_name }}</span></div>
                                    <div class="text-[10px] text-slate-600 mb-0.5">{{ bank.account_name || 'No Account Name' }}</div>
                                    <div class="text-[10px] text-slate-500 font-mono flex items-center gap-2">
                                        <span>A/C: {{ bank.account_number }}</span>
                                        <span v-if="bank.routing_number" class="text-slate-400">&bull;</span>
                                        <span v-if="bank.routing_number">Routing: {{ bank.routing_number }}</span>
                                    </div>
                                </div>
                                <div class="flex items-center gap-2">
                                    <span v-if="bank.is_active" class="px-1.5 py-0.5 rounded text-[9px] font-bold bg-emerald-100 text-emerald-800 uppercase">Active</span>
                                    <button @click="openBankModal(bank)" class="text-slate-400 hover:text-indigo-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path></svg></button>
                                    <button @click="deleteBankAccount(bank.id)" class="text-slate-400 hover:text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </div>
                            <div v-if="!company.bank_accounts || company.bank_accounts.length === 0" class="text-xs text-slate-500 text-center py-2">
                                No bank accounts added.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Documents Tab -->
            <div v-show="activeTab === 'docs'" class="grid grid-cols-1 gap-6">
                <div class="max-w-4xl space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Documents</h3>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Trade License</label>
                                <input type="file" @change="(e) => { docForm.document_type = 'Trade License'; handleDocUpload(e); }" class="w-full rounded-lg border border-slate-300 text-sm p-1.5 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Drug License</label>
                                <input type="file" @change="(e) => { docForm.document_type = 'Drug License'; handleDocUpload(e); }" class="w-full rounded-lg border border-slate-300 text-sm p-1.5 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">TIN Certificate</label>
                                <input type="file" @change="(e) => { docForm.document_type = 'TIN Certificate'; handleDocUpload(e); }" class="w-full rounded-lg border border-slate-300 text-sm p-1.5 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">VAT Certificate</label>
                                <input type="file" @change="(e) => { docForm.document_type = 'VAT Certificate'; handleDocUpload(e); }" class="w-full rounded-lg border border-slate-300 text-sm p-1.5 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Company Logo</label>
                                <input type="file" @change="handleLogoUpload" class="w-full rounded-lg border border-slate-300 text-sm p-1.5 focus:border-emerald-500 focus:ring-emerald-500 bg-white">
                            </div>
                        </div>

                        <div class="mt-8 space-y-2">
                            <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Uploaded Documents</div>
                            <div v-for="doc in company.documents" :key="doc.id" class="flex justify-between items-center bg-slate-50 border border-slate-200 rounded-lg p-2">
                                <div class="flex items-center">
                                    <svg class="w-4 h-4 text-emerald-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
                                    <div>
                                        <div class="text-xs font-semibold text-slate-700">{{ doc.document_type }}</div>
                                        <div class="text-[9px] text-slate-500">Uploaded on {{ formatDate(doc.created_at) }}</div>
                                    </div>
                                </div>
                                <div class="flex gap-2">
                                    <a :href="`/storage/${doc.file_path}`" target="_blank" class="text-slate-400 hover:text-emerald-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-4l-4 4m0 0l-4-4m4 4V4"></path></svg></a>
                                    <button @click="deleteDocument(doc.id)" class="text-slate-400 hover:text-red-600"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path></svg></button>
                                </div>
                            </div>
                            <div v-if="!company.documents || company.documents.length === 0" class="text-xs text-slate-500 text-center py-2">
                                No documents uploaded.
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Subscription & Package Tab -->
            <div v-show="activeTab === 'subscription'" class="grid grid-cols-1 gap-6">
                <div class="max-w-4xl space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Subscription & Package</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Current Package</label>
                                <input v-model="form.subscription_package" type="text" readonly class="w-full rounded-lg border-slate-300 bg-slate-50 text-slate-500 focus:ring-0 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Subscription Start</label>
                                <input v-model="form.subscription_start" type="date" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Subscription Expiry</label>
                                <input v-model="form.subscription_expiry" type="date" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Maximum Users</label>
                                <input v-model="form.max_users" type="number" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Maximum Branches</label>
                                <input v-model="form.max_branches" type="number" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Storage Limit (GB)</label>
                                <input v-model="form.storage_limit" type="number" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Preferences Tab -->
            <div v-show="activeTab === 'preferences'" class="grid grid-cols-1 gap-6">
                <div class="max-w-4xl space-y-6">
                    <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                        <h3 class="text-sm font-bold text-slate-800 mb-5">Preferences</h3>
                        <div class="grid grid-cols-1 md:grid-cols-3 gap-x-6 gap-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Language</label>
                                <select v-model="form.language" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10 bg-white">
                                    <option>English</option>
                                    <option>Bangla</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Timezone</label>
                                <select v-model="form.timezone" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10 bg-white">
                                    <option>Asia/Dhaka</option>
                                </select>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Currency</label>
                                <select v-model="form.currency" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10 bg-white">
                                    <option>BDT</option>
                                    <option>USD</option>
                                </select>
                            </div>
                            
                            <div class="md:col-span-3 flex gap-6 mt-2">
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.email_notifications" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                    <span class="ml-2 text-sm text-slate-700">Email Notifications</span>
                                </label>
                                <label class="flex items-center">
                                    <input type="checkbox" v-model="form.sms_notifications" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                    <span class="ml-2 text-sm text-slate-700">SMS Notifications</span>
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <!-- Sticky Footer for Save -->
        <div v-show="['general', 'business', 'regulatory', 'subscription', 'prefs'].includes(activeTab)" class="fixed bottom-0 right-0 left-0 md:left-64 bg-white border-t border-slate-200 p-4 flex justify-end px-4 sm:px-6 lg:px-8 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
            <button @click="saveProfile" :disabled="form.processing" class="inline-flex justify-center items-center rounded-lg bg-emerald-600 px-6 py-2.5 text-sm font-semibold text-white shadow-sm hover:bg-emerald-500 transition-colors disabled:opacity-50">
                <svg v-if="form.processing" class="animate-spin -ml-1 mr-2 h-4 w-4 text-white" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"></circle><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4zm2 5.291A7.962 7.962 0 014 12H0c0 3.042 1.135 5.824 3 7.938l3-2.647z"></path></svg>
                <svg v-else class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7H5a2 2 0 00-2 2v9a2 2 0 002 2h14a2 2 0 002-2V9a2 2 0 00-2-2h-3m-1 4l-3 3m0 0l-3-3m3 3V4"></path></svg>
                Save Changes
            </button>
        </div>

        <!-- Bank Account Modal -->
        <div v-if="showBankModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-slate-500 bg-opacity-75 transition-opacity" @click="showBankModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="relative z-10 inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="saveBankAccount">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4">{{ editingBank ? 'Edit Bank Account' : 'Add Bank Account' }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Bank Name</label>
                                    <select v-model="bankForm.bank_name" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-white">
                                        <option value="" disabled>Select Bank</option>
                                        <option v-for="bank in banks" :key="bank.id" :value="bank.name">{{ bank.name }}</option>
                                    </select>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Account Name</label>
                                    <input v-model="bankForm.account_name" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Account Number</label>
                                    <input v-model="bankForm.account_number" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Routing Number</label>
                                    <input v-model="bankForm.routing_number" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono">
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Account Type</label>
                                        <select v-model="bankForm.account_type" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                            <option>Current</option>
                                            <option>Savings</option>
                                            <option>Corporate</option>
                                        </select>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-slate-700">Branch Name</label>
                                        <select v-model="bankForm.branch_name" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm bg-white">
                                            <option value="">Select Branch</option>
                                            <option v-for="branch in availableBranches" :key="branch.id" :value="branch.name">{{ branch.name }}</option>
                                        </select>
                                    </div>
                                </div>
                                <div>
                                    <label class="flex items-center">
                                        <input type="checkbox" v-model="bankForm.is_active" class="rounded border-slate-300 text-emerald-600 focus:ring-emerald-500 h-4 w-4">
                                        <span class="ml-2 text-sm text-slate-700">Active Account</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="bg-slate-50 px-4 py-3 sm:px-6 flex justify-end gap-3 border-t border-slate-200">
                            <button type="button" @click="showBankModal = false" class="inline-flex justify-center rounded-xl border border-slate-300 shadow-sm px-4 py-2 bg-white text-sm font-medium text-slate-700 hover:bg-slate-50 focus:outline-none">Cancel</button>
                            <button type="submit" :disabled="bankForm.processing" class="inline-flex justify-center rounded-xl border border-transparent shadow-sm px-4 py-2 bg-emerald-600 text-sm font-medium text-white hover:bg-emerald-700 focus:outline-none">Save Account</button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </Layout>
</template>

<style>
button, a, [role="button"] {
    cursor: pointer !important;
}
</style>

<style scoped>
.no-scrollbar::-webkit-scrollbar {
    display: none;
}
.no-scrollbar {
    -ms-overflow-style: none;
    scrollbar-width: none;
}
</style>
