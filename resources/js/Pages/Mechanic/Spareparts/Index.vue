<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    spareparts: Array,
});

const form = useForm({
    id: '',
    name: '',
    stock: 0,
    price: 0,
    buy_price: 0,
    is_restock: false,
    restock_qty: 0,
});

const isEditing = ref(false);
const showModal = ref(false);
const showRestockModal = ref(false);
const restockTarget = ref(null);

const formatRp = (value) => {
    if (!value) return '-';
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    showModal.value = true;
};

const openEditModal = (sp) => {
    form.reset();
    form.clearErrors();
    form.id = sp.id;
    form.name = sp.name;
    form.stock = sp.stock;
    form.price = sp.price;
    form.buy_price = sp.buy_price || 0;
    isEditing.value = true;
    showModal.value = true;
};

const openRestockModal = (sp) => {
    form.reset();
    form.clearErrors();
    form.id = sp.id;
    form.name = sp.name;
    form.stock = sp.stock;
    form.price = sp.price;
    form.is_restock = true;
    form.buy_price = sp.buy_price || 0;
    form.restock_qty = 1;
    restockTarget.value = sp;
    showRestockModal.value = true;
};

const submit = () => {
    if (form.is_restock) {
        form.put(route('mechanic.spareparts.update', form.id), {
            onSuccess: () => {
                showRestockModal.value = false;
                form.reset();
            }
        });
        return;
    }

    if (isEditing.value) {
        form.put(route('mechanic.spareparts.update', form.id), {
            onSuccess: () => showModal.value = false,
        });
    } else {
        form.post(route('mechanic.spareparts.store'), {
            onSuccess: () => showModal.value = false,
        });
    }
};

const deleteSp = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus sparepart ini?')) {
        useForm({}).delete(route('mechanic.spareparts.destroy', id));
    }
};
</script>

<template>
    <Head title="Manajemen Sparepart" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Stok Sparepart</h2>
                <PrimaryButton @click="openCreateModal">
                    + Tambah Sparepart
                </PrimaryButton>
            </div>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">
                
                <!-- Table -->
                <Card noPadding>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="p-5 font-medium">Nama Sparepart</th>
                                    <th class="p-5 font-medium text-center">Stok Saat Ini</th>
                                    <th class="p-5 font-medium">Harga Jual</th>
                                    <th class="p-5 font-medium">Harga Beli</th>
                                    <th class="p-5 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="sp in spareparts" :key="sp.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-5 text-gray-800 font-medium">{{ sp.name }}</td>
                                    <td class="p-5 text-center">
                                        <Badge :type="sp.stock < 5 ? 'danger' : 'success'">
                                            {{ sp.stock }} pcs
                                        </Badge>
                                    </td>
                                    <td class="p-5 text-gray-800 font-semibold">{{ formatRp(sp.price) }}</td>
                                    <td class="p-5 text-gray-500 text-sm">{{ formatRp(sp.buy_price) }}</td>
                                    <td class="p-5 text-right space-x-2">
                                        <button @click="openRestockModal(sp)" class="text-green-600 hover:text-green-800 font-medium text-sm transition-colors p-2 hover:bg-green-50 rounded-lg whitespace-nowrap">+ Kulakan</button>
                                        <button @click="openEditModal(sp)" class="text-blue-500 hover:text-blue-700 font-medium text-sm transition-colors p-2 hover:bg-blue-50 rounded-lg">Edit</button>
                                        <button @click="deleteSp(sp.id)" class="text-red-500 hover:text-red-700 font-medium text-sm transition-colors p-2 hover:bg-red-50 rounded-lg">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="spareparts.length === 0">
                                    <td colspan="5" class="p-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 11H5m14 0a2 2 0 012 2v6a2 2 0 01-2 2H5a2 2 0 01-2-2v-6a2 2 0 012-2m14 0V9a2 2 0 00-2-2M5 11V9a2 2 0 002-2m0 0V5a2 2 0 012-2h6a2 2 0 012 2v2M7 7h10"></path></svg>
                                            <span class="text-sm">Belum ada data sparepart.</span>
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
                        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showModal = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-6 pt-6 pb-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-6" id="modal-title">
                                        {{ isEditing ? 'Edit Profil Sparepart' : 'Tambah Sparepart Baru' }}
                                    </h3>
                                    
                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nama Sparepart</label>
                                            <input v-model="form.name" type="text" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required>
                                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1.5">{{ form.errors.name }}</div>
                                        </div>
                                        <div class="grid grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Stok Akhir</label>
                                                <input v-model="form.stock" type="number" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required min="0">
                                                <span v-if="isEditing" class="text-xs text-orange-500 block mt-1">Koreksi stok manual (tidak memotong uang kas). Untuk pengadaan, gunakan tombol + Kulakan.</span>
                                                <span v-else class="text-xs text-gray-500 block mt-1">Jika stok diisi &gt; 0, sistem akan mencatatnya sebagai pengeluaran awal di Buku Kas.</span>
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                                                <input v-model="form.price" type="number" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required min="0">
                                            </div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Harga Beli / Kulakan <span class="text-gray-400 font-normal ml-1">(Opsional - Rp)</span></label>
                                            <input v-model="form.buy_price" type="number" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" min="0">
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse border-t border-gray-50 gap-3">
                                    <PrimaryButton type="submit" :disabled="form.processing">
                                        Simpan
                                    </PrimaryButton>
                                    <button type="button" @click="showModal = false" class="inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-5 py-2.5 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none transition-colors">
                                        Batal
                                    </button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>

                <!-- Modal Restock -->
                <div v-if="showRestockModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
                    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                        <div class="fixed inset-0 bg-gray-900/50 backdrop-blur-sm transition-opacity" @click="showRestockModal = false"></div>
                        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                        <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full border border-gray-100">
                            <form @submit.prevent="submit">
                                <div class="bg-white px-6 pt-6 pb-6">
                                    <h3 class="text-xl font-bold text-gray-900 mb-2" id="modal-title">
                                        Tambah Stok (Kulakan)
                                    </h3>
                                    <p class="text-sm text-gray-500 mb-6">Restock untuk item: <strong class="text-gray-800">{{ restockTarget?.name }}</strong></p>
                                    
                                    <div class="space-y-5">
                                        <div class="grid grid-cols-2 gap-5">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Jumlah Ditambahkan</label>
                                                <input v-model="form.restock_qty" type="number" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required min="1">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700">Harga Beli Satuan (Rp)</label>
                                                <input v-model="form.buy_price" type="number" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required min="0">
                                            </div>
                                        </div>
                                        <div class="bg-orange-50 text-orange-700 p-3 rounded-lg text-sm flex gap-2">
                                            <svg class="w-5 h-5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                                            <p>Aksi ini akan mencatat <strong>Pengeluaran</strong> pada Buku Kas sebesar <span class="font-bold">{{ formatRp(form.restock_qty * form.buy_price) }}</span>.</p>
                                        </div>
                                    </div>
                                </div>
                                <div class="bg-gray-50/50 px-6 py-4 flex flex-row-reverse border-t border-gray-50 gap-3">
                                    <PrimaryButton type="submit" :disabled="form.processing">
                                        Simpan & Catat Pengeluaran
                                    </PrimaryButton>
                                    <button type="button" @click="showRestockModal = false" class="inline-flex justify-center rounded-xl border border-gray-200 shadow-sm px-5 py-2.5 bg-white text-sm font-semibold text-gray-700 hover:bg-gray-50 hover:text-gray-900 focus:outline-none transition-colors">
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
