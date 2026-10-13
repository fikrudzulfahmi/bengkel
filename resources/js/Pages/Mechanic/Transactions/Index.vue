<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link } from '@inertiajs/vue3';
import Card from '@/Components/Card.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    transactions: Object,
});

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};
</script>

<template>
    <Head title="Riwayat Transaksi" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Riwayat Transaksi Servis</h2>
                <Link :href="route('mechanic.transactions.create')">
                    <PrimaryButton>
                        + Kasir Baru
                    </PrimaryButton>
                </Link>
            </div>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="max-w-7xl mx-auto space-y-6">
                
                <Card noPadding>
                    <div class="overflow-x-auto">
                        <table class="w-full text-left border-collapse">
                            <thead>
                                <tr class="bg-gray-50/50 text-gray-500 text-xs uppercase tracking-wider border-b border-gray-100">
                                    <th class="p-5 font-medium">No Nota</th>
                                    <th class="p-5 font-medium">Tanggal</th>
                                    <th class="p-5 font-medium">Pelanggan & Motor</th>
                                    <th class="p-5 font-medium text-right">Diskon</th>
                                    <th class="p-5 font-medium text-right">Grand Total</th>
                                    <th class="p-5 font-medium text-center">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="trx in transactions.data" :key="trx.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-5 font-bold text-gray-700">#TRX-{{ String(trx.id).padStart(6, '0') }}</td>
                                    <td class="p-5 text-gray-600 text-sm">{{ new Date(trx.created_at).toLocaleString('id-ID') }}</td>
                                    <td class="p-5">
                                        <div class="font-bold text-gray-800">{{ trx.customer?.name || 'Umum' }}</div>
                                        <div class="text-sm text-primary-dark">{{ trx.motorcycle?.plate_number }} - {{ trx.motorcycle?.type }}</div>
                                    </td>
                                    <td class="p-5 text-right text-red-500 font-medium">{{ formatRp(trx.discount) }}</td>
                                    <td class="p-5 text-right font-extrabold text-gray-900">{{ formatRp(trx.total_price) }}</td>
                                    <td class="p-5 text-center">
                                        <a :href="route('mechanic.transactions.print', trx.id) + '?popup=1'" target="_blank" class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-lg font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 hover:-translate-y-0.5 hover:shadow-soft focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-primary focus:ring-offset-2 transition-all duration-300 ease-in-out">
                                            Cetak Struk
                                        </a>
                                    </td>
                                </tr>
                                <tr v-if="transactions.data.length === 0">
                                    <td colspan="6" class="p-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v12a2 2 0 002 2h10a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path></svg>
                                            <span class="text-sm">Belum ada transaksi servis.</span>
                                        </div>
                                    </td>
                                </tr>
                            </tbody>
                        </table>
                    </div>

                    <!-- Pagination -->
                    <div class="p-5 border-t border-gray-50 flex justify-end bg-gray-50/30 rounded-b-2xl" v-if="transactions.links && transactions.links.length > 3">
                        <div class="flex space-x-1">
                            <Link 
                                v-for="(link, i) in transactions.links" 
                                :key="i" 
                                :href="link.url || '#'"
                                v-html="link.label"
                                class="px-3 py-1.5 rounded-lg text-sm font-medium transition-colors"
                                :class="{'bg-primary text-white shadow-soft': link.active, 'text-gray-500 hover:bg-gray-200': !link.active, 'opacity-50 cursor-not-allowed': !link.url}"
                                :preserve-scroll="true"
                            />
                        </div>
                    </div>
                </Card>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
