<script setup>
import { computed } from 'vue';
import { Head, Link } from '@inertiajs/vue3';
import ResellerLayout from '@/Layouts/ResellerLayout.vue';

const props = defineProps({
    stats: Object,
    chartData: Array,
    referralCodes: Array,
    recentActivity: Array,
    reseller: Object,
});

const maxReferrals = computed(() => {
    if (!props.chartData || props.chartData.length === 0) return 30;
    const max = Math.max(...props.chartData.map(d => d.referrals || 0));
    return max < 10 ? 10 : Math.ceil(max / 5) * 5;
});

const generatePoints = (key) => {
    if (!props.chartData || props.chartData.length === 0) return '';
    const step = 100 / (props.chartData.length - 1 || 1);
    return props.chartData.map((d, i) => {
        const x = i * step;
        const val = d[key] || 0;
        const y = 100 - (val / maxReferrals.value * 100);
        return `${x},${y}`;
    }).join(' ');
};

const emeraldPolyline = computed(() => generatePoints('referrals'));
const purplePolyline = computed(() => generatePoints('active_companies'));
const emeraldPolygon = computed(() => {
    const points = generatePoints('referrals');
    if (!points) return '';
    return `${points} 100,100 0,100`;
});
const purplePolygon = computed(() => {
    const points = generatePoints('active_companies');
    if (!points) return '';
    return `${points} 100,100 0,100`;
});
</script>

<template>
    <Head title="Dashboard - Partner Portal" />
    <ResellerLayout>
        
        <!-- Date Picker Row -->
        <div class="flex justify-end mb-6">
            <button class="flex items-center gap-2 px-4 py-2 bg-white border border-slate-200 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-50 transition-colors shadow-sm">
                <svg class="w-4 h-4 text-slate-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z" /></svg>
                May 17 – May 23, 2024
                <svg class="w-4 h-4 text-slate-400 ml-1" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7" /></svg>
            </button>
        </div>

        <!-- 4 Stat Cards -->
        <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-4 gap-6 mb-6">
            <!-- Total Referrals -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-emerald-50 flex items-center justify-center text-emerald-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                                Total Referrals
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-800 ml-[3.25rem]">{{ stats.total_companies || 0 }}</div>
                <div class="text-[11px] font-bold text-emerald-500 mt-1 ml-[3.25rem]"> <span class="text-slate-400 font-medium">All Time</span></div>
                
                <!-- Sparkline placeholder -->
                <div class="absolute bottom-0 right-0 w-24 h-12 opacity-80">
                    <svg viewBox="0 0 100 40" class="w-full h-full text-emerald-400" preserveAspectRatio="none"><path d="M0 40 C 20 30, 40 40, 60 20 S 80 20, 100 10 L 100 40 Z" fill="currentColor" fill-opacity="0.1"/><path d="M0 40 C 20 30, 40 40, 60 20 S 80 20, 100 10" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>

            <!-- Active Companies -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-blue-50 flex items-center justify-center text-blue-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                                Active Companies
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-800 ml-[3.25rem]">{{ stats.active_codes || 0 }}</div>
                <div class="text-[11px] font-bold text-emerald-500 mt-1 ml-[3.25rem]"> <span class="text-slate-400 font-medium">Active Codes</span></div>
                
                <!-- Sparkline placeholder -->
                <div class="absolute bottom-0 right-0 w-24 h-12 opacity-80">
                    <svg viewBox="0 0 100 40" class="w-full h-full text-blue-400" preserveAspectRatio="none"><path d="M0 30 C 20 20, 40 40, 60 10 S 80 30, 100 5 L 100 40 Z" fill="currentColor" fill-opacity="0.1"/><path d="M0 30 C 20 20, 40 40, 60 10 S 80 30, 100 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>

            <!-- Commission Earned -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-purple-50 flex items-center justify-center text-purple-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                                Commission Earned
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-800 ml-[3.25rem]">৳{{ Number(stats.lifetime_earnings || 0).toLocaleString() }}</div>
                <div class="text-[11px] font-bold text-emerald-500 mt-1 ml-[3.25rem]"> <span class="text-slate-400 font-medium">Lifetime</span></div>
                
                <!-- Sparkline placeholder -->
                <div class="absolute bottom-0 right-0 w-24 h-12 opacity-80">
                    <svg viewBox="0 0 100 40" class="w-full h-full text-purple-400" preserveAspectRatio="none"><path d="M0 40 C 30 20, 50 35, 70 15 S 90 20, 100 5 L 100 40 Z" fill="currentColor" fill-opacity="0.1"/><path d="M0 40 C 30 20, 50 35, 70 15 S 90 20, 100 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>

            <!-- Pending Payout -->
            <div class="bg-white p-5 rounded-2xl shadow-sm border border-slate-100 flex flex-col relative overflow-hidden group">
                <div class="flex items-start justify-between mb-2">
                    <div class="flex items-center gap-3">
                        <div class="w-10 h-10 rounded-full bg-amber-50 flex items-center justify-center text-amber-500">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                        </div>
                        <div>
                            <div class="flex items-center gap-1.5 text-xs font-bold text-slate-500">
                                Pending Payout
                                <svg class="w-3.5 h-3.5 text-slate-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="text-3xl font-black text-slate-800 ml-[3.25rem]">৳{{ Number(stats.pending_withdrawal || 0).toLocaleString() }}</div>
                <div class="text-[11px] font-medium text-slate-500 mt-1 ml-[3.25rem]">Wallet Balance ৳{{ Number(stats.wallet_balance || 0).toLocaleString() }}</div>
                
                <!-- Sparkline placeholder -->
                <div class="absolute bottom-0 right-0 w-24 h-12 opacity-80">
                    <svg viewBox="0 0 100 40" class="w-full h-full text-amber-400" preserveAspectRatio="none"><path d="M0 25 C 25 35, 45 15, 65 25 S 85 10, 100 5 L 100 40 Z" fill="currentColor" fill-opacity="0.1"/><path d="M0 25 C 25 35, 45 15, 65 25 S 85 10, 100 5" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"/></svg>
                </div>
            </div>
        </div>

        <!-- Middle Charts Row -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 mb-6">
            <!-- Referrals Overview Chart -->
            <div class="lg:col-span-6 xl:col-span-6 bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-[15px] text-slate-800">Referrals Overview</h3>
                    <select class="text-xs font-bold text-slate-600 bg-slate-50 border border-slate-200 rounded-lg px-3 py-1.5 focus:outline-none focus:ring-2 focus:ring-emerald-500/20">
                        <option>This Week</option>
                        <option>This Month</option>
                        <option>This Year</option>
                    </select>
                </div>
                <div class="flex items-center gap-4 mb-4">
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-emerald-500"></span> Referrals
                    </div>
                    <div class="flex items-center gap-1.5 text-xs font-medium text-slate-500">
                        <span class="w-2 h-2 rounded-full bg-purple-500"></span> Active Companies
                    </div>
                </div>
                <!-- Line Chart Placeholder SVG -->
                <div class="flex-1 w-full h-48 relative mt-auto">
                    <!-- Grid lines -->
                    <div class="absolute inset-0 flex flex-col justify-between pt-2 pb-6">
                        <div class="border-b border-slate-100 w-full h-0"></div>
                        <div class="border-b border-slate-100 w-full h-0"></div>
                        <div class="border-b border-slate-100 w-full h-0"></div>
                        <div class="border-b border-slate-100 w-full h-0"></div>
                    </div>
                    <!-- Y Axis Labels -->
                    <div class="absolute left-0 inset-y-0 flex flex-col justify-between pt-1 pb-5 text-[10px] text-slate-400 font-medium">
                        <span>{{ maxReferrals }}</span>
                        <span>{{ Math.round(maxReferrals * 0.66) }}</span>
                        <span>{{ Math.round(maxReferrals * 0.33) }}</span>
                        <span>0</span>
                    </div>
                    <!-- Chart SVG -->
                    <div class="absolute inset-0 pl-6 pb-6">
                        <svg viewBox="0 0 100 100" class="w-full h-full" preserveAspectRatio="none">
                            <!-- Purple Area (Active Companies) -->
                            <polygon :points="purplePolygon" fill="rgba(168, 85, 247, 0.15)"/>
                            <polyline :points="purplePolyline" fill="none" stroke="#a855f7" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                            <template v-for="(d, i) in chartData" :key="'p'+i">
                                <circle :cx="i * (100 / (chartData.length - 1 || 1))" :cy="100 - ((d.active_companies || 0) / maxReferrals * 100)" r="2" fill="#a855f7" class="drop-shadow-sm"/>
                            </template>
                            
                            <!-- Emerald Area (Total Referrals) -->
                            <polygon :points="emeraldPolygon" fill="rgba(16, 185, 129, 0.15)"/>
                            <polyline :points="emeraldPolyline" fill="none" stroke="#10b981" stroke-width="2" stroke-linejoin="round" stroke-linecap="round"/>
                            <template v-for="(d, i) in chartData" :key="'e'+i">
                                <circle :cx="i * (100 / (chartData.length - 1 || 1))" :cy="100 - ((d.referrals || 0) / maxReferrals * 100)" r="2" fill="#10b981" class="drop-shadow-sm"/>
                            </template>
                        </svg>
                    </div>
                    <!-- X Axis Labels -->
                    <div class="absolute bottom-0 left-6 right-0 flex justify-between text-[10px] text-slate-400 font-medium">
                        <span v-for="d in chartData" :key="d.month">{{ d.short_date || d.month }}</span>
                    </div>
                </div>
            </div>

            <!-- Earnings Breakdown Donut -->
            <div class="lg:col-span-3 xl:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col items-center">
                <div class="w-full flex justify-between items-center mb-6">
                    <h3 class="font-bold text-[15px] text-slate-800">Earnings Breakdown</h3>
                </div>
                
                <div class="relative w-40 h-40 mb-6 flex-shrink-0">
                    <svg viewBox="0 0 36 36" class="w-full h-full transform -rotate-90">
                        <!-- Pending -->
                        <path :stroke-dasharray="`${(stats.pending_withdrawal / (stats.lifetime_earnings || 1)) * 100}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#f59e0b" stroke-width="4" />
                        <!-- Paid -->
                        <path :stroke-dasharray="`${(stats.total_paid / (stats.lifetime_earnings || 1)) * 100}, 100`" d="M18 2.0845 a 15.9155 15.9155 0 0 1 0 31.831 a 15.9155 15.9155 0 0 1 0 -31.831" fill="none" stroke="#10b981" stroke-width="4" />
                    </svg>
                    <div class="absolute inset-0 flex flex-col items-center justify-center">
                        <span class="text-xl font-black text-slate-800">৳{{ Number(stats.lifetime_earnings || 0).toLocaleString() }}</span>
                        <span class="text-[10px] font-bold text-slate-400">Total</span>
                    </div>
                </div>

                <div class="w-full space-y-3">
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-emerald-500"></span> Paid
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-800">৳{{ Number(stats.total_paid || 0).toLocaleString() }} <span class="text-slate-400 font-medium">({{ ((stats.total_paid / (stats.lifetime_earnings || 1)) * 100).toFixed(1) }}%)</span></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-amber-500"></span> Pending
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-800">৳{{ Number(stats.pending_withdrawal || 0).toLocaleString() }} <span class="text-slate-400 font-medium">({{ ((stats.pending_withdrawal / (stats.lifetime_earnings || 1)) * 100).toFixed(1) }}%)</span></div>
                        </div>
                    </div>
                    <div class="flex items-center justify-between">
                        <div class="flex items-center gap-2 text-xs font-bold text-slate-700">
                            <span class="w-2.5 h-2.5 rounded-full bg-blue-500"></span> In Wallet
                        </div>
                        <div class="text-right">
                            <div class="text-xs font-bold text-slate-800">৳{{ Number(stats.wallet_balance || 0).toLocaleString() }} <span class="text-slate-400 font-medium">({{ ((stats.wallet_balance / (stats.lifetime_earnings || 1)) * 100).toFixed(1) }}%)</span></div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Quick Links -->
            <div class="lg:col-span-3 xl:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="mb-4">
                    <h3 class="font-bold text-[15px] text-slate-800">Quick Links</h3>
                </div>
                <div class="space-y-3 flex-1 flex flex-col justify-center">
                    <Link href="#" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 012-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Marketing Resources</div>
                                <div class="text-[10px] text-slate-500 font-medium">Banners, Videos & More</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>

                    <Link href="#" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8.684 13.342C8.886 12.938 9 12.482 9 12c0-.482-.114-.938-.316-1.342m0 2.684a3 3 0 110-2.684m0 2.684l6.632 3.316m-6.632-6l6.632-3.316m0 0a3 3 0 105.367-2.684 3 3 0 00-5.367 2.684zm0 9.316a3 3 0 105.368 2.684 3 3 0 00-5.368-2.684z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Share Your Link</div>
                                <div class="text-[10px] text-slate-500 font-medium">Invite & Earn More</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>

                    <Link href="#" class="flex items-center justify-between p-3 rounded-xl border border-slate-100 hover:border-emerald-200 hover:bg-emerald-50/50 transition-colors group">
                        <div class="flex items-center gap-3">
                            <div class="w-8 h-8 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center group-hover:bg-emerald-100">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/></svg>
                            </div>
                            <div>
                                <div class="text-xs font-bold text-slate-800">Payout Settings</div>
                                <div class="text-[10px] text-slate-500 font-medium">Manage payment details</div>
                            </div>
                        </div>
                        <svg class="w-4 h-4 text-slate-300 group-hover:text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"/></svg>
                    </Link>
                </div>
            </div>
        </div>

        <!-- Bottom Row: Table & Activity -->
        <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 pb-8">
            <!-- Your Referral Codes Table -->
            <div class="lg:col-span-8 xl:col-span-9 bg-white rounded-2xl shadow-sm border border-slate-100 flex flex-col overflow-hidden">
                <div class="p-5 flex justify-between items-center border-b border-slate-100">
                    <div>
                        <h3 class="font-bold text-[15px] text-slate-800">Your Referral Codes</h3>
                        <p class="text-xs text-slate-500 font-medium mt-0.5">Create and manage referral codes for different campaigns.</p>
                    </div>
                    <Link :href="route('reseller.codes.index')" class="px-4 py-2 bg-[#00b67a] hover:bg-[#009b68] text-white text-xs font-bold rounded-lg flex items-center gap-1.5 transition-colors shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/></svg>
                        Generate New Code
                    </Link>
                </div>
                
                <div class="overflow-x-auto flex-1">
                    <table class="w-full text-left border-collapse">
                        <thead>
                            <tr class="bg-slate-50/50">
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100">Code</th>
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100">Campaign Label</th>
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-center">Referrals</th>
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-center">Active Companies</th>
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-right">Commission Earned</th>
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-center">Status</th>
                                <th class="py-3 px-5 text-[10px] font-extrabold text-slate-500 uppercase tracking-wider border-b border-slate-100 text-right">Actions</th>
                            </tr>
                        </thead>
                        <tbody class="text-sm">
                            <tr v-for="code in referralCodes" :key="code.id" class="hover:bg-slate-50/50 transition-colors border-b border-slate-100/50">
                                <td class="py-3 px-5">
                                    <div class="inline-flex items-center gap-1.5 px-2 py-1 bg-slate-100 rounded-md text-xs font-mono font-bold text-slate-700">
                                        {{ code.code }}
                                        <button class="text-slate-400 hover:text-[#00b67a]"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 16H6a2 2 0 01-2-2V6a2 2 0 012-2h8a2 2 0 012 2v2m-6 12h8a2 2 0 002-2v-8a2 2 0 00-2-2h-8a2 2 0 00-2 2v8a2 2 0 002 2z"/></svg></button>
                                    </div>
                                </td>
                                <td class="py-3 px-5">
                                    <div class="text-xs font-bold text-slate-800">{{ code.campaign_label }}</div>
                                    <div class="text-[10px] text-slate-400 font-medium mt-0.5">Created: {{ code.created_at }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <div class="text-sm font-black text-slate-800">{{ code.referrals_count }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <div class="text-sm font-black text-slate-800">{{ code.active_companies }}</div>
                                </td>
                                <td class="py-3 px-5 text-right">
                                    <div class="text-sm font-black text-slate-800">৳{{ Number(code.commission_earned || 0).toLocaleString() }}</div>
                                </td>
                                <td class="py-3 px-5 text-center">
                                    <span :class="code.status === 'active' ? 'bg-emerald-100 text-emerald-700' : 'bg-red-100 text-red-700'" class="inline-flex items-center px-2 py-0.5 rounded text-[10px] font-bold uppercase">{{ code.status }}</span>
                                </td>
                                <td class="py-3 px-5 text-right">
                                    <div class="flex items-center justify-end gap-2">
                                        <Link :href="route('reseller.codes.index')" class="w-6 h-6 rounded flex items-center justify-center text-slate-400 hover:text-blue-600 hover:bg-blue-50 transition-colors"><svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"/><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"/></svg></Link>
                                    </div>
                                </td>
                            </tr>
                            <tr v-if="!referralCodes || referralCodes.length === 0">
                                <td colspan="7" class="py-6 text-center text-sm text-slate-500">No referral codes found.</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
                <div class="p-4 border-t border-slate-100 flex justify-center">
                    <Link :href="route('reseller.codes.index')" class="text-xs font-bold text-slate-500 hover:text-emerald-600 transition-colors flex items-center gap-1">
                        View all referral codes <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"/></svg>
                    </Link>
                </div>
            </div>

            <!-- Recent Activity -->
            <div class="lg:col-span-4 xl:col-span-3 bg-white rounded-2xl shadow-sm border border-slate-100 p-5 flex flex-col">
                <div class="flex justify-between items-center mb-6">
                    <h3 class="font-bold text-[15px] text-slate-800">Recent Activity</h3>
                    <button class="text-[11px] font-bold text-[#00b67a] hover:text-[#009b68] transition-colors">View All</button>
                </div>
                
                <div class="space-y-5 flex-1 relative">
                    <!-- Line connecting timeline -->
                    <div class="absolute left-[1.15rem] top-3 bottom-5 w-px bg-slate-100 -z-10"></div>
                    
                    <div v-if="!recentActivity || recentActivity.length === 0" class="text-center text-sm text-slate-500 py-6">
                        No recent activity.
                    </div>

                    <div v-for="activity in recentActivity" :key="activity.timestamp" class="flex gap-4">
                        <div v-if="activity.type === 'referral'" class="w-9 h-9 rounded-full bg-emerald-50 text-emerald-500 flex items-center justify-center flex-shrink-0 border-[3px] border-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"/></svg>
                        </div>
                        <div v-else-if="activity.type === 'commission'" class="w-9 h-9 rounded-full bg-purple-50 text-purple-500 flex items-center justify-center flex-shrink-0 border-[3px] border-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"/></svg>
                        </div>
                        <div v-else class="w-9 h-9 rounded-full bg-amber-50 text-amber-500 flex items-center justify-center flex-shrink-0 border-[3px] border-white shadow-sm">
                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/></svg>
                        </div>

                        <div class="pt-1">
                            <div class="text-[11px] font-bold text-slate-600 mb-0.5">{{ activity.title }}</div>
                            <div class="text-[13px] font-black text-slate-800 leading-tight">{{ activity.subtitle }}</div>
                            <div class="text-[9px] font-semibold text-slate-400 mt-1">{{ activity.date }}</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </ResellerLayout>
</template>
