<script setup>
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import StorefrontLayout from '@/Layouts/StorefrontLayout.vue';

const props = defineProps({
    company: Object,
});

const form = useForm({
    patient_name: '',
    patient_phone: '',
    patient_address: '',
    notes: '',
    prescription: null,
});

const imagePreview = ref(null);

const handleFileChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        form.prescription = file;
        const reader = new FileReader();
        reader.onload = (e) => {
            imagePreview.value = e.target.result;
        };
        reader.readAsDataURL(file);
    }
};

const submit = () => {
    form.post(route('storefront.prescription.upload', props.company.slug), {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            imagePreview.value = null;
        },
    });
};
</script>

<template>
    <Head :title="`Upload Prescription - ${company.name}`" />

    <StorefrontLayout :company="company">
        <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
            <div class="text-center mb-10">
                <h1 class="text-3xl font-extrabold text-slate-900 sm:text-4xl">Upload Your Prescription</h1>
                <p class="mt-4 text-lg text-slate-500">Quickly send your prescription to {{ company.name }} and we'll prepare your medicines for you!</p>
            </div>

            <div class="bg-white shadow-xl sm:rounded-2xl border border-slate-100 overflow-hidden">
                <div class="grid grid-cols-1 md:grid-cols-2">
                    
                    <!-- Left Side: Upload Area -->
                    <div class="p-8 bg-slate-50 border-b md:border-b-0 md:border-r border-slate-200">
                        <h3 class="text-lg font-bold text-slate-800 mb-4">1. Attach Prescription Image</h3>
                        
                        <div 
                            class="mt-2 flex justify-center rounded-xl border-2 border-dashed px-6 py-10 transition-colors"
                            :class="{'border-emerald-500 bg-emerald-50': imagePreview, 'border-slate-300 hover:border-slate-400': !imagePreview}"
                        >
                            <div class="text-center">
                                <div v-if="imagePreview" class="mb-4">
                                    <img :src="imagePreview" alt="Preview" class="mx-auto h-48 object-contain rounded-lg shadow-sm">
                                </div>
                                <svg v-else class="mx-auto h-12 w-12 text-slate-300" viewBox="0 0 24 24" fill="currentColor" aria-hidden="true">
                                    <path fill-rule="evenodd" d="M1.5 6a2.25 2.25 0 012.25-2.25h16.5A2.25 2.25 0 0122.5 6v12a2.25 2.25 0 01-2.25 2.25H3.75A2.25 2.25 0 011.5 18V6zM3 16.06V18c0 .414.336.75.75.75h16.5A.75.75 0 0021 18v-1.94l-2.69-2.689a1.5 1.5 0 00-2.12 0l-.88.879.97.97a.75.75 0 11-1.06 1.06l-5.16-5.159a1.5 1.5 0 00-2.12 0L3 16.061zm10.125-7.81a1.125 1.125 0 112.25 0 1.125 1.125 0 01-2.25 0z" clip-rule="evenodd" />
                                </svg>
                                
                                <div class="mt-4 flex text-sm leading-6 text-slate-600 justify-center">
                                    <label for="file-upload" class="relative cursor-pointer rounded-md bg-white font-semibold text-emerald-600 focus-within:outline-none focus-within:ring-2 focus-within:ring-emerald-600 focus-within:ring-offset-2 hover:text-emerald-500">
                                        <span>{{ imagePreview ? 'Change Image' : 'Upload a file' }}</span>
                                        <input id="file-upload" name="file-upload" type="file" class="sr-only" @change="handleFileChange" accept="image/*">
                                    </label>
                                </div>
                                <p class="text-xs leading-5 text-slate-500 mt-2">PNG, JPG, JPEG up to 5MB</p>
                                <InputError :message="form.errors.prescription" class="mt-2" />
                            </div>
                        </div>
                    </div>

                    <!-- Right Side: Patient Details -->
                    <div class="p-8">
                        <h3 class="text-lg font-bold text-slate-800 mb-6">2. Patient Details</h3>
                        
                        <form @submit.prevent="submit" class="space-y-5">
                            
                            <!-- Success Message -->
                            <div v-if="$page.props.flash.success" class="bg-emerald-50 border border-emerald-200 text-emerald-700 px-4 py-3 rounded-lg flex items-start gap-3">
                                <svg class="w-5 h-5 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                <div>
                                    <h4 class="font-bold text-sm">Success!</h4>
                                    <p class="text-sm mt-1">{{ $page.props.flash.success }}</p>
                                </div>
                            </div>

                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900">Patient Name</label>
                                <input v-model="form.patient_name" type="text" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" placeholder="John Doe">
                                <p v-if="form.errors.patient_name" class="mt-1 text-sm text-red-600">{{ form.errors.patient_name }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900">Phone Number</label>
                                <input v-model="form.patient_phone" type="text" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" placeholder="017xxxxxxxx">
                                <p v-if="form.errors.patient_phone" class="mt-1 text-sm text-red-600">{{ form.errors.patient_phone }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900">Delivery Address</label>
                                <textarea v-model="form.patient_address" rows="2" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" placeholder="House 12, Road 4..."></textarea>
                                <p v-if="form.errors.patient_address" class="mt-1 text-sm text-red-600">{{ form.errors.patient_address }}</p>
                            </div>

                            <div>
                                <label class="block text-sm font-medium leading-6 text-slate-900">Additional Notes (Optional)</label>
                                <textarea v-model="form.notes" rows="2" class="block w-full rounded-lg border-0 py-2.5 text-slate-900 shadow-sm ring-1 ring-inset ring-slate-300 placeholder:text-slate-400 focus:ring-2 focus:ring-inset focus:ring-emerald-600 sm:text-sm sm:leading-6" placeholder="Any specific requirements..."></textarea>
                            </div>

                            <div class="pt-2">
                                <button type="submit" :disabled="form.processing" class="flex w-full justify-center rounded-lg bg-emerald-600 px-3 py-3 text-sm font-semibold leading-6 text-white shadow-sm hover:bg-emerald-500 focus-visible:outline focus-visible:outline-2 focus-visible:outline-offset-2 focus-visible:outline-emerald-600 transition-colors disabled:opacity-50">
                                    {{ form.processing ? 'Uploading...' : 'Submit Prescription' }}
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </StorefrontLayout>
</template>
