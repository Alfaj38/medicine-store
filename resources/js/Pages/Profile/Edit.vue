<script setup>
import { Head, useForm, usePage } from '@inertiajs/vue3';
import AppLayout from '@/Layouts/AppLayout.vue';
import { ref } from 'vue';

const props = defineProps({
    user: Object,
});

const profileForm = useForm({
    name: props.user.name,
    email: props.user.email,
    avatar: null,
});

const passwordForm = useForm({
    current_password: '',
    password: '',
    password_confirmation: '',
});

const avatarPreview = ref(props.user.avatar ? '/storage/' + props.user.avatar : null);
const avatarInput = ref(null);

const handleAvatarChange = (e) => {
    const file = e.target.files[0];
    if (file) {
        profileForm.avatar = file;
        avatarPreview.value = URL.createObjectURL(file);
    }
};

const updateProfile = () => {
    profileForm.post(route('profile.update'), {
        preserveScroll: true,
        onSuccess: () => {
            profileForm.clearErrors();
        },
    });
};

const updatePassword = () => {
    passwordForm.put(route('profile.password'), {
        preserveScroll: true,
        onSuccess: () => passwordForm.reset(),
    });
};
</script>

<template>
    <Head title="My Profile" />

    <AppLayout>

        <!-- Page Heading -->
        <header class="bg-white shadow border-b border-slate-200">
            <div class="w-full mx-auto py-6 px-4 sm:px-6 lg:px-8">
                <h2 class="font-semibold text-xl text-slate-800 leading-tight">
                    My Profile
                </h2>
            </div>
        </header>

        <main class="py-12">
            <div class="w-full mx-auto sm:px-6 lg:px-8 space-y-6">
                
                <!-- Success Message -->
                <div v-if="$page.props.flash?.success" class="bg-emerald-50 border-l-4 border-emerald-400 p-4 rounded-md shadow-sm">
                    <div class="flex">
                        <div class="flex-shrink-0">
                            <svg class="h-5 w-5 text-emerald-400" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
                        </div>
                        <div class="ml-3">
                            <p class="text-sm font-medium text-emerald-800">
                                {{ $page.props.flash.success }}
                            </p>
                        </div>
                    </div>
                </div>

                <!-- Profile Information -->
                <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-200">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-bold text-slate-900">Profile Information</h2>
                            <p class="mt-1 text-sm text-slate-500">Update your account's profile information and avatar.</p>
                        </header>

                        <form @submit.prevent="updateProfile" class="mt-6 space-y-6">
                            <!-- Avatar -->
                            <div class="flex items-center space-x-6">
                                <div class="shrink-0">
                                    <img v-if="avatarPreview" :src="avatarPreview" class="h-16 w-16 object-cover rounded-full border border-slate-200" alt="Avatar Preview" />
                                    <div v-else class="h-16 w-16 rounded-full bg-slate-100 flex items-center justify-center text-slate-600 font-bold text-2xl border border-slate-200">
                                        {{ props.user.name.charAt(0) }}
                                    </div>
                                </div>
                                <label class="block">
                                    <span class="sr-only">Choose profile photo</span>
                                    <input type="file" ref="avatarInput" @change="handleAvatarChange" accept="image/*" class="block w-full text-sm text-slate-500
                                      file:mr-4 file:py-2.5 file:px-4
                                      file:rounded-full file:border-0
                                      file:text-sm file:font-semibold
                                      file:bg-emerald-50 file:text-emerald-700
                                      hover:file:bg-emerald-100 cursor-pointer transition-colors
                                    "/>
                                </label>
                                <div v-if="profileForm.errors.avatar" class="text-sm text-rose-600">{{ profileForm.errors.avatar }}</div>
                            </div>

                            <!-- Name -->
                            <div>
                                <label class="block font-medium text-sm text-slate-700">Name</label>
                                <input v-model="profileForm.name" type="text" class="border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm mt-1 block w-full" required />
                                <div v-if="profileForm.errors.name" class="text-sm text-rose-600 mt-1">{{ profileForm.errors.name }}</div>
                            </div>

                            <!-- Email -->
                            <div>
                                <label class="block font-medium text-sm text-slate-700">Email</label>
                                <input v-model="profileForm.email" type="email" class="border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm mt-1 block w-full" required />
                                <div v-if="profileForm.errors.email" class="text-sm text-rose-600 mt-1">{{ profileForm.errors.email }}</div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button :disabled="profileForm.processing" class="inline-flex items-center px-6 py-2.5 bg-slate-900 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-800 active:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                    Save Profile
                                </button>
                                <span v-if="profileForm.recentlySuccessful" class="text-sm font-medium text-emerald-600">Saved.</span>
                            </div>
                        </form>
                    </section>
                </div>

                <!-- Update Password -->
                <div class="p-4 sm:p-8 bg-white shadow-sm sm:rounded-2xl border border-slate-200">
                    <section class="max-w-xl">
                        <header>
                            <h2 class="text-lg font-bold text-slate-900">Update Password</h2>
                            <p class="mt-1 text-sm text-slate-500">Ensure your account is using a long, random password to stay secure.</p>
                        </header>

                        <form @submit.prevent="updatePassword" class="mt-6 space-y-6">
                            <div>
                                <label class="block font-medium text-sm text-slate-700">Current Password</label>
                                <input v-model="passwordForm.current_password" type="password" class="border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm mt-1 block w-full" required />
                                <div v-if="passwordForm.errors.current_password" class="text-sm text-rose-600 mt-1">{{ passwordForm.errors.current_password }}</div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-slate-700">New Password</label>
                                <input v-model="passwordForm.password" type="password" class="border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm mt-1 block w-full" required />
                                <div v-if="passwordForm.errors.password" class="text-sm text-rose-600 mt-1">{{ passwordForm.errors.password }}</div>
                            </div>

                            <div>
                                <label class="block font-medium text-sm text-slate-700">Confirm Password</label>
                                <input v-model="passwordForm.password_confirmation" type="password" class="border-slate-300 focus:border-emerald-500 focus:ring-emerald-500 rounded-xl shadow-sm mt-1 block w-full" required />
                                <div v-if="passwordForm.errors.password_confirmation" class="text-sm text-rose-600 mt-1">{{ passwordForm.errors.password_confirmation }}</div>
                            </div>

                            <div class="flex items-center gap-4">
                                <button :disabled="passwordForm.processing" class="inline-flex items-center px-6 py-2.5 bg-slate-900 border border-transparent rounded-xl font-semibold text-xs text-white uppercase tracking-widest hover:bg-slate-800 active:bg-slate-900 focus:outline-none focus:ring-2 focus:ring-emerald-500 focus:ring-offset-2 transition ease-in-out duration-150 disabled:opacity-50">
                                    Update Password
                                </button>
                                <span v-if="passwordForm.recentlySuccessful" class="text-sm font-medium text-emerald-600">Saved.</span>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </main>
    </AppLayout>
</template>
