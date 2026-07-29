<script setup>
import { Head, router } from '@inertiajs/vue3';
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

const closeOrBack = () => {
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get('popup') === '1') {
        window.close();
    } else {
        router.get(route('mechanic.transactions.create'));
    }
};

onMounted(() => {
    setTimeout(() => {
        window.print();
        
        const urlParams = new URLSearchParams(window.location.search);
        if (urlParams.get('popup') === '1') {
            window.close();
        } else {
            // Kembali ke halaman kasir setelah dialog print ditutup menggunakan SPA navigasi (tanpa reload / tab baru)
            router.get(route('mechanic.transactions.create'));
        }
    }, 500);
});
</script>

<template>
    <Head title="Cetak Struk Servis" />

    <div class="bg-white text-black p-4 md:p-4 font-sans max-w-4xl mx-auto w-full text-xs">
        <!-- Header Struk -->
        <div class="text-center mb-4 border-b border-black pb-2">
            <h1 class="text-lg font-bold uppercase tracking-wider mb-1">SMEKISA MOTOR CARE</h1>
            <p class="text-xs">SMKS Islam 1 Kota Blitar (Teknik Sepeda Motor)</p>
            <p class="text-[10px] mt-1">Jl. Musi No.6, Kota Blitar | Telp: (0342) 801111</p>
        </div>

        <div class="flex justify-between mb-4">
            <div class="w-1/2">
                <table class="w-full text-xs">
                    <tbody>
                        <tr><td class="w-20 font-semibold py-0.5">Nama</td><td>: {{ transaction.customer?.name || 'Umum' }}</td></tr>
                        <tr><td class="w-20 font-semibold py-0.5">Plat Motor</td><td>: {{ transaction.motorcycle?.plate_number || '-' }}</td></tr>
                        <tr><td class="w-20 font-semibold py-0.5">Tipe Motor</td><td>: {{ transaction.motorcycle?.type || '-' }}</td></tr>
                    </tbody>
                </table>
            </div>
            <div class="w-1/3">
                <table class="w-full text-xs">
                    <tbody>
                        <tr><td class="w-16 font-semibold py-0.5">Nota</td><td>: #TRX-{{ String(transaction.id).padStart(6, '0') }}</td></tr>
                        <tr><td class="w-16 font-semibold py-0.5">Tanggal</td><td>: {{ new Date(transaction.created_at).toLocaleDateString('id-ID') }}</td></tr>
                        <tr><td class="w-16 font-semibold py-0.5">Mekanik</td><td>: {{ transaction.user?.name }}</td></tr>
                    </tbody>
                </table>
            </div>
        </div>

        <!-- Tabel Detail -->
        <div class="mb-4 border border-black rounded">
            <table class="w-full text-left border-collapse text-xs">
                <thead>
                    <tr class="bg-gray-100 border-b border-black">
                        <th class="p-1 font-bold text-center border-r border-black w-8">No</th>
                        <th class="p-1 font-bold border-r border-black">Item</th>
                        <th class="p-1 font-bold text-center border-r border-black w-10">Qty</th>
                        <th class="p-1 font-bold text-right border-r border-black w-24">Harga</th>
                        <th class="p-1 font-bold text-right w-24">Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- Jasa Servis -->
                    <tr v-for="(svc, index) in transaction.transaction_services" :key="'svc-'+svc.id" class="border-b border-black border-dashed">
                        <td class="p-1 text-center border-r border-black">{{ index + 1 }}</td>
                        <td class="p-1 border-r border-black">Jasa: {{ svc.service?.name || 'Servis' }}</td>
                        <td class="p-1 text-center border-r border-black">1</td>
                        <td class="p-1 text-right border-r border-black">{{ formatRp(svc.price) }}</td>
                        <td class="p-1 text-right">{{ formatRp(svc.price) }}</td>
                    </tr>
                    
                    <!-- Spareparts -->
                    <tr v-for="(item, index) in transaction.details" :key="'sp-'+item.id" class="border-b border-black border-dashed last:border-0">
                        <td class="p-1 text-center border-r border-black">{{ transaction.transaction_services.length + index + 1 }}</td>
                        <td class="p-1 border-r border-black">{{ item.sparepart?.name }}</td>
                        <td class="p-1 text-center border-r border-black">{{ item.qty }}</td>
                        <td class="p-1 text-right border-r border-black">{{ formatRp(item.price) }}</td>
                        <td class="p-1 text-right">{{ formatRp(item.price * item.qty) }}</td>
                    </tr>
                </tbody>
            </table>
        </div>

        <!-- Total -->
        <div class="flex justify-end mb-4">
            <div class="w-2/3 md:w-1/2">
                <table class="w-full text-right text-xs">
                    <tbody>
                        <tr>
                            <td class="py-0.5 pr-4">Subtotal Jasa:</td>
                            <td class="py-0.5">{{ formatRp(totalService()) }}</td>
                        </tr>
                        <tr>
                            <td class="py-0.5 pr-4">Subtotal Sparepart:</td>
                            <td class="py-0.5">{{ formatRp(totalSparepart()) }}</td>
                        </tr>
                        <tr v-if="transaction.discount > 0" class="text-red-600">
                            <td class="py-0.5 pr-4">Diskon:</td>
                            <td class="py-0.5">- {{ formatRp(transaction.discount) }}</td>
                        </tr>
                        <tr class="font-bold text-sm">
                            <td class="py-1 pr-4">GRAND TOTAL:</td>
                            <td class="py-1 border-t border-black">{{ formatRp(transaction.total_price) }}</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>

        <div class="mt-4 pt-2 border-t border-dashed border-gray-400 text-center text-[10px] text-gray-500 print:block hidden">
            Dicetak otomatis oleh Sistem E-Bengkel pada {{ new Date().toLocaleString('id-ID') }}
        </div>
        
        <!-- Tombol Kembali -->
        <div class="mt-8 text-center print:hidden">
            <button @click="closeOrBack" class="bg-gray-800 text-white px-6 py-2 rounded shadow hover:bg-gray-700">
                Kembali ke Kasir
            </button>
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
