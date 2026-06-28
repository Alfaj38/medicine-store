<script setup>
import { computed } from 'vue';

const props = defineProps({
    permissionsByModule: Object,
    activeModule: String,
    formPermissions: Array,
});

const emit = defineEmits(['update:activeModule', 'togglePermission', 'toggleModule']);

const modulesList = computed(() => Object.keys(props.permissionsByModule));

const hasPermission = (permName) => {
    return props.formPermissions.includes(permName);
};

const getPermissionForAction = (moduleName, action) => {
    return props.permissionsByModule[moduleName]?.find(p => p.action === action);
};

const standardActions = ['view', 'add', 'edit', 'delete'];

// Check if all permissions in a module are selected
const isModuleFullySelected = (moduleName) => {
    const perms = props.permissionsByModule[moduleName] || [];
    if (perms.length === 0) return false;
    return perms.every(p => hasPermission(p.name));
};

const toggleModule = (moduleName) => {
    emit('toggleModule', moduleName, !isModuleFullySelected(moduleName));
};

</script>

<template>
    <div class="flex flex-col lg:flex-row gap-6">
        <!-- Left Sidebar: Modules -->
        <div class="w-full lg:w-64 flex-shrink-0">
            <div class="flex justify-between items-center mb-4">
                <h4 class="font-bold text-slate-800 text-sm">Menu Permissions</h4>
                <span class="text-xs text-emerald-600 bg-emerald-50 px-2 py-1 rounded-full font-medium">{{ modulesList.length }} Modules</span>
            </div>
            <p class="text-xs text-slate-500 mb-4">Control access to system modules and features.</p>
            
            <div class="space-y-1 bg-white border border-slate-200 rounded-xl overflow-hidden p-2">
                <button 
                    v-for="moduleName in modulesList" :key="moduleName"
                    @click="$emit('update:activeModule', moduleName)"
                    class="w-full text-left px-3 py-2.5 rounded-lg text-sm font-medium transition-colors flex items-center justify-between group"
                    :class="activeModule === moduleName ? 'bg-emerald-50 text-emerald-700' : 'text-slate-600 hover:bg-slate-50'"
                >
                    <div class="flex items-center gap-3">
                        <svg class="w-4 h-4 text-slate-400 group-hover:text-emerald-500" :class="{'text-emerald-500': activeModule === moduleName}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                        </svg>
                        {{ moduleName }}
                    </div>
                    <svg class="w-4 h-4 text-slate-400" :class="{'rotate-90': activeModule === moduleName}" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
                    </svg>
                </button>
            </div>
        </div>

        <!-- Center: Matrix -->
        <div class="flex-1 min-w-0 bg-white border border-slate-200 rounded-2xl p-6">
            <div class="flex items-center gap-3 mb-6 pb-6 border-b border-slate-100">
                <div class="w-10 h-10 rounded-xl bg-emerald-50 flex items-center justify-center text-emerald-600">
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"></path></svg>
                </div>
                <div>
                    <h3 class="font-bold text-slate-800 text-lg">Module: {{ activeModule }}</h3>
                    <p class="text-sm text-slate-500">Configure permissions for this module</p>
                </div>
            </div>

            <div class="overflow-x-auto">
                <table class="w-full text-left border-collapse">
                    <thead>
                        <tr>
                            <th class="py-3 px-4 text-sm font-medium text-slate-500 w-1/3">Feature</th>
                            <th class="py-3 px-4 text-sm font-medium text-slate-500 text-center w-1/6" v-for="action in standardActions" :key="action">
                                <span class="capitalize">{{ action }}</span>
                            </th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-slate-100">
                        <tr>
                            <td class="py-4 px-4">
                                <p class="text-sm font-bold text-slate-800">{{ activeModule }} Access</p>
                                <p class="text-xs text-slate-500 mt-0.5">Allow access to {{ activeModule }} features.</p>
                            </td>
                            <td class="py-4 px-4 text-center" v-for="action in standardActions" :key="action">
                                <label v-if="getPermissionForAction(activeModule, action)" class="inline-flex items-center cursor-pointer">
                                    <input 
                                        type="checkbox" 
                                        class="rounded text-emerald-600 focus:ring-emerald-500 border-slate-300 w-5 h-5 cursor-pointer"
                                        :checked="hasPermission(getPermissionForAction(activeModule, action).name)"
                                        @change="$emit('togglePermission', getPermissionForAction(activeModule, action).name)"
                                    >
                                </label>
                                <span v-else class="text-slate-300">-</span>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <div class="mt-6 flex justify-end">
                    <button @click="toggleModule(activeModule)" class="text-sm text-emerald-600 font-medium hover:text-emerald-700">
                        {{ isModuleFullySelected(activeModule) ? 'Deselect All in Module' : 'Select All in Module' }}
                    </button>
                </div>
            </div>
        </div>
    </div>
</template>
