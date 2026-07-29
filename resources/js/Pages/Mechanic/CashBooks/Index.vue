<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    cashBooks: Array,
    summary: Object,
    filters: Object,
    pendingDeposits: Array,
    depositHistory: Array,
    bendaharaCashBooks: Array,
    bendaharaSummary: Object,
});

const month = ref(props.filters.month);
const year = ref(props.filters.year);

const filterData = () => {
    router.get(route('mechanic.cash-books.index'), { month: month.value, year: year.value }, { preserveState: true });
};

const form = useForm({
    type: 'pemasukan',
    amount: '',
    description: '',
    date: new Date().toISOString().split('T')[0],
});

const setorForm = useForm({
    amount: '',
    description: '',
    date: new Date().toISOString().split('T')[0],
});

const showModal = ref(false);
const showSetorModal = ref(false);
const activeTab = ref('kas'); // kas, setoran, bendahara

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
};

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.date = new Date().toISOString().split('T')[0];
    showModal.value = true;
};

const openSetorModal = () => {
    setorForm.reset();
    setorForm.clearErrors();
    setorForm.date = new Date().toISOString().split('T')[0];
    showSetorModal.value = true;
};

const submit = () => {
    form.post(route('mechanic.cash-books.store'), {
        onSuccess: () => showModal.value = false,
    });
};

const submitSetor = () => {
    setorForm.post(route('mechanic.cash-books.setor-bendahara'), {
        onSuccess: () => showSetorModal.value = false,
    });
};

const deleteEntry = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus catatan ini?')) {
        useForm({}).delete(route('mechanic.cash-books.destroy', id));
    }
};

const depositStatusLabel = (s) => {
    if (s === 'approved') return 'Disetujui';
    if (s === 'rejected') return 'Ditolak';
    return 'Menunggu';
};

const depositBadgeType = (s) => {
    if (s === 'approved') return 'success';
    if (s === 'rejected') return 'danger';
    return 'warning';
};
</script>

<template>
    <Head title="Buku Kas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buku Kas <span class="text-gray-400 text-sm font-normal ml-2">(Di luar servis &amp; sparepart)</span></h2>
                <div class="flex items-center gap-3">
                    <button @click="openSetorModal"
                        class="inline-flex items-center gap-2 bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold px-4 py-2 rounded-xl transition-all shadow-sm">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                        </svg>
                        Setor ke Bendahara
                    </button>
                    <PrimaryButton @click="openCreateModal">
                        + Catat Kas
                    </PrimaryButton>
                </div>
            </div>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Pending Deposit Alert -->
                <div v-if="pendingDeposits.length > 0"
                    class="flex items-center gap-4 bg-amber-50 border border-amber-200 rounded-2xl p-4">
                    <div class="w-10 h-10 rounded-full bg-amber-100 flex items-center justify-center text-amber-600 flex-shrink-0">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z" />
                        </svg>
                    </div>
                    <div class="flex-1">
                        <div class="font-semibold text-amber-800 text-sm">
                            {{ pendingDeposits.length }} setoran menunggu verifikasi bendahara
                        </div>
                        <div class="text-xs text-amber-600 mt-0.5">
                            Total: {{ formatRp(pendingDeposits.reduce((s, d) => s + d.amount, 0)) }} — Saldo belum dikurangi sampai disetujui
                        </div>
                    </div>
                    <button @click="activeTab = 'setoran'"
                        class="text-xs font-semibold text-amber-700 bg-amber-100 hover:bg-amber-200 px-3 py-1.5 rounded-lg transition-colors">
                        Lihat
                    </button>
                </div>

                <!-- Filter -->
                <Card>
                    <div class="flex flex-wrap items-end gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700 mb-1">Bulan</label>
                            <select v-model="month" @change="filterData" class="rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors">
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
                            <input v-model="year" type="number" @change="filterData" class="rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm w-24 transition-colors">
                        </div>
                    </div>
                </Card>

                <!-- Summary Cards -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card>
                        <div class="flex items-center gap-4">
                            <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12"></path></svg>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium mb-1">Total Pemasukan</div>
                                <div class="text-2xl font-bold text-gray-800">{{ formatRp(summary.pemasukan) }}</div>
                            </div>
                        </div>
                    </Card>
                    <Card>
                        <div class="flex items-center gap-4">
                            <div class="p-4 rounded-2xl bg-red-50 text-red-500">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6"></path></svg>
                            </div>
                            <div>
                                <div class="text-gray-500 text-sm font-medium mb-1">Total Pengeluaran</div>
                                <div class="text-2xl font-bold text-gray-800">{{ formatRp(summary.pengeluaran) }}</div>
                            </div>
                        </div>
                    </Card>
                    <Card class="bg-gradient-to-br from-primary to-secondary text-white border-transparent">
                        <div class="flex items-center gap-4">
                            <div class="p-4 rounded-2xl bg-white/20 text-white backdrop-blur-sm">
                                <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div>
                                <div class="text-primary-light text-sm font-medium mb-1">Saldo Kas</div>
                                <div class="text-3xl font-extrabold">{{ formatRp(summary.saldo) }}</div>
                            </div>
                        </div>
                    </Card>
                </div>

                <!-- Tabs -->
                <div class="flex rounded-xl overflow-hidden border border-gray-200 w-fit">
                    <button @click="activeTab = 'kas'"
                        :class="activeTab === 'kas' ? 'bg-primary text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-5 py-2 text-sm font-medium transition-colors">
                        Kas Harian
                    </button>
                    <button @click="activeTab = 'setoran'"
                        :class="activeTab === 'setoran' ? 'bg-amber-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-5 py-2 text-sm font-medium transition-colors border-l border-gray-200 flex items-center gap-2">
                        Riwayat Setoran
                        <span v-if="pendingDeposits.length > 0"
                            class="bg-red-500 text-white text-xs font-bold w-5 h-5 rounded-full flex items-center justify-center">
                            {{ pendingDeposits.length }}
                        </span>
                    </button>
                    <button @click="activeTab = 'bendahara'"
                        :class="activeTab === 'bendahara' ? 'bg-emerald-600 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                        class="px-5 py-2 text-sm font-medium transition-colors border-l border-gray-200">
                        Laporan Bendahara
                    </button>
                </div>

                <!-- Tab: Kas Harian -->
                <Card noPadding v-if="activeTab === 'kas'">
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="p-5 font-medium">Tanggal</th>
                                    <th class="p-5 font-medium">Tipe</th>
                                    <th class="p-5 font-medium">Keterangan</th>
                                    <th class="p-5 font-medium text-right">Nominal</th>
                                    <th class="p-5 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="cash in cashBooks" :key="cash.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-5 text-sm text-gray-600">{{ new Date(cash.date).toLocaleDateString('id-ID') }}</td>
                                    <td class="p-5">
                                        <Badge :type="cash.type === 'pemasukan' ? 'success' : 'danger'">
                                            {{ cash.type }}
                                        </Badge>
                                    </td>
                                    <td class="p-5 text-sm text-gray-800">{{ cash.description }}</td>
                                    <td class="p-5 text-right font-semibold text-gray-900">{{ formatRp(cash.amount) }}</td>
                                    <td class="p-5 text-right">
                                        <button @click="deleteEntry(cash.id)" class="text-red-500 hover:text-red-700 font-medium text-sm transition-colors p-2 hover:bg-red-50 rounded-lg">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="cashBooks.length === 0">
                                    <td colspan="5" class="p-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"></path></svg>
                                            <span class="text-sm">Belum ada catatan kas bulan ini.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>

                <!-- Tab: Riwayat Setoran -->
                <div v-if="activeTab === 'setoran'" class="space-y-4">
                    <template v-if="pendingDeposits.length > 0">
                        <h4 class="text-sm font-bold text-amber-700 flex items-center gap-2">
                            <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                            Menunggu Verifikasi
                        </h4>
                        <Card noPadding>
                            <div class="overflow-x-auto">
                                <table class="w-full text-left border-collapse">
                                    <thead>
                                        <tr class="bg-amber-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-amber-100">
                                            <th class="p-4 font-medium">Tanggal</th>
                                            <th class="p-4 font-medium">Keterangan</th>
                                            <th class="p-4 font-medium text-right">Nominal</th>
                                            <th class="p-4 font-medium">Status</th>
                                        </tr>
                                    </thead>
                                    <tbody class="divide-y divide-gray-50">
                                        <tr v-for="dep in pendingDeposits" :key="dep.id" class="hover:bg-amber-50/30 transition-colors">
                                            <td class="p-4 text-sm text-gray-600">{{ formatDate(dep.date) }}</td>
                                            <td class="p-4 text-sm text-gray-800">{{ dep.description }}</td>
                                            <td class="p-4 text-right font-bold text-gray-900">{{ formatRp(dep.amount) }}</td>
                                            <td class="p-4">
                                                <Badge type="warning">Menunggu Verifikasi</Badge>
                                            </td>
                                        </tr>
                                    </tbody>
                                </table>
                            </div>
                        </Card>
                    </template>

                    <h4 class="text-sm font-bold text-gray-600 mt-4">Riwayat Diproses Bulan Ini</h4>
                    <Card noPadding>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                        <th class="p-4 font-medium">Tanggal</th>
                                        <th class="p-4 font-medium">Keterangan</th>
                                        <th class="p-4 font-medium text-right">Nominal</th>
                                        <th class="p-4 font-medium">Status</th>
                                        <th class="p-4 font-medium">Catatan</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr v-for="dep in depositHistory" :key="dep.id" class="hover:bg-gray-50/50 transition-colors">
                                        <td class="p-4 text-sm text-gray-600">{{ formatDate(dep.date) }}</td>
                                        <td class="p-4 text-sm text-gray-800">{{ dep.description }}</td>
                                        <td class="p-4 text-right font-bold text-gray-900">{{ formatRp(dep.amount) }}</td>
                                        <td class="p-4">
                                            <Badge :type="depositBadgeType(dep.status)">{{ depositStatusLabel(dep.status) }}</Badge>
                                        </td>
                                        <td class="p-4 text-xs text-gray-500">{{ dep.rejection_note || '-' }}</td>
                                    </tr>
                                    <tr v-if="depositHistory.length === 0">
                                        <td colspan="5" class="p-8 text-center text-sm text-gray-400">Belum ada riwayat setoran bulan ini.</td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </Card>
                </div>

                <!-- Tab: Laporan Kas Bendahara (read-only) -->
                <div v-if="activeTab === 'bendahara'" class="space-y-6">
                    <div class="flex items-center gap-3 bg-emerald-50 border border-emerald-200 rounded-2xl p-4">
                        <div class="w-9 h-9 rounded-full bg-emerald-100 flex items-center justify-center text-emerald-600 flex-shrink-0">
                            <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z" /><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z" /></svg>
                        </div>
                        <div>
                            <div class="font-semibold text-emerald-800 text-sm">Laporan Kas Bendahara (Hanya Lihat)</div>
                            <div class="text-xs text-emerald-600 mt-0.5">Data ini dikelola oleh bendahara. Anda hanya dapat melihat saldo dan rinciannya.</div>
                        </div>
                    </div>
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                        <Card>
                            <div class="flex items-center gap-4">
                                <div class="p-4 rounded-2xl bg-emerald-50 text-emerald-600">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 11l5-5m0 0l5 5m-5-5v12" /></svg>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm font-medium mb-1">Pemasukan Bendahara</div>
                                    <div class="text-2xl font-bold text-gray-800">{{ formatRp(bendaharaSummary.pemasukan) }}</div>
                                </div>
                            </div>
                        </Card>
                        <Card>
                            <div class="flex items-center gap-4">
                                <div class="p-4 rounded-2xl bg-red-50 text-red-500">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 13l-5 5m0 0l-5-5m5 5V6" /></svg>
                                </div>
                                <div>
                                    <div class="text-gray-500 text-sm font-medium mb-1">Pengeluaran Bendahara</div>
                                    <div class="text-2xl font-bold text-gray-800">{{ formatRp(bendaharaSummary.pengeluaran) }}</div>
                                </div>
                            </div>
                        </Card>
                        <Card class="bg-gradient-to-br from-emerald-600 to-teal-600 text-white border-transparent">
                            <div class="flex items-center gap-4">
                                <div class="p-4 rounded-2xl bg-white/20 text-white">
                                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                </div>
                                <div>
                                    <div class="text-white/80 text-sm font-medium mb-1">Saldo Kas Bendahara</div>
                                    <div class="text-3xl font-extrabold">{{ formatRp(bendaharaSummary.saldo) }}</div>
                                </div>
                            </div>
                        </Card>
                    </div>
                    <Card noPadding>
                        <div class="overflow-x-auto">
                            <table class="w-full text-left border-collapse">
                                <thead>
                                    <tr class="bg-emerald-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-emerald-100">
                                        <th class="p-5 font-medium">Tanggal</th>
                                        <th class="p-5 font-medium">Tipe</th>
                                        <th class="p-5 font-medium">Keterangan</th>
                                        <th class="p-5 font-medium text-right">Nominal</th>
                                    </tr>
                                </thead>
                                <tbody class="divide-y divide-gray-50">
                                    <tr v-for="cash in bendaharaCashBooks" :key="cash.id" class="hover:bg-emerald-50/30 transition-colors">
                                        <td class="p-5 text-sm text-gray-600">{{ formatDate(cash.date) }}</td>
                                        <td class="p-5">
                                            <Badge :type="cash.type === 'pemasukan' ? 'success' : 'danger'">{{ cash.type }}</Badge>
                                        </td>
                                        <td class="p-5 text-sm text-gray-800">{{ cash.description }}</td>
                                        <td class="p-5 text-right font-semibold text-gray-900">{{ formatRp(cash.amount) }}</td>
                                    </tr>
                                    <tr v-if="bendaharaCashBooks.length === 0">
                                        <td colspan="4" class="p-10 text-center text-gray-500">
                                            <div class="flex flex-col items-center">
                                                <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4" /></svg>
                                                <span class="text-sm">Belum ada catatan kas bendahara bulan ini.</span>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </Card>
                </div>

                <!-- Modal Catat Kas -->
                <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">Catat Kas Baru</h3>
                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                                            <input v-model="form.date" type="date" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Jenis Kas</label>
                                            <select v-model="form.type" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                                                <option value="pemasukan">Pemasukan</option>
                                                <option value="pengeluaran">Pengeluaran</option>
                                            </select>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                            <input v-model="form.amount" type="number" min="1" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Keterangan / Deskripsi</label>
                                            <textarea v-model="form.description" rows="3" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required placeholder="Contoh: Beli sapu, Beli sabun cuci tangan..."></textarea>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                                    <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-red-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">Simpan</button>
                                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">Batal</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Setor ke Bendahara -->
                <div v-if="showSetorModal" class="fixed inset-0 z-50 overflow-y-auto" role="dialog" aria-modal="true">
                    <div class="flex items-center justify-center min-h-screen p-4">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showSetorModal = false"></div>
                        <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full z-10">
                            <form @submit.prevent="submitSetor">
                                <div class="p-6">
                                    <div class="flex items-center gap-3 mb-5">
                                        <div class="w-11 h-11 rounded-xl bg-amber-100 flex items-center justify-center text-amber-600">
                                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 19l9 2-9-18-9 18 9-2zm0 0v-8" />
                                            </svg>
                                        </div>
                                        <div>
                                            <h3 class="text-lg font-bold text-gray-900">Setor ke Bendahara</h3>
                                            <p class="text-xs text-gray-500">Pengajuan akan menunggu verifikasi bendahara</p>
                                        </div>
                                    </div>

                                    <div class="bg-amber-50 border border-amber-100 rounded-xl p-4 mb-5 text-sm text-amber-800">
                                        <div class="flex items-start gap-2">
                                            <svg class="w-4 h-4 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            <span>Uang akan dikurangi dari kas Anda dan masuk ke kas bendahara <strong>setelah disetujui</strong> oleh bendahara.</span>
                                        </div>
                                    </div>

                                    <div class="space-y-4">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Tanggal Setoran</label>
                                            <input v-model="setorForm.date" type="date" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-amber-400 focus:ring-amber-400 sm:text-sm" required>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nominal (Rp)</label>
                                            <input v-model="setorForm.amount" type="number" min="1" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-amber-400 focus:ring-amber-400 sm:text-sm" required placeholder="0">
                                            <p v-if="setorForm.errors.amount" class="text-red-500 text-xs mt-1">{{ setorForm.errors.amount }}</p>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Keterangan</label>
                                            <textarea v-model="setorForm.description" rows="3" class="mt-1 block w-full rounded-xl border-gray-200 shadow-sm focus:border-amber-400 focus:ring-amber-400 sm:text-sm" required placeholder="Contoh: Setoran hasil servis minggu ke-3..."></textarea>
                                            <p v-if="setorForm.errors.description" class="text-red-500 text-xs mt-1">{{ setorForm.errors.description }}</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50 px-6 py-4 flex justify-end gap-3 rounded-b-2xl">
                                    <button type="button" @click="showSetorModal = false"
                                        class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                        Batal
                                    </button>
                                    <button type="submit" :disabled="setorForm.processing"
                                        class="px-5 py-2 rounded-lg bg-amber-500 hover:bg-amber-600 text-white text-sm font-semibold transition-colors disabled:opacity-50 flex items-center gap-2">
                                        <svg v-if="setorForm.processing" class="animate-spin w-4 h-4" fill="none" viewBox="0 0 24 24"><circle class="opacity-25" cx="12" cy="12" r="10" stroke="currentColor" stroke-width="4"/><path class="opacity-75" fill="currentColor" d="M4 12a8 8 0 018-8V0C5.373 0 0 5.373 0 12h4z"/></svg>
                                        Kirim Pengajuan
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
