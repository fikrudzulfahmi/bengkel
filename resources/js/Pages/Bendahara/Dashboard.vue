<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    metrics: Object,
    pendingDeposits: Array,
    recentActivities: Array,
});

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
};

const approveForm = useForm({});
const rejectForm = useForm({ rejection_note: '' });

const approveDeposit = (deposit) => {
    if (confirm(`Setujui setoran dari ${deposit.mechanic?.name} sebesar ${formatRp(deposit.amount)}?`)) {
        approveForm.post(route('bendahara.deposits.approve', deposit.id));
    }
};
</script>

<template>
    <Head title="Dashboard Bendahara" />
    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                    Dashboard Bendahara
                    <span class="text-gray-400 text-sm font-normal ml-2">(Bulan ini)</span>
                </h2>
                <a :href="route('bendahara.deposits.index')" class="relative inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 17h5l-1.405-1.405A2.032 2.032 0 0118 14.158V11a6.002 6.002 0 00-4-5.659V5a2 2 0 10-4 0v.341C7.67 6.165 6 8.388 6 11v3.159c0 .538-.214 1.055-.595 1.436L4 17h5m6 0v1a3 3 0 11-6 0v-1m6 0H9" /></svg>
                    Verifikasi Setoran
                    <span v-if="metrics.pending_deposits > 0" class="absolute -top-2 -right-2 bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center animate-pulse">
                        {{ metrics.pending_deposits }}
                    </span>
                </a>
            </div>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Metric Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card>
                        <div class="flex items-center gap-4">
                            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" /></svg>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium mb-1">Pemasukan Bulan Ini</div>
                                <div class="text-2xl font-bold text-gray-800">{{ formatRp(metrics.pemasukan_bulan) }}</div>
                            </div>
                        </div>
                    </Card>
                    <Card>
                        <div class="flex items-center gap-4">
                            <div class="p-4 rounded-2xl bg-red-50 text-red-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium mb-1">Pengeluaran Bulan Ini</div>
                                <div class="text-2xl font-bold text-gray-800">{{ formatRp(metrics.pengeluaran_bulan) }}</div>
                            </div>
                        </div>
                    </Card>
                    <Card class="bg-gradient-to-br from-amber-500 to-orange-500 text-white border-transparent">
                        <div class="flex items-center gap-4">
                            <div class="p-4 rounded-2xl bg-white/20 text-white">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <div class="text-white/80 text-sm font-medium mb-1">Saldo Kas Bendahara</div>
                                <div class="text-3xl font-extrabold">{{ formatRp(metrics.saldo_bulan) }}</div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Pending Deposits -->
                <Card v-if="pendingDeposits.length > 0">
                    <div class="flex items-center justify-between mb-4">
                        <h3 class="text-base font-bold text-gray-800 flex items-center gap-2">
                            <span class="w-2.5 h-2.5 bg-amber-500 rounded-full animate-pulse inline-block"></span>
                            Setoran Menunggu Verifikasi
                            <span class="bg-amber-100 text-amber-700 text-xs font-bold px-2 py-0.5 rounded-full">{{ pendingDeposits.length }}</span>
                        </h3>
                        <a :href="route('bendahara.deposits.index')" class="text-sm text-amber-600 font-medium hover:underline">Lihat semua →</a>
                    </div>
                    <div class="space-y-3">
                        <div v-for="deposit in pendingDeposits.slice(0, 5)" :key="deposit.id"
                            class="flex items-center justify-between p-3 rounded-xl bg-amber-50 border border-amber-100">
                            <div class="flex items-center gap-3">
                                <div class="w-9 h-9 rounded-full bg-amber-200 text-amber-800 flex items-center justify-center font-bold text-sm">
                                    {{ deposit.mechanic?.name?.charAt(0)?.toUpperCase() }}
                                </div>
                                <div>
                                    <div class="font-semibold text-gray-800 text-sm">{{ deposit.mechanic?.name }}</div>
                                    <div class="text-xs text-gray-500">{{ deposit.description }}</div>
                                </div>
                            </div>
                            <div class="text-right flex items-center gap-3">
                                <div>
                                    <div class="font-bold text-gray-900 text-sm">{{ formatRp(deposit.amount) }}</div>
                                    <div class="text-xs text-gray-400">{{ formatDate(deposit.date) }}</div>
                                </div>
                                <button @click="approveDeposit(deposit)"
                                    :disabled="approveForm.processing"
                                    class="bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors disabled:opacity-50">
                                    Setujui
                                </button>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Empty Pending Deposits -->
                <Card v-else>
                    <div class="flex flex-col items-center py-6 text-center">
                        <div class="w-14 h-14 rounded-full bg-emerald-50 flex items-center justify-center mb-3">
                            <svg class="w-7 h-7 text-emerald-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                        </div>
                        <p class="text-sm text-gray-500 font-medium">Tidak ada setoran yang perlu diverifikasi</p>
                    </div>
                </Card>

                <!-- Recent Activities -->
                <Card>
                    <h3 class="text-base font-bold text-gray-800 mb-4">Aktivitas Terbaru</h3>
                    <div class="space-y-3">
                        <div v-for="activity in recentActivities" :key="activity.id"
                            class="flex items-start gap-3 p-3 rounded-xl hover:bg-gray-50 transition-colors">
                            <div class="w-8 h-8 rounded-full bg-gray-100 text-gray-600 flex items-center justify-center font-bold text-xs flex-shrink-0 mt-0.5">
                                {{ activity.user?.name?.charAt(0)?.toUpperCase() }}
                            </div>
                            <div class="flex-1 min-w-0">
                                <div class="text-sm font-semibold text-gray-800">{{ activity.action }}</div>
                                <div class="text-xs text-gray-500 truncate">{{ activity.description }}</div>
                                <div class="text-xs text-gray-400 mt-0.5">{{ activity.user?.name }}</div>
                            </div>
                        </div>
                    </div>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
