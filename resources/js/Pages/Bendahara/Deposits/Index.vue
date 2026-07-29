<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';

const props = defineProps({
    deposits: Array,
    pendingCount: Number,
    filters: Object,
});

const status = ref(props.filters.status);
const month = ref(props.filters.month);
const year = ref(props.filters.year);

const filterData = () => {
    router.get(route('bendahara.deposits.index'), {
        status: status.value,
        month: month.value,
        year: year.value
    }, { preserveState: true });
};

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value || 0);
};

const formatDate = (dateStr) => {
    return new Date(dateStr).toLocaleDateString('id-ID', { day: '2-digit', month: 'long', year: 'numeric' });
};

const formatDateTime = (dateStr) => {
    if (!dateStr) return '-';
    return new Date(dateStr).toLocaleString('id-ID', {
        day: '2-digit', month: 'short', year: 'numeric',
        hour: '2-digit', minute: '2-digit'
    });
};

// Approve
const approveForm = useForm({});
const approveDeposit = (deposit) => {
    if (confirm(`Setujui setoran dari ${deposit.mechanic?.name} sebesar ${formatRp(deposit.amount)}?\n\nUang akan masuk ke buku kas bendahara dan dikurangi dari kas mekanik.`)) {
        approveForm.post(route('bendahara.deposits.approve', deposit.id));
    }
};

// Reject
const showRejectModal = ref(false);
const selectedDeposit = ref(null);
const rejectForm = useForm({ rejection_note: '' });

const openRejectModal = (deposit) => {
    selectedDeposit.value = deposit;
    rejectForm.rejection_note = '';
    showRejectModal.value = true;
};

const submitReject = () => {
    rejectForm.post(route('bendahara.deposits.reject', selectedDeposit.value.id), {
        onSuccess: () => {
            showRejectModal.value = false;
            selectedDeposit.value = null;
        }
    });
};

const statusBadgeType = (s) => {
    if (s === 'approved') return 'success';
    if (s === 'rejected') return 'danger';
    return 'warning';
};

const statusLabel = (s) => {
    if (s === 'approved') return 'Disetujui';
    if (s === 'rejected') return 'Ditolak';
    return 'Menunggu';
};
</script>

<template>
    <Head title="Verifikasi Setoran" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <div>
                    <h2 class="font-semibold text-xl text-gray-800 leading-tight">Verifikasi Setoran Mekanik</h2>
                    <p class="text-xs text-gray-400 mt-0.5">Kelola pengajuan setoran dari mekanik ke kas bendahara</p>
                </div>
                <div v-if="pendingCount > 0" class="flex items-center gap-2 bg-amber-50 border border-amber-200 text-amber-700 px-4 py-2 rounded-xl text-sm font-semibold">
                    <span class="w-2 h-2 bg-amber-500 rounded-full animate-pulse"></span>
                    {{ pendingCount }} Menunggu Verifikasi
                </div>
            </div>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">

                <!-- Filter Tabs -->
                <Card>
                    <div class="flex flex-wrap items-center gap-4">
                        <div class="flex rounded-xl overflow-hidden border border-gray-200">
                            <button @click="status = 'pending'; filterData()"
                                :class="status === 'pending' ? 'bg-amber-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                class="px-4 py-2 text-sm font-medium transition-colors flex items-center gap-2">
                                Menunggu
                                <span v-if="pendingCount > 0" class="bg-white/30 text-white text-xs px-1.5 py-0.5 rounded-full">{{ pendingCount }}</span>
                            </button>
                            <button @click="status = 'approved'; filterData()"
                                :class="status === 'approved' ? 'bg-emerald-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                class="px-4 py-2 text-sm font-medium transition-colors border-x border-gray-200">
                                Disetujui
                            </button>
                            <button @click="status = 'rejected'; filterData()"
                                :class="status === 'rejected' ? 'bg-red-500 text-white' : 'bg-white text-gray-600 hover:bg-gray-50'"
                                class="px-4 py-2 text-sm font-medium transition-colors">
                                Ditolak
                            </button>
                        </div>

                        <template v-if="status !== 'pending'">
                            <div>
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
                                <input v-model="year" type="number" @change="filterData" class="rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm w-24 transition-colors">
                            </div>
                        </template>
                    </div>
                </Card>

                <!-- Deposits Table -->
                <Card noPadding>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="p-5 font-medium">Mekanik</th>
                                    <th class="p-5 font-medium">Tanggal</th>
                                    <th class="p-5 font-medium">Keterangan</th>
                                    <th class="p-5 font-medium text-right">Nominal</th>
                                    <th class="p-5 font-medium">Status</th>
                                    <th class="p-5 font-medium text-right" v-if="status === 'pending'">Aksi</th>
                                    <th class="p-5 font-medium" v-else>Info</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="deposit in deposits" :key="deposit.id"
                                    :class="[
                                        'transition-colors',
                                        deposit.status === 'pending' ? 'hover:bg-amber-50/30' :
                                        deposit.status === 'approved' ? 'hover:bg-emerald-50/30' : 'hover:bg-red-50/30'
                                    ]">
                                    <td class="p-5">
                                        <div class="flex items-center gap-3">
                                            <div class="w-9 h-9 rounded-full bg-primary/10 text-primary-dark flex items-center justify-center font-bold text-sm">
                                                {{ deposit.mechanic?.name?.charAt(0)?.toUpperCase() }}
                                            </div>
                                            <div>
                                                <div class="font-semibold text-gray-800 text-sm">{{ deposit.mechanic?.name }}</div>
                                                <div class="text-xs text-gray-400">{{ deposit.mechanic?.email }}</div>
                                            </div>
                                        </div>
                                    </td>
                                    <td class="p-5 text-sm text-gray-600">{{ formatDate(deposit.date) }}</td>
                                    <td class="p-5 text-sm text-gray-800 max-w-xs">
                                        <div class="truncate">{{ deposit.description }}</div>
                                        <div v-if="deposit.rejection_note" class="text-xs text-red-500 mt-1">
                                            Alasan: {{ deposit.rejection_note }}
                                        </div>
                                    </td>
                                    <td class="p-5 text-right">
                                        <span class="font-bold text-gray-900">{{ formatRp(deposit.amount) }}</span>
                                    </td>
                                    <td class="p-5">
                                        <Badge :type="statusBadgeType(deposit.status)">
                                            {{ statusLabel(deposit.status) }}
                                        </Badge>
                                    </td>
                                    <!-- Pending: Approve / Reject buttons -->
                                    <td class="p-5 text-right" v-if="status === 'pending'">
                                        <div class="flex items-center justify-end gap-2">
                                            <button @click="approveDeposit(deposit)"
                                                :disabled="approveForm.processing"
                                                class="inline-flex items-center gap-1.5 bg-emerald-500 hover:bg-emerald-600 text-white text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors disabled:opacity-50">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" /></svg>
                                                Setujui
                                            </button>
                                            <button @click="openRejectModal(deposit)"
                                                class="inline-flex items-center gap-1.5 bg-red-100 hover:bg-red-200 text-red-700 text-xs font-semibold px-3 py-1.5 rounded-lg transition-colors">
                                                <svg class="w-3.5 h-3.5" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                                                Tolak
                                            </button>
                                        </div>
                                    </td>
                                    <!-- History: processed at info -->
                                    <td class="p-5" v-else>
                                        <div class="text-xs text-gray-500">{{ formatDateTime(deposit.processed_at) }}</div>
                                    </td>
                                </tr>
                                <tr v-if="deposits.length === 0">
                                    <td colspan="6" class="p-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <div class="w-14 h-14 rounded-full bg-gray-50 flex items-center justify-center mb-3">
                                                <svg class="w-7 h-7 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z" /></svg>
                                            </div>
                                            <span class="text-sm font-medium">
                                                {{ status === 'pending' ? 'Tidak ada setoran yang menunggu verifikasi' : 'Belum ada data setoran' }}
                                            </span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </Card>

            </div>
        </div>

        <!-- Modal Tolak Setoran -->
        <div v-if="showRejectModal" class="fixed inset-0 z-50 overflow-y-auto" aria-modal="true">
            <div class="flex items-center justify-center min-h-screen p-4">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75" @click="showRejectModal = false"></div>
                <div class="relative bg-white rounded-2xl shadow-xl max-w-md w-full p-6 z-10">
                    <div class="flex items-center gap-3 mb-5">
                        <div class="w-11 h-11 rounded-xl bg-red-100 flex items-center justify-center text-red-600">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" /></svg>
                        </div>
                        <div>
                            <h3 class="text-lg font-bold text-gray-900">Tolak Setoran</h3>
                            <p class="text-sm text-gray-500">dari {{ selectedDeposit?.mechanic?.name }}</p>
                        </div>
                    </div>

                    <div class="bg-red-50 rounded-xl p-4 mb-5">
                        <div class="text-sm text-gray-700">{{ selectedDeposit?.description }}</div>
                        <div class="text-xl font-bold text-red-600 mt-1">{{ formatRp(selectedDeposit?.amount) }}</div>
                    </div>

                    <form @submit.prevent="submitReject">
                        <div class="mb-5">
                            <label class="block text-sm font-medium text-gray-700 mb-1">Alasan Penolakan <span class="text-gray-400 font-normal">(opsional)</span></label>
                            <textarea v-model="rejectForm.rejection_note" rows="3"
                                class="w-full rounded-xl border-gray-200 shadow-sm focus:border-red-400 focus:ring-red-400 sm:text-sm"
                                placeholder="Tuliskan alasan penolakan..."></textarea>
                        </div>
                        <div class="flex gap-3 justify-end">
                            <button type="button" @click="showRejectModal = false"
                                class="px-4 py-2 rounded-lg border border-gray-300 text-sm font-medium text-gray-700 hover:bg-gray-50 transition-colors">
                                Batal
                            </button>
                            <button type="submit" :disabled="rejectForm.processing"
                                class="px-4 py-2 rounded-lg bg-red-500 hover:bg-red-600 text-white text-sm font-semibold transition-colors disabled:opacity-50">
                                Ya, Tolak Setoran
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
