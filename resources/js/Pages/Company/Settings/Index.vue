<script setup>
import { ref, computed } from 'vue';
import { useForm, router, usePage } from '@inertiajs/vue3';
import Layout from './Layout.vue';

const props = defineProps({
    company: Object
});

const page = usePage();

const activeTab = ref('general');

// Main Profile Form
const form = useForm({
    name: props.company.name || '',
    email: props.company.email || '',
    phone: props.company.phone || '',
    alternative_phone: props.company.alternative_phone || '',
    website: props.company.website || '',
    address: props.company.address || '',
    business_type: props.company.business_type || 'Pharmaceutical',
    year_established: props.company.year_established || '',
    registration_number: props.company.registration_number || '',
    tax_id: props.company.tax_id || '',
    trade_license_number: props.company.trade_license_number || '',
    vat_registration_number: props.company.vat_registration_number || '',
    about_company: props.company.about_company || '',
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
    account_number: '',
    account_type: 'Current',
    branch_name: '',
    is_active: true
});

const openBankModal = (bank = null) => {
    if (bank) {
        editingBank.value = bank;
        bankForm.bank_name = bank.bank_name;
        bankForm.account_number = bank.account_number;
        bankForm.account_type = bank.account_type;
        bankForm.branch_name = bank.branch_name;
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
                <div v-if="form.recentlySuccessful" class="flex items-center text-emerald-600 text-sm font-medium">
                    <svg class="w-4 h-4 mr-1.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                    All changes saved
                </div>
                <button class="bg-white border border-slate-200 text-slate-700 px-4 py-2 rounded-lg text-sm font-medium shadow-sm hover:bg-slate-50 flex items-center">
                    <svg class="w-4 h-4 mr-2 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 6H6a2 2 0 00-2 2v10a2 2 0 002 2h10a2 2 0 002-2v-4M14 4h6m0 0v6m0-6L10 14"></path></svg>
                    View Profile
                </button>
            </div>
        </div>

        <!-- Tabs -->
        <div class="border-b border-slate-200 mb-6">
            <nav class="-mb-px flex space-x-8">
                <a href="#general" @click.prevent="activeTab = 'general'" :class="activeTab === 'general' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">General Information</a>
                <a href="#business" @click.prevent="activeTab = 'business'" :class="activeTab === 'business' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Business Details</a>
                <a href="#bank" @click.prevent="activeTab = 'bank'" :class="activeTab === 'bank' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Bank Details</a>
                <a href="#docs" @click.prevent="activeTab = 'docs'" :class="activeTab === 'docs' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Documents</a>
                <a href="#prefs" @click.prevent="activeTab = 'prefs'" :class="activeTab === 'prefs' ? 'border-emerald-500 text-emerald-600' : 'border-transparent text-slate-500 hover:text-slate-700 hover:border-slate-300'" class="whitespace-nowrap py-3 border-b-2 font-medium text-sm">Preferences</a>
            </nav>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-3 gap-6 pb-20">
            <!-- Left Column: General & Business -->
            <div class="lg:col-span-2 space-y-6">
                
                <!-- Basic Information Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-5">Basic Information</h3>
                    
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                        <!-- Left Side Inputs -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Company Name <span class="text-red-500">*</span></label>
                                <input v-model="form.name" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Email Address <span class="text-red-500">*</span></label>
                                <input v-model="form.email" type="email" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Website</label>
                                <input v-model="form.website" type="text" placeholder="https://www.example.com" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                        </div>

                        <!-- Right Side Inputs & Logo -->
                        <div class="space-y-4">
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Company Logo</label>
                                <div class="flex items-center gap-4">
                                    <div class="w-16 h-16 rounded-xl border border-slate-200 bg-slate-50 flex items-center justify-center overflow-hidden flex-shrink-0">
                                        <img v-if="logoPreview" :src="logoPreview" class="w-full h-full object-contain">
                                        <div v-else class="text-emerald-600 font-bold text-xl">{{ form.name ? form.name.charAt(0) : 'C' }}</div>
                                    </div>
                                    <div class="flex-1">
                                        <label class="cursor-pointer border border-dashed border-slate-300 rounded-lg flex flex-col items-center justify-center py-3 hover:bg-slate-50 transition-colors">
                                            <svg class="w-5 h-5 text-slate-400 mb-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg>
                                            <span class="text-xs font-medium text-slate-600">Upload Logo</span>
                                            <span class="text-[10px] text-slate-400">PNG, JPG (Max 2MB)</span>
                                            <input type="file" class="hidden" @change="handleLogoUpload" accept="image/*">
                                        </label>
                                    </div>
                                </div>
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Phone Number <span class="text-red-500">*</span></label>
                                <input v-model="form.phone" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                            <div>
                                <label class="block text-xs font-semibold text-slate-700 mb-1">Alternative Phone</label>
                                <input v-model="form.alternative_phone" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                        </div>

                        <!-- Full Width Address -->
                        <div class="md:col-span-2">
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Physical Address <span class="text-red-500">*</span></label>
                            <textarea v-model="form.address" rows="2" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm"></textarea>
                        </div>
                    </div>
                </div>

                <!-- Business Details Card -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-6">
                    <h3 class="text-sm font-bold text-slate-800 mb-5">Business Details</h3>
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-4">
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Business Type</label>
                            <select v-model="form.business_type" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10 bg-white">
                                <option value="Pharmaceutical">Pharmaceutical</option>
                                <option value="Clinic">Clinic</option>
                                <option value="Hospital">Hospital</option>
                                <option value="Retail Pharmacy">Retail Pharmacy</option>
                                <option value="Wholesale">Wholesale</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Year Established</label>
                            <div class="relative">
                                <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                    <svg class="h-4 w-4 text-slate-400" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path></svg>
                                </div>
                                <input v-model="form.year_established" type="number" class="w-full pl-9 rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                            </div>
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Registration Number</label>
                            <input v-model="form.registration_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Tax Identification (TIN)</label>
                            <input v-model="form.tax_id" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">Trade License Number</label>
                            <input v-model="form.trade_license_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
                        </div>
                        <div>
                            <label class="block text-xs font-semibold text-slate-700 mb-1">VAT Registration Number</label>
                            <input v-model="form.vat_registration_number" type="text" class="w-full rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm h-10">
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

            <!-- Right Column: Status, Actions, Bank, Docs -->
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
                        <button class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-slate-400 mr-3 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path></svg>
                                <div class="text-left">
                                    <div class="text-sm font-semibold text-slate-700">View Company Profile</div>
                                    <div class="text-[10px] text-slate-500">See how your profile appears to others</div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                        <button class="w-full flex items-center justify-between p-3 rounded-lg hover:bg-slate-50 transition-colors group">
                            <div class="flex items-center">
                                <svg class="w-5 h-5 text-slate-400 mr-3 group-hover:text-emerald-600 transition-colors" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16v1a3 3 0 003 3h10a3 3 0 003-3v-1m-4-8l-4-4m0 0L8 8m4-4v12"></path></svg>
                                <div class="text-left">
                                    <div class="text-sm font-semibold text-slate-700">Export Profile</div>
                                    <div class="text-[10px] text-slate-500">Download company profile as PDF</div>
                                </div>
                            </div>
                            <svg class="w-4 h-4 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path></svg>
                        </button>
                    </div>
                </div>

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
                                <div class="text-xs font-bold text-slate-800">{{ bank.bank_name }}</div>
                                <div class="text-[10px] text-slate-500 font-mono">{{ bank.account_number }}</div>
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

                <!-- Upload Documents -->
                <div class="bg-white rounded-2xl shadow-sm border border-slate-200 p-5">
                    <h3 class="text-sm font-bold text-slate-800 mb-2">Upload Documents</h3>
                    <p class="text-[10px] text-slate-500 mb-4">Keep your company documents up to date.</p>
                    
                    <div class="mb-4 flex gap-2 items-center">
                        <select v-model="docForm.document_type" class="text-xs rounded-lg border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 h-8 py-0 pl-2 pr-6">
                            <option>Trade License</option>
                            <option>TIN Certificate</option>
                            <option>VAT Certificate</option>
                            <option>Other</option>
                        </select>
                        <div class="flex-1 relative">
                            <input type="file" class="absolute inset-0 w-full h-full opacity-0 cursor-pointer" @change="handleDocUpload" accept=".pdf,.jpg,.png">
                            <div class="border border-dashed border-slate-300 rounded-lg h-8 flex items-center justify-center bg-slate-50 hover:bg-slate-100 transition-colors">
                                <span class="text-[10px] font-medium text-slate-600"><svg class="w-3 h-3 inline mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"></path></svg> Upload</span>
                            </div>
                        </div>
                    </div>

                    <div class="space-y-2">
                        <div class="text-[10px] font-bold text-slate-400 uppercase tracking-wider mb-2">Uploaded Documents</div>
                        <div v-for="doc in company.documents" :key="doc.id" class="flex justify-between items-center bg-red-50/30 border border-red-100 rounded-lg p-2">
                            <div class="flex items-center">
                                <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 21h10a2 2 0 002-2V9.414a1 1 0 00-.293-.707l-5.414-5.414A1 1 0 0012.586 3H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path></svg>
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

        <!-- Sticky Footer for Save -->
        <div class="fixed bottom-0 right-0 left-0 md:left-64 bg-white border-t border-slate-200 p-4 flex justify-end px-4 sm:px-6 lg:px-8 z-10 shadow-[0_-4px_6px_-1px_rgba(0,0,0,0.05)]">
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
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="saveBankAccount">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-slate-900 mb-4">{{ editingBank ? 'Edit Bank Account' : 'Add Bank Account' }}</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Bank Name</label>
                                    <input v-model="bankForm.bank_name" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-slate-700">Account Number</label>
                                    <input v-model="bankForm.account_number" type="text" required class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm font-mono">
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
                                        <input v-model="bankForm.branch_name" type="text" class="mt-1 block w-full rounded-xl border-slate-300 shadow-sm focus:border-emerald-500 focus:ring-emerald-500 sm:text-sm">
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
