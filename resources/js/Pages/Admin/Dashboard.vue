<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import { computed } from 'vue';

const props = defineProps({
    metrics: Object,
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
    <Head title="Admin Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Admin Dashboard</h2>
        </template>

        <div class="p-6 md:p-8 space-y-8">
            <div class="max-w-7xl mx-auto space-y-8">
                
                <!-- Welcome Section -->
                <div class="flex items-center justify-between">
                    <div>
                        <h3 class="text-2xl font-bold text-gray-900">Ringkasan Sistem</h3>
                        <p class="text-gray-500 mt-1">Pantau performa dan aktivitas bengkel secara keseluruhan.</p>
                    </div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card class="bg-gradient-to-br from-teal-600 to-emerald-600 border-0 shadow-lg shadow-teal-600/20">
                        <div class="p-6">
                            <div class="flex justify-between items-start">
                                <div>
                                    <p class="text-white/80 text-sm font-medium uppercase tracking-wider mb-1">Total Pemasukan Bersih</p>
                                    <h4 class="text-3xl font-bold text-white">{{ formatRp(metrics.total_revenue) }}</h4>
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
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-1">Total Transaksi</p>
                                <h4 class="text-3xl font-bold text-gray-900">{{ metrics.total_transactions }}</h4>
                            </div>
                            <div class="bg-blue-50 p-3 rounded-2xl">
                                <svg class="w-8 h-8 text-blue-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path></svg>
                            </div>
                        </div>
                    </Card>

                    <Card>
                        <div class="flex justify-between items-start">
                            <div>
                                <p class="text-gray-500 text-sm font-medium uppercase tracking-wider mb-1">Total Mekanik</p>
                                <h4 class="text-3xl font-bold text-gray-900">{{ metrics.total_mechanics }}</h4>
                            </div>
                            <div class="bg-orange-50 p-3 rounded-2xl">
                                <svg class="w-8 h-8 text-orange-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path></svg>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Recent Activities -->
                <div class="space-y-4">
                    <h4 class="text-lg font-bold text-gray-900 flex items-center gap-2">
                        <svg class="w-5 h-5 text-gray-500" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        Aktivitas Sistem Terkini
                    </h4>
                    
                    <Card noPadding v-if="recentActivities.length > 0">
                        <div class="divide-y divide-gray-100">
                            <div v-for="act in recentActivities" :key="act.id" class="p-5 flex gap-4 hover:bg-gray-50/50 transition-colors">
                                <div class="flex-shrink-0 mt-1">
                                    <div class="w-10 h-10 rounded-full bg-primary/10 flex items-center justify-center font-bold text-primary">
                                        {{ act.user.name.charAt(0) }}
                                    </div>
                                </div>
                                <div class="flex-1">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <p class="text-sm font-semibold text-gray-900">{{ act.user.name }} <span class="font-normal text-gray-500 mx-1">&bull;</span> {{ act.action }}</p>
                                            <p class="text-sm text-gray-600 mt-1 leading-relaxed">{{ act.description }}</p>
                                        </div>
                                        <p class="text-xs text-gray-400 whitespace-nowrap">{{ formatDate(act.created_at) }}</p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </Card>
                    
                    <Card v-else>
                        <p class="text-center text-gray-500 py-6">Belum ada aktivitas di sistem.</p>
                    </Card>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
