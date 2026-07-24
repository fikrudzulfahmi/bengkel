<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import { computed } from 'vue';

const props = defineProps({
    metrics: Object,
    lowStockSpareparts: Array,
    recentActivities: Array,
});

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const formatDate = (dateString) => {
    const date = new Date(dateString);
    return date.toLocaleDateString('id-ID', {
        year: 'numeric',
        month: 'short',
        day: 'numeric',
        hour: '2-digit',
        minute: '2-digit'
    });
};
</script>

<template>
    <Head title="Mekanik Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Dashboard Mekanik</h2>
        </template>

        <div class="p-6 md:p-8 space-y-8">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Welcome Section -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Selamat datang kembali!</h3>
                        <p class="text-gray-500 mt-1">Berikut adalah ringkasan performa Anda hari ini.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Card class="bg-gradient-to-br from-primary to-teal-500 border-0 shadow-lg shadow-primary/20">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-white/80 text-sm font-medium uppercase tracking-wider mb-1">Pendapatan Hari Ini</p>
                                    <h4 class="text-3xl font-bold text-white">{{ formatRp(metrics.today_revenue) }}</h4>
                                </div>
                                <div class="bg-white/20 p-3 rounded-2xl backdrop-blur-sm">
                                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                </div>
                            </div>
                        </div>
                    </Card>
                    
                    <Card>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-1">Transaksi Selesai (Hari Ini)</p>
                                <h4 class="text-3xl font-bold text-gray-900">{{ metrics.today_transactions }} <span class="text-base font-normal text-gray-400">Servis</span></h4>
                            </div>
                            <div class="bg-primary/10 p-3 rounded-2xl">
                                <svg class="w-8 h-8 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2m-6 9l2 2 4-4"></path></svg>
                            </div>
                        </div>
                    </Card>
                </div>

                <div class="grid grid-cols-1 lg:grid-cols-3 gap-8">
                    <!-- Low Stock Alerts -->
                    <div class="lg:col-span-1 space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-red-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-3L13.732 4c-.77-1.333-2.694-1.333-3.464 0L3.34 16c-.77 1.333.192 3 1.732 3z"></path></svg>
                            Peringatan Stok
                        </h4>
                        
                        <Card noPadding v-if="lowStockSpareparts.length > 0">
                            <div class="divide-y divide-gray-100">
                                <div v-for="sp in lowStockSpareparts" :key="sp.id" class="p-4 flex justify-between items-center hover:bg-gray-50 transition-colors">
                                    <div>
                                        <p class="font-medium text-gray-900">{{ sp.name }}</p>
                                        <p class="text-xs text-gray-500">{{ formatRp(sp.price) }}</p>
                                    </div>
                                    <Badge :type="sp.stock === 0 ? 'danger' : 'warning'">Sisa {{ sp.stock }}</Badge>
                                </div>
                            </div>
                            <div class="p-4 bg-gray-50 border-t border-gray-100 text-center">
                                <Link :href="route('mechanic.spareparts.index')" class="text-sm font-medium text-primary hover:text-teal-700 transition-colors">Kelola Sparepart &rarr;</Link>
                            </div>
                        </Card>
                        
                        <div v-else class="bg-green-50 border border-green-100 rounded-2xl p-6 text-center">
                            <div class="mx-auto w-12 h-12 bg-green-100 rounded-full flex items-center justify-center mb-3">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path></svg>
                            </div>
                            <h5 class="font-bold text-green-900 mb-1">Stok Aman</h5>
                            <p class="text-sm text-green-700">Semua sparepart memiliki stok yang cukup.</p>
                        </div>
                    </div>

                    <!-- Recent Activities -->
                    <div class="lg:col-span-2 space-y-4">
                        <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                            <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            Aktivitas Terkini Anda
                        </h4>
                        
                        <Card noPadding v-if="recentActivities.length > 0">
                            <div class="divide-y divide-gray-100">
                                <div v-for="act in recentActivities" :key="act.id" class="p-5 flex gap-4 hover:bg-gray-50/50 transition-colors">
                                    <div class="flex-shrink-0 mt-1">
                                        <div class="w-2.5 h-2.5 rounded-full bg-primary ring-4 ring-primary/20"></div>
                                    </div>
                                    <div>
                                        <p class="text-sm font-semibold text-gray-900">{{ act.action }}</p>
                                        <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ act.description }}</p>
                                        <p class="text-xs text-gray-400 mt-2">{{ formatDate(act.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </Card>
                        
                        <Card v-else>
                            <p class="text-center text-gray-500 py-6">Belum ada aktivitas hari ini.</p>
                        </Card>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
