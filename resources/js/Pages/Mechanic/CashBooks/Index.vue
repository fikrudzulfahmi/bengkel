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
    filters: Object
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

const showModal = ref(false);

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    form.date = new Date().toISOString().split('T')[0];
    showModal.value = true;
};

const submit = () => {
    form.post(route('mechanic.cash-books.store'), {
        onSuccess: () => showModal.value = false,
    });
};

const deleteEntry = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus catatan ini?')) {
        useForm({}).delete(route('mechanic.cash-books.destroy', id));
    }
};
</script>

<template>
    <Head title="Buku Kas" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Buku Kas <span class="text-gray-400 text-sm font-normal ml-2">(Di luar servis & sparepart)</span></h2>
                <PrimaryButton @click="openCreateModal">
                    + Catat Kas
                </PrimaryButton>
            </div>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">
                
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

                <!-- Table -->
                <Card noPadding>
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

                <!-- Modal -->
                <div v-if="showModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showModal = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                                    <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4" id="modal-title">
                                        Catat Kas Baru
                                    </h3>
                                    
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
                                    <button type="submit" :disabled="form.processing" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-red-600 focus:outline-none sm:ml-3 sm:w-auto sm:text-sm disabled:opacity-50">
                                        Simpan
                                    </button>
                                    <button type="button" @click="showModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                        Batal
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
