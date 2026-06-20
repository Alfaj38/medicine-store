<template>
    <AdminLayout>
        <template #header>
            <div class="flex justify-between items-center w-full">
                <div>
                    <h1 class="text-xl font-bold text-slate-800">Platform Control Center</h1>
                    <p class="text-sm text-slate-500 font-medium">Welcome back, <span class="text-slate-800 font-bold">Super Admin</span> 👋</p>
                </div>
            </div>
        </template>

        <div class="flex flex-col lg:flex-row gap-6">
            <!-- Main Content Area -->
            <div class="flex-1 space-y-6">
                
                <!-- Quick Actions -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <Link :href="route('admin.companies.index')" class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 hover:border-emerald-300 transition-colors cursor-pointer shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-emerald-50 text-emerald-600 flex items-center justify-center shrink-0">➕</div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">New Company</p>
                            <p class="text-[10px] text-slate-500 font-medium">Add new tenant</p>
                        </div>
                    </Link>
                    <Link :href="route('admin.packages.index')" class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 hover:border-purple-300 transition-colors cursor-pointer shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-purple-50 text-purple-600 flex items-center justify-center shrink-0">📦</div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Create Package</p>
                            <p class="text-[10px] text-slate-500 font-medium">Add subscription plan</p>
                        </div>
                    </Link>
                    <Link :href="route('admin.coupons.index')" class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 hover:border-orange-300 transition-colors cursor-pointer shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-orange-50 text-orange-600 flex items-center justify-center shrink-0">🎫</div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Add Coupon</p>
                            <p class="text-[10px] text-slate-500 font-medium">Create discount coupon</p>
                        </div>
                    </Link>
                    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 hover:border-red-300 transition-colors cursor-pointer shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-red-50 text-red-600 flex items-center justify-center shrink-0">📢</div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Broadcast Notice</p>
                            <p class="text-[10px] text-slate-500 font-medium">Send announcement</p>
                        </div>
                    </div>
                    <div class="bg-white rounded-xl border border-slate-200 p-4 flex items-center gap-3 hover:border-blue-300 transition-colors cursor-pointer shadow-sm">
                        <div class="w-10 h-10 rounded-lg bg-blue-50 text-blue-600 flex items-center justify-center shrink-0">📥</div>
                        <div>
                            <p class="text-xs font-bold text-slate-800">Export Report</p>
                            <p class="text-[10px] text-slate-500 font-medium">Download reports</p>
                        </div>
                    </div>
                </div>

                <!-- Main Stats -->
                <div class="grid grid-cols-2 md:grid-cols-6 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center text-xs">🏢</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Total Companies</span>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800">{{ fmt(stats.total_companies) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 12 this month</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center text-xs">🟢</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Active Companies</span>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800">{{ fmt(stats.active_companies) }}</p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1">85.21% of total</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-amber-50 text-amber-500 flex items-center justify-center text-xs">🧪</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Trial Companies</span>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800">{{ fmt(stats.trial_companies) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 3 this week</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-red-50 text-red-500 flex items-center justify-center text-xs">🚫</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Expired Companies</span>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800">{{ fmt(stats.expired_companies) }}</p>
                        <p class="text-[10px] text-red-500 font-bold mt-1 flex items-center gap-1">↓ 2 this month</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center text-xs">👥</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Total Users</span>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800">{{ fmt(stats.total_users) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 18 this week</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-purple-50 text-purple-500 flex items-center justify-center text-xs">🏪</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Total Branches</span>
                        </div>
                        <p class="text-2xl font-extrabold text-slate-800">{{ fmt(stats.total_branches) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 22 this week</p>
                    </div>
                </div>

                <!-- Financial Stats -->
                <div class="grid grid-cols-2 md:grid-cols-5 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-emerald-50 text-emerald-500 flex items-center justify-center text-xs">💰</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">MRR</span>
                        </div>
                        <p class="text-xl font-extrabold text-slate-800">৳{{ fmt(stats.mrr) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 18.2% vs last month</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-blue-50 text-blue-500 flex items-center justify-center text-xs">📈</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">ARR</span>
                        </div>
                        <p class="text-xl font-extrabold text-slate-800">৳{{ fmt(stats.arr) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 21.7% vs last year</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-purple-50 text-purple-500 flex items-center justify-center text-xs">🏦</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Revenue (This Month)</span>
                        </div>
                        <p class="text-xl font-extrabold text-slate-800">৳{{ fmt(stats.revenue_this_month) }}</p>
                        <p class="text-[10px] text-emerald-500 font-bold mt-1 flex items-center gap-1">↑ 14.5% vs last month</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-orange-50 text-orange-500 flex items-center justify-center text-xs">⏳</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Pending Invoices</span>
                        </div>
                        <p class="text-xl font-extrabold text-slate-800">৳{{ fmt(stats.pending_invoices) }}</p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1 flex items-center gap-1">12 Invoices</p>
                    </div>
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                        <div class="flex items-center gap-2 mb-2">
                            <div class="w-6 h-6 rounded bg-red-50 text-red-500 flex items-center justify-center text-xs">🚨</div>
                            <span class="text-[10px] font-bold text-slate-500 uppercase tracking-wide">Overdue Invoices</span>
                        </div>
                        <p class="text-xl font-extrabold text-slate-800">৳{{ fmt(stats.overdue_invoices) }}</p>
                        <p class="text-[10px] text-slate-400 font-bold mt-1 flex items-center gap-1">8 Invoices</p>
                    </div>
                </div>

                <!-- Charts Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="lg:col-span-1.5 xl:col-span-1 bg-white rounded-xl shadow-sm border border-slate-100 p-5 min-h-[300px] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Revenue Overview</h3>
                            <select class="text-xs bg-slate-50 border-slate-200 rounded text-slate-600 font-medium py-1 px-2 focus:ring-0">
                                <option>This Month</option>
                            </select>
                        </div>
                        <div class="flex-1 w-full relative -ml-2 -mb-2">
                            <apexchart type="area" height="100%" width="100%" :options="revenueOptions" :series="revenueChart.series"></apexchart>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 min-h-[300px] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Trial Conversion Funnel</h3>
                            <select class="text-xs bg-slate-50 border-slate-200 rounded text-slate-600 font-medium py-1 px-2 focus:ring-0">
                                <option>This Month</option>
                            </select>
                        </div>
                        <div class="flex-1 w-full relative flex items-center justify-center pb-6">
                            <!-- Custom CSS Funnel representation -->
                            <div class="w-full max-w-[200px] flex flex-col items-center gap-1">
                                <div class="w-[200px] h-12 bg-emerald-300 flex items-center justify-between px-3 text-emerald-900 text-xs font-bold relative" style="clip-path: polygon(0 0, 100% 0, 90% 100%, 10% 100%)">
                                    <span>Visitors</span><span>1,200</span>
                                </div>
                                <div class="w-[160px] h-12 bg-blue-300 flex items-center justify-between px-3 text-blue-900 text-xs font-bold relative" style="clip-path: polygon(0 0, 100% 0, 90% 100%, 10% 100%)">
                                    <span>Demo Requests</span><span>150</span>
                                    <span class="absolute -right-16 text-slate-400 text-[10px]">12.5%</span>
                                </div>
                                <div class="w-[128px] h-12 bg-purple-300 flex items-center justify-between px-3 text-purple-900 text-xs font-bold relative" style="clip-path: polygon(0 0, 100% 0, 85% 100%, 15% 100%)">
                                    <span>Trials Started</span><span>80</span>
                                    <span class="absolute -right-[72px] text-slate-400 text-[10px]">6.7%</span>
                                </div>
                                <div class="w-[90px] h-12 bg-amber-300 flex items-center justify-between px-3 text-amber-900 text-xs font-bold relative" style="clip-path: polygon(0 0, 100% 0, 80% 100%, 20% 100%)">
                                    <span>Converted</span><span>25</span>
                                    <span class="absolute -right-[90px] text-slate-400 text-[10px]">2.1%</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 min-h-[300px] flex flex-col">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Subscription Overview</h3>
                        </div>
                        <div class="flex-1 w-full relative flex items-center justify-center pb-4">
                            <apexchart type="donut" height="200" width="100%" :options="donutOptions" :series="subscriptionChart.series"></apexchart>
                        </div>
                    </div>
                </div>

                <!-- Lists & Bottom Row -->
                <div class="grid grid-cols-1 lg:grid-cols-3 gap-4">
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 min-h-[300px]">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Recent Signups</h3>
                            <Link href="#" class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest hover:underline">View all</Link>
                        </div>
                        <div class="space-y-4 mt-6">
                            <div v-for="user in recentSignups" :key="user.name" class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold" :class="user.color">
                                        {{ user.name.substring(0,2).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ user.name }}</p>
                                        <p class="text-[10px] text-slate-500 font-medium">{{ user.plan }} • {{ user.branches }} Branches</p>
                                    </div>
                                </div>
                                <span class="text-[10px] text-slate-400 font-bold">{{ user.time }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 min-h-[300px]">
                        <div class="flex justify-between items-center mb-4">
                            <h3 class="text-sm font-bold text-slate-800">Recent Demo Requests</h3>
                            <Link :href="route('admin.demo-requests.index')" class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest hover:underline">View all</Link>
                        </div>
                        <div class="space-y-4 mt-6">
                            <div v-for="req in demoRequests" :key="req.name" class="flex justify-between items-center">
                                <div class="flex items-center gap-3">
                                    <div class="w-8 h-8 rounded-full flex items-center justify-center text-white text-xs font-bold bg-purple-500">
                                        {{ req.name.substring(0,2).toUpperCase() }}
                                    </div>
                                    <div>
                                        <p class="text-xs font-bold text-slate-800">{{ req.name }}</p>
                                        <p class="text-[10px] text-slate-500 font-medium">{{ req.email }}</p>
                                    </div>
                                </div>
                                <div class="text-right">
                                    <p class="text-[10px] text-slate-500 font-bold flex items-center gap-1 justify-end"><span class="text-[8px]">📅</span> {{ req.date }}</p>
                                    <span class="text-[10px] font-bold mt-0.5 inline-block" :class="req.status === 'pending' ? 'text-blue-500' : 'text-slate-400'">{{ req.status.toUpperCase() }}</span>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5 min-h-[300px] flex flex-col justify-between">
                        <div>
                            <div class="flex justify-between items-center mb-4">
                                <h3 class="text-sm font-bold text-slate-800">Support Tickets Overview</h3>
                                <Link href="#" class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest hover:underline">View all</Link>
                            </div>
                            <div class="flex items-center justify-center h-[180px]">
                                <apexchart type="donut" height="200" width="100%" :options="ticketOptions" :series="supportTickets.series"></apexchart>
                            </div>
                        </div>
                        <div class="bg-red-50 border border-red-100 p-3 rounded-lg flex justify-between items-center">
                            <span class="text-xs font-bold text-red-600">⚠️ 1 ticket requires immediate attention</span>
                            <button class="bg-white text-red-600 text-[10px] font-bold px-2 py-1 rounded shadow-sm border border-red-200">View Ticket</button>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Right Sidebar -->
            <div class="w-full lg:w-80 flex-shrink-0 space-y-6">
                
                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <div class="flex justify-between items-center mb-6">
                        <h3 class="text-sm font-bold text-slate-800">Recent Notifications</h3>
                        <Link href="#" class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest hover:underline">View all</Link>
                    </div>
                    
                    <div class="space-y-6">
                        <div v-for="(n, i) in notifications" :key="i" class="flex gap-3 items-start relative">
                            <!-- Timeline line -->
                            <div v-if="i !== notifications.length - 1" class="absolute left-[15px] top-8 bottom-[-24px] w-0.5 bg-slate-100"></div>
                            
                            <div class="w-8 h-8 rounded-full flex items-center justify-center text-xs shrink-0 z-10" :class="n.bg">
                                {{ n.icon }}
                            </div>
                            <div>
                                <div class="flex justify-between items-center w-full">
                                    <p class="text-xs font-bold text-slate-800">{{ n.title }}</p>
                                    <span class="text-[9px] text-slate-400 font-bold ml-2 shrink-0">{{ n.time }}</span>
                                </div>
                                <p class="text-[10px] text-slate-500 font-medium leading-relaxed mt-0.5 pr-2">{{ n.desc }}</p>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="bg-white rounded-xl shadow-sm border border-slate-100 p-5">
                    <div class="flex justify-between items-center mb-5">
                        <h3 class="text-sm font-bold text-slate-800">System Health</h3>
                        <Link href="#" class="text-[10px] text-emerald-600 font-bold uppercase tracking-widest hover:underline">View all</Link>
                    </div>

                    <div class="space-y-4">
                        <div v-for="h in systemHealth" :key="h.name" class="flex justify-between items-center border-b border-slate-50 pb-3 last:border-0 last:pb-0">
                            <div class="flex items-center gap-2">
                                <span class="text-sm">{{ h.icon }}</span>
                                <span class="text-xs font-bold text-slate-700">{{ h.name }}</span>
                            </div>
                            <span class="text-[10px] font-bold" :class="h.color">{{ h.status }}</span>
                        </div>
                    </div>
                    
                    <div class="mt-4 pt-4 border-t border-slate-100 flex justify-between items-center">
                        <span class="text-[9px] text-slate-400 font-bold">Last checked: 1 min ago</span>
                        <button class="text-slate-400 hover:text-emerald-500 transition-colors">↻</button>
                    </div>
                </div>

            </div>
        </div>

    </AdminLayout>
</template>

<script setup>
import { computed } from 'vue';
import { Link } from '@inertiajs/vue3';
import AdminLayout from './Layout.vue';
import VueApexCharts from 'vue3-apexcharts';

const apexchart = VueApexCharts;

const props = defineProps({
    stats: Object,
    revenueChart: Object,
    trialFunnel: Object,
    subscriptionChart: Object,
    supportTickets: Object,
    recentSignups: Array,
    demoRequests: Array,
    notifications: Array,
    systemHealth: Array,
});

const fmt = (val) => Number(val).toLocaleString();

// Chart configurations
const revenueOptions = computed(() => ({
    chart: { type: 'area', toolbar: { show: false }, zoom: { enabled: false }, fontFamily: 'Inter, sans-serif' },
    colors: ['#10b981', '#cbd5e1'],
    dataLabels: { enabled: false },
    stroke: { curve: 'smooth', width: [2, 2], dashArray: [0, 5] },
    xaxis: { categories: props.revenueChart.categories, axisBorder: { show: false }, axisTicks: { show: false }, labels: { style: { colors: '#94a3b8', fontSize: '9px', fontWeight: 600 } } },
    yaxis: { labels: { formatter: (val) => (val >= 1000 ? val/1000 + 'K' : val), style: { colors: '#94a3b8', fontSize: '9px', fontWeight: 600 } } },
    grid: { borderColor: '#f1f5f9', strokeDashArray: 4 },
    fill: { type: 'gradient', gradient: { shadeIntensity: 1, opacityFrom: 0.2, opacityTo: 0, stops: [0, 100] } },
    legend: { position: 'top', horizontalAlign: 'left', fontSize: '10px', markers: { radius: 12 }, itemMargin: { horizontal: 10, vertical: 0 } },
    tooltip: { y: { formatter: (val) => '৳' + val + 'K' } }
}));

const donutOptions = computed(() => ({
    chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
    labels: props.subscriptionChart.labels,
    colors: ['#0ea5e9', '#8b5cf6', '#10b981'],
    dataLabels: { enabled: false },
    plotOptions: { donut: { size: '75%', labels: { show: true, name: { show: false }, value: { fontSize: '24px', fontWeight: 800, color: '#0f172a' }, total: { show: true, label: 'Total', formatter: function (w) { return w?.globals?.seriesTotals ? w.globals.seriesTotals.reduce((a, b) => a + b, 0) : 142 } } } } },
    stroke: { width: 0 },
    legend: { position: 'bottom', fontSize: '10px', markers: { radius: 12 }, itemMargin: { horizontal: 5, vertical: 5 } }
}));

const ticketOptions = computed(() => ({
    chart: { type: 'donut', fontFamily: 'Inter, sans-serif' },
    labels: props.supportTickets.labels,
    colors: ['#ef4444', '#f59e0b', '#3b82f6', '#10b981'],
    dataLabels: { enabled: false },
    plotOptions: { donut: { size: '75%', labels: { show: true, name: { show: false }, value: { fontSize: '24px', fontWeight: 800, color: '#0f172a' }, total: { show: true, label: 'Total', formatter: function () { return props.supportTickets.total } } } } },
    stroke: { width: 0 },
    legend: { position: 'right', fontSize: '10px', markers: { radius: 12 }, itemMargin: { horizontal: 0, vertical: 5 } }
}));

</script>
