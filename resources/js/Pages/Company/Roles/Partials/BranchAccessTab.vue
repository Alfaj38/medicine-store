<script setup>
import { computed } from 'vue';

const props = defineProps({
    branches: Array,
    formBranches: Array,
});

const emit = defineEmits(['toggleBranch']);

const isSelected = (branchId) => {
    return props.formBranches.includes(branchId);
};

</script>

<template>
    <div class="bg-white border border-slate-200 rounded-2xl p-6">
        <div class="flex justify-between items-center mb-6">
            <div>
                <h3 class="font-bold text-slate-800 text-lg">Branch Access</h3>
                <p class="text-sm text-slate-500">Define which branches this role can access.</p>
            </div>
            <div class="text-sm text-emerald-600 bg-emerald-50 px-3 py-1 rounded-full font-medium">
                {{ formBranches.length }} / {{ branches.length }} Selected
            </div>
        </div>

        <div class="grid grid-cols-1 md:grid-cols-3 lg:grid-cols-4 gap-4">
            <label 
                v-for="branch in branches" :key="branch.id"
                class="relative flex items-start p-4 cursor-pointer rounded-xl border-2 transition-all"
                :class="isSelected(branch.id) ? 'border-emerald-500 bg-emerald-50/30' : 'border-slate-200 hover:border-slate-300 bg-white'"
            >
                <div class="min-w-0 flex-1">
                    <div class="flex items-center justify-between">
                        <span class="text-sm font-bold text-slate-900 truncate pr-2">{{ branch.name }}</span>
                        <input 
                            type="checkbox" 
                            class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 w-5 h-5 flex-shrink-0 cursor-pointer"
                            :checked="isSelected(branch.id)"
                            @change="$emit('toggleBranch', branch.id)"
                        >
                    </div>
                    <p class="text-xs text-slate-500 mt-1 truncate">{{ branch.area ? branch.area.name : 'Branch location' }}</p>
                </div>
            </label>
            <div v-if="branches.length === 0" class="col-span-full py-8 text-center text-slate-500 text-sm">
                No branches found in your company.
            </div>
        </div>
    </div>
</template>
