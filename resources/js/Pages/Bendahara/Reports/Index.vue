<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    summary: Object,
    filters: Object,
});

const month = ref(props.filters.month);
const year = ref(props.filters.year);

const type = ref('monthly');
const printMonth = ref(props.filters.month);
const printYear = ref(props.filters.year);

const filterData = () => {
    router.get(route('bendahara.reports.index'), {
        month: month.value,
        year: year.value
    }, { preserveState: true });
};

const printFinance = () => {
    const url = route('bendahara.reports.print-finance', {
        type: type.value,
        month: printMonth.value,
        year: printYear.value
    });
    window.open(url, '_blank');
};

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};

const months = [
    { value: '01', label: 'Januari' },
    { value: '02', label: 'Februari' },
    { value: '03', label: 'Maret' },
    { value: '04', label: 'April' },
    { value: '05', label: 'Mei' },
    { value: '06', label: 'Juni' },
    { value: '07', label: 'Juli' },
    { value: '08', label: 'Agustus' },
    { value: '09', label: 'September' },
    { value: '10', label: 'Oktober' },
    { value: '11', label: 'November' },
    { value: '12', label: 'Desember' }
];
</script>

<template>
    <Head title="Laporan Bendahara" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Laporan Keuangan Bendahara</h2>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Quick Stats for Selected Month -->
                <Card>
                    <div class="flex flex-col md:flex-row md:items-center justify-between gap-6 mb-6">
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Ringkasan Bulan Ini</h3>
                            <p class="text-sm text-gray-500">Pilih bulan dan tahun untuk melihat ringkasan singkat.</p>
                        </div>
                        <div class="flex items-center gap-3">
                            <select v-model="month" @change="filterData" class="rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                            </select>
                            <input v-model="year" type="number" @change="filterData" class="rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm w-24">
                        </div>
                    </div>

                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <div class="p-5 rounded-2xl bg-emerald-50 border border-emerald-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-emerald-100 text-emerald-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" /></svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-emerald-800">Total Pemasukan</div>
                                <div class="text-xl font-bold text-emerald-900 mt-0.5">{{ formatRp(summary.pemasukan) }}</div>
                            </div>
                        </div>

                        <div class="p-5 rounded-2xl bg-red-50 border border-red-100 flex items-center gap-4">
                            <div class="w-12 h-12 rounded-xl bg-red-100 text-red-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-red-800">Total Pengeluaran</div>
                                <div class="text-xl font-bold text-red-900 mt-0.5">{{ formatRp(summary.pengeluaran) }}</div>
                            </div>
                        </div>

                        <div class="p-5 rounded-2xl bg-amber-500 border border-amber-600 flex items-center gap-4 text-white">
                            <div class="w-12 h-12 rounded-xl bg-white/20 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                            </div>
                            <div>
                                <div class="text-sm font-medium text-amber-100">Laba / Saldo</div>
                                <div class="text-xl font-bold text-white mt-0.5">{{ formatRp(summary.saldo) }}</div>
                            </div>
                        </div>
                    </div>
                </Card>

                <!-- Print Options -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <Card class="h-full flex flex-col">
                        <div class="flex items-center gap-4 mb-6">
                            <div class="w-12 h-12 rounded-2xl bg-blue-100 text-blue-600 flex items-center justify-center">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                            </div>
                            <div>
                                <h3 class="text-lg font-bold text-gray-900">Cetak Laporan Keuangan</h3>
                                <p class="text-sm text-gray-500">Unduh PDF atau cetak langsung riwayat keuangan bendahara.</p>
                            </div>
                        </div>

                        <div class="space-y-5 flex-1">
                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-1.5">Jenis Laporan</label>
                                <div class="grid grid-cols-2 gap-3">
                                    <label :class="['flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors', type === 'monthly' ? 'border-primary bg-primary/5 text-primary-dark' : 'border-gray-200 hover:bg-gray-50 text-gray-700']">
                                        <input type="radio" v-model="type" value="monthly" class="text-primary focus:ring-primary border-gray-300">
                                        <span class="text-sm font-medium">Bulanan</span>
                                    </label>
                                    <label :class="['flex items-center gap-3 p-3 rounded-xl border cursor-pointer transition-colors', type === 'yearly' ? 'border-primary bg-primary/5 text-primary-dark' : 'border-gray-200 hover:bg-gray-50 text-gray-700']">
                                        <input type="radio" v-model="type" value="yearly" class="text-primary focus:ring-primary border-gray-300">
                                        <span class="text-sm font-medium">Tahunan</span>
                                    </label>
                                </div>
                            </div>
                            <div class="grid grid-cols-2 gap-4">
                                <div v-if="type === 'monthly'">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Bulan</label>
                                    <select v-model="printMonth" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                        <option v-for="m in months" :key="m.value" :value="m.value">{{ m.label }}</option>
                                    </select>
                                </div>
                                <div :class="{'col-span-2': type === 'yearly'}">
                                    <label class="block text-sm font-medium text-gray-700 mb-1.5">Tahun</label>
                                    <input v-model="printYear" type="number" class="w-full rounded-xl border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                </div>
                            </div>
                        </div>

                        <div class="mt-6 pt-5 border-t border-gray-100 flex justify-end">
                            <PrimaryButton @click="printFinance" class="gap-2">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                Buka Dokumen Cetak
                            </PrimaryButton>
                        </div>
                    </Card>

                    <!-- Info / Instructions -->
                    <Card class="bg-gray-50/50 border-none">
                        <h4 class="font-bold text-gray-900 mb-3">Informasi Laporan Bendahara</h4>
                        <ul class="space-y-3 text-sm text-gray-600">
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Laporan ini hanya mencakup transaksi kas yang dikelola oleh Bendahara (bukan kas operasional harian mekanik).</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                <span>Pemasukan otomatis mencatat setoran dari mekanik yang telah <strong class="text-gray-800">disetujui</strong>.</span>
                            </li>
                            <li class="flex items-start gap-2">
                                <svg class="w-5 h-5 text-amber-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 17h2a2 2 0 002-2v-4a2 2 0 00-2-2H5a2 2 0 00-2 2v4a2 2 0 002 2h2m2 4h6a2 2 0 002-2v-4a2 2 0 00-2-2H9a2 2 0 00-2 2v4a2 2 0 002 2zm8-12V5a2 2 0 00-2-2H9a2 2 0 00-2 2v4h10z" /></svg>
                                <span>Untuk mencetak, pastikan memilih bulan dan tahun yang sesuai, lalu klik tombol "Buka Dokumen Cetak". Jendela baru akan terbuka untuk pratinjau cetak.</span>
                            </li>
                        </ul>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
