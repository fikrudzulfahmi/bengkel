<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

const props = defineProps({
    transactions: Array,
    cashBooks: Array,
    summary: Object,
    filters: Object
});

const month = ref(props.filters.month);
const year = ref(props.filters.year);

const filterData = () => {
    router.get(route('admin.reports.index'), { month: month.value, year: year.value }, { preserveState: true });
};

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Laporan Keuangan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Keuangan & Unit Masuk</h2>
        </template>

        <div class="p-6 md:p-8">
            <div class="max-w-7xl mx-auto  space-y-6">
                
                <!-- Filter and Print Actions -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex flex-col md:flex-row md:items-center justify-between gap-4">
                    <div class="flex items-center space-x-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select v-model="month" @change="filterData" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                <option value="01">Januari</option>
                                <option value="02">Februari</option>
                                <option value="03">Maret</option>
                                <option value="04">April</option>
                                <option value="05">Mei</option>
                                <option value="06">Juni</option>
                                <option value="07">Juli</option>
                                <option value="08">Agustus</option>
                                <option value="09">September</option>
                                <option value="10">Oktober</option>
                                <option value="11">November</option>
                                <option value="12">Desember</option>
                            </select>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Tahun</label>
                            <input v-model="year" type="number" @change="filterData" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm w-24">
                        </div>
                    </div>
                    
                    <div class="flex flex-wrap gap-2">
                        <a :href="route('admin.reports.print-service', { month: month, year: year })" target="_blank" class="inline-flex items-center px-4 py-2 bg-blue-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cetak Service Bulanan
                        </a>
                        <a :href="route('admin.reports.print-finance', { type: 'monthly', month: month, year: year })" target="_blank" class="inline-flex items-center px-4 py-2 bg-green-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cetak Keuangan Bulanan
                        </a>
                        <a :href="route('admin.reports.print-finance', { type: 'yearly', year: year })" target="_blank" class="inline-flex items-center px-4 py-2 bg-indigo-600 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-indigo-700 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                            Cetak Keuangan Tahunan
                        </a>
                    </div>
                </div>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-4 gap-6">
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-sm font-medium mb-1">Pendapatan Servis</div>
                        <div class="text-2xl font-bold text-gray-800">{{ formatRp(summary.income_servis) }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-sm font-medium mb-1">Pemasukan Lain</div>
                        <div class="text-2xl font-bold text-gray-800">{{ formatRp(summary.income_lain) }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                        <div class="text-gray-500 text-sm font-medium mb-1">Pengeluaran Operasional</div>
                        <div class="text-2xl font-bold text-red-600">{{ formatRp(summary.pengeluaran) }}</div>
                    </div>
                    <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 bg-gradient-to-br from-primary to-secondary text-white">
                        <div class="text-red-100 text-sm font-medium mb-1">Laba Bersih</div>
                        <div class="text-3xl font-extrabold">{{ formatRp(summary.laba) }}</div>
                    </div>
                </div>

                <!-- Unit Masuk / Transaksi -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100 mt-8">
                    <div class="p-6 border-b border-gray-100">
                        <h3 class="text-lg font-bold text-gray-800">Riwayat Unit Masuk (Servis)</h3>
                    </div>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50 text-gray-600 text-sm border-b">
                                    <th class="p-4 font-semibold">Tanggal</th>
                                    <th class="p-4 font-semibold">Pelanggan</th>
                                    <th class="p-4 font-semibold">Motor</th>
                                    <th class="p-4 font-semibold">Mekanik</th>
                                    <th class="p-4 font-semibold text-right">Total Biaya</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="trx in transactions" :key="trx.id" class="border-b hover:bg-gray-50 transition">
                                    <td class="p-4 text-gray-500">{{ new Date(trx.created_at).toLocaleDateString('id-ID') }}</td>
                                    <td class="p-4 text-gray-800 font-medium">{{ trx.customer?.name || 'Umum' }}</td>
                                    <td class="p-4 text-gray-500">{{ trx.motorcycle?.type || '-' }} ({{ trx.motorcycle?.plate_number || '-' }})</td>
                                    <td class="p-4 text-gray-500">{{ trx.user?.name }}</td>
                                    <td class="p-4 text-right font-semibold text-gray-800">{{ formatRp(trx.total_price) }}</td>
                                </tr>
                                <tr v-if="transactions.length === 0">
                                    <td colspan="5" class="p-8 text-center text-gray-500">Belum ada transaksi servis bulan ini.</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
