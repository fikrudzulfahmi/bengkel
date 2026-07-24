<script setup>
import { Head } from '@inertiajs/vue3';
import { onMounted } from 'vue';

const props = defineProps({
    transaction: Object
});

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const totalSparepart = () => {
    return props.transaction.details.reduce((sum, item) => sum + (item.price * item.qty), 0);
};

const totalService = () => {
    return props.transaction.transaction_services.reduce((sum, svc) => sum + svc.price, 0);
};

onMounted(() => {
    window.print();
});
</script>

<template>
    <Head title="Cetak Struk Servis" />

    <div class="bg-white text-black p-8 font-sans max-w-4xl mx-auto w-full min-h-screen">
        <!-- Header Struk -->
        <div class="text-center mb-8 border-b-2 border-black pb-4">
            <h1 class="text-3xl font-bold uppercase tracking-wider mb-2">SMEKISA MOTOR CARE</h1>
            <p class="text-lg">SMKS Islam 1 Kota Blitar (Departemen Teknik Sepeda Motor)</p>
            <p class="text-sm mt-1">Jl. Musi No.6, Kota Blitar | Telp: (0342) 801111</p>
        </div>

        <div class="flex justify-between mb-8">
            <div class="w-1/2">
                <h3 class="font-bold text-lg mb-2">Data Pelanggan:</h3>
                <table class="w-full text-sm">
                    <tr><td class="w-24 font-semibold py-1">Nama</td><td>: {{ transaction.customer?.name || 'Umum' }}</td></tr>
                    <tr><td class="w-24 font-semibold py-1">No. Telp</td><td>: {{ transaction.customer?.phone || '-' }}</td></tr>
                    <tr><td class="w-24 font-semibold py-1">Plat Motor</td><td>: {{ transaction.motorcycle?.plate_number || '-' }}</td></tr>
                    <tr><td class="w-24 font-semibold py-1">Tipe Motor</td><td>: {{ transaction.motorcycle?.type || '-' }}</td></tr>
                </table>
            </div>
            <div class="w-1/3">
                <h3 class="font-bold text-lg mb-2">Data Transaksi:</h3>
                <table class="w-full text-sm">
                    <tr><td class="w-24 font-semibold py-1">No. Nota</td><td>: #TRX-{{ String(transaction.id).padStart(6, '0') }}</td></tr>
                    <tr><td class="w-24 font-semibold py-1">Tanggal</td><td>: {{ new Date(transaction.created_at).toLocaleDateString('id-ID') }}</td></tr>
                    <tr><td class="w-24 font-semibold py-1">Mekanik</td><td>: {{ transaction.user?.name }}</td></tr>
                    <tr><td class="w-24 font-semibold py-1">Status</td><td>: {{ transaction.status.toUpperCase() }}</td></tr>
                </table>
            </div>
        </div>

        <!-- Tabel Detail -->
        <div class="mb-8 border border-black rounded">
            <table class="w-full text-left border-collapse">
                <thead>
                    <tr class="bg-gray-100 border-b border-black">
                        <th class="p-3 font-bold w-12 text-center border-r border-black">No</th>
                        <th class="p-3 font-bold border-r border-black">Keterangan (Jasa / Sparepart)</th>
                        <th class="p-3 font-bold text-center w-20 border-r border-black">Qty</th>
                        <th class="p-3 font-bold text-right w-32 border-r border-black">Harga Satuan</th>
                        <th class="p-3 font-bold text-right w-40">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Jasa Servis -->
                    <tr v-for="(svc, index) in transaction.transaction_services" :key="'svc-'+svc.id" class="border-b border-black">
                        <td class="p-3 text-center border-r border-black">{{ index + 1 }}</td>
                        <td class="p-3 border-r border-black">Jasa: {{ svc.service?.name || 'Servis' }}</td>
                        <td class="p-3 text-center border-r border-black">1</td>
                        <td class="p-3 text-right border-r border-black">{{ formatRp(svc.price) }}</td>
                        <td class="p-3 text-right font-medium">{{ formatRp(svc.price) }}</td>
                    </tr>
                    
                    <!-- Spareparts -->
                    <tr v-for="(item, index) in transaction.details" :key="'sp-'+item.id" class="border-b border-black last:border-0">
                        <td class="p-3 text-center border-r border-black">{{ transaction.transaction_services.length + index + 1 }}</td>
                        <td class="p-3 border-r border-black">{{ item.sparepart?.name }}</td>
                        <td class="p-3 text-center border-r border-black">{{ item.qty }}</td>
                        <td class="p-3 text-right border-r border-black">{{ formatRp(item.price) }}</td>
                        <td class="p-3 text-right font-medium">{{ formatRp(item.price * item.qty) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total & Ttd -->
        <div class="flex justify-end mb-12">
            <div class="w-1/2 md:w-1/3">
                <table class="w-full text-right text-lg">
                    <tr class="text-base text-gray-700">
                        <td class="py-1 pr-4">Subtotal Jasa:</td>
                        <td class="py-1">{{ formatRp(totalService()) }}</td>
                    </tr>
                    <tr class="text-base text-gray-700">
                        <td class="py-1 pr-4">Subtotal Sparepart:</td>
                        <td class="py-1">{{ formatRp(totalSparepart()) }}</td>
                    </tr>
                    <tr v-if="transaction.discount > 0" class="text-base text-red-600">
                        <td class="py-1 pr-4">Diskon:</td>
                        <td class="py-1">- {{ formatRp(transaction.discount) }}</td>
                    </tr>
                    <tr class="font-bold text-xl">
                        <td class="py-2 pr-4">GRAND TOTAL:</td>
                        <td class="py-2 border-t-2 border-black">{{ formatRp(transaction.total_price) }}</td>
                    </tr>
                </table>
            </div>
        </div>
        
        <div class="flex justify-between mt-12 text-center pt-8">
            <div class="w-1/3">
                <p class="mb-16 font-medium">Hormat Kami,</p>
                <p class="font-bold underline">{{ transaction.user?.name }}</p>
                <p class="text-sm">Mekanik</p>
            </div>
            <div class="w-1/3">
                <p class="mb-16 font-medium">Pelanggan,</p>
                <p class="font-bold underline">{{ transaction.customer?.name || '( ........................ )' }}</p>
                <p class="text-sm">Terima Kasih</p>
            </div>
        </div>

        <div class="mt-8 pt-4 border-t border-dashed border-gray-400 text-center text-xs text-gray-500 print:block hidden">
            Dicetak otomatis oleh Sistem E-Bengkel Smekisa Motor Care pada {{ new Date().toLocaleString('id-ID') }}
        </div>
        
        <!-- Tombol Kembali (Tidak Ikut di-print) -->
        <div class="mt-8 text-center print:hidden">
            <button @click="window.close()" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700">Tutup Halaman</button>
        </div>
    </div>
</template>

<style>
@media print {
    body {
        margin: 0;
        padding: 0;
        background-color: white;
    }
    .print\:hidden {
        display: none !important;
    }
    .print\:block {
        display: block !important;
    }
}
</style>
