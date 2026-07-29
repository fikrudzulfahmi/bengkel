<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm, router } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const props = defineProps({
    customers: Array,
    spareparts: Array,
    services: Array,
    pending_transactions: {
        type: Array,
        default: () => []
    }
});

const form = useForm({
    transaction_id: null,
    customer_id: '',
    customer_name: '',
    customer_phone: '',
    motorcycle_id: '',
    motorcycle_plate: '',
    motorcycle_type: '',
    discount: 0,
    notes: '',
    items: [],
    services: [],
    action: 'complete' // 'draft' or 'complete'
});

const isNewCustomer = ref(true);
const isNewMotorcycle = ref(true);

const showSparepartModal = ref(false);
const newSparepart = ref({ name: '', price: 0, buy_price: 0, stock: 1 });

const showRestockModal = ref(false);
const restockData = ref({ id: null, name: '', buy_price: 0, qty: 1, sell_price: 0 });

const showServiceModal = ref(false);
const newService = ref({ name: '', price: 0 });

const selectedCustomer = computed(() => {
    return props.customers.find(c => c.id === form.customer_id);
});

const availableMotorcycles = computed(() => {
    if (selectedCustomer.value) {
        return selectedCustomer.value.motorcycles;
    }
    return [];
});

const customerSelection = ref('NEW');
const motorcycleSelection = ref('NEW');

const selectCustomer = () => {
    const val = customerSelection.value;
    if (val === 'NEW') {
        isNewCustomer.value = true;
        form.customer_id = '';
        form.customer_name = '';
        form.customer_phone = '';
        
        isNewMotorcycle.value = true;
        motorcycleSelection.value = 'NEW';
        form.motorcycle_id = '';
        form.motorcycle_plate = '';
        form.motorcycle_type = '';
    } else {
        isNewCustomer.value = false;
        form.customer_id = parseInt(val);
        const customer = props.customers.find(c => c.id === form.customer_id);
        if (customer) {
            form.customer_name = customer.name;
            form.customer_phone = customer.phone;
            
            if (customer.motorcycles && customer.motorcycles.length > 0) {
                // Auto-select the first motorcycle
                const firstMoto = customer.motorcycles[0];
                motorcycleSelection.value = firstMoto.id;
                isNewMotorcycle.value = false;
                form.motorcycle_id = firstMoto.id;
                form.motorcycle_plate = firstMoto.plate_number;
                form.motorcycle_type = firstMoto.type;
            } else {
                motorcycleSelection.value = 'NEW';
                isNewMotorcycle.value = true;
                form.motorcycle_id = '';
                form.motorcycle_plate = '';
                form.motorcycle_type = '';
            }
        }
    }
};

const selectMotorcycle = () => {
    const val = motorcycleSelection.value;
    if (val === 'NEW') {
        isNewMotorcycle.value = true;
        form.motorcycle_id = '';
        form.motorcycle_plate = '';
        form.motorcycle_type = '';
    } else {
        isNewMotorcycle.value = false;
        form.motorcycle_id = parseInt(val);
        const m = availableMotorcycles.value.find(m => m.id === form.motorcycle_id);
        if (m) {
            form.motorcycle_plate = m.plate_number;
            form.motorcycle_type = m.type;
        }
    }
};

const loadDraft = (draft) => {
    form.reset();
    form.transaction_id = draft.id;
    
    // Load Customer
    if (draft.customer_id) {
        customerSelection.value = draft.customer_id;
        isNewCustomer.value = false;
        form.customer_id = draft.customer_id;
        form.customer_name = draft.customer?.name || '';
        form.customer_phone = draft.customer?.phone || '';
    } else {
        customerSelection.value = 'NEW';
        isNewCustomer.value = true;
    }
    
    // Load Motorcycle
    if (draft.motorcycle_id) {
        motorcycleSelection.value = draft.motorcycle_id;
        isNewMotorcycle.value = false;
        form.motorcycle_id = draft.motorcycle_id;
        form.motorcycle_plate = draft.motorcycle?.plate_number || '';
        form.motorcycle_type = draft.motorcycle?.type || '';
    } else {
        motorcycleSelection.value = 'NEW';
        isNewMotorcycle.value = true;
    }

    form.discount = draft.discount || 0;
    form.notes = draft.notes || '';
    
    // Load Details (Spareparts)
    form.items = draft.details.map(d => ({
        is_new: false,
        sparepart_id: d.sparepart_id,
        name: d.sparepart?.name,
        price: d.price,
        qty: d.qty,
        max_stock: (d.sparepart?.stock || 0) + d.qty // Include existing qty in max stock because we will restore it on backend
    }));
    
    // Load Services
    form.services = draft.transaction_services.map(s => ({
        is_new: false,
        service_id: s.service_id,
        name: s.service?.name,
        price: s.price
    }));
};

const resetForm = () => {
    form.reset();
    form.items = [];
    form.services = [];
    form.transaction_id = null;
    customerSelection.value = 'NEW';
    motorcycleSelection.value = 'NEW';
    isNewCustomer.value = true;
    isNewMotorcycle.value = true;
};

// SPAREPART
const addSparepart = (e) => {
    const val = e.target.value;
    if (val === 'NEW') {
        showSparepartModal.value = true;
        e.target.value = '';
        return;
    }

    const spId = parseInt(val);
    if (!spId) return;
    
    const sp = props.spareparts.find(s => s.id === spId);
    if (sp) {
        const existing = form.items.find(i => i.sparepart_id === spId && !i.is_new);
        if (existing) {
            if (existing.qty < existing.max_stock) existing.qty++;
        } else {
            if (sp.stock > 0) {
                form.items.push({
                    is_new: false,
                    sparepart_id: sp.id,
                    name: sp.name,
                    price: sp.price,
                    qty: 1,
                    max_stock: sp.stock
                });
            } else {
                showRestockModal.value = true;
                restockData.value = {
                    id: sp.id,
                    name: sp.name,
                    buy_price: sp.buy_price || 0,
                    qty: 1,
                    sell_price: sp.price
                };
            }
        }
    }
    e.target.value = '';
};

const submitRestock = () => {
    if (restockData.value.qty < 1 || restockData.value.buy_price < 0) return;
    
    form.items.push({
        is_new: false,
        is_restock: true,
        sparepart_id: restockData.value.id,
        name: restockData.value.name,
        price: restockData.value.sell_price,
        qty: 1, 
        new_buy_price: restockData.value.buy_price,
        restock_qty: restockData.value.qty,
        max_stock: restockData.value.qty,
    });
    
    showRestockModal.value = false;
};

const submitNewSparepart = () => {
    if (!newSparepart.value.name || newSparepart.value.price < 0 || newSparepart.value.stock < 1) return;
    
    form.items.push({
        is_new: true,
        new_name: newSparepart.value.name,
        price: newSparepart.value.price,
        new_price: newSparepart.value.price,
        new_buy_price: newSparepart.value.buy_price,
        qty: 1, // 1 used for this transaction
        new_stock: newSparepart.value.stock, // stock added to DB
        max_stock: newSparepart.value.stock,
        name: newSparepart.value.name + ' (Baru)'
    });
    
    showSparepartModal.value = false;
    newSparepart.value = { name: '', price: 0, buy_price: 0, stock: 1 };
};

const removeItem = (index) => {
    form.items.splice(index, 1);
};

// SERVICE
const addService = (e) => {
    const val = e.target.value;
    if (val === 'NEW') {
        showServiceModal.value = true;
        e.target.value = '';
        return;
    }

    const svcId = parseInt(val);
    if (!svcId) return;
    
    const svc = props.services.find(s => s.id === svcId);
    if (svc) {
        const existing = form.services.find(i => i.service_id === svcId && !i.is_new);
        if (!existing) {
            form.services.push({
                is_new: false,
                service_id: svc.id,
                name: svc.name,
                price: svc.price,
            });
        }
    }
    e.target.value = '';
};

const submitNewService = () => {
    if (!newService.value.name || newService.value.price < 0) return;
    
    form.services.push({
        is_new: true,
        new_name: newService.value.name,
        price: newService.value.price,
        new_price: newService.value.price,
        name: newService.value.name + ' (Baru)'
    });
    
    showServiceModal.value = false;
    newService.value = { name: '', price: 0 };
};

const removeService = (index) => {
    form.services.splice(index, 1);
};


// TOTALS
const totalSparepart = computed(() => {
    return form.items.reduce((sum, item) => sum + (item.price * item.qty), 0);
});

const totalService = computed(() => {
    return form.services.reduce((sum, svc) => sum + svc.price, 0);
});

const grandTotal = computed(() => {
    const total = totalSparepart.value + totalService.value - (form.discount || 0);
    return total > 0 ? total : 0;
});

const formatRp = (value) => {
    return new Intl.NumberFormat('id-ID', { style: 'currency', currency: 'IDR', minimumFractionDigits: 0 }).format(value);
};

const submit = (actionType) => {
    if (form.items.length === 0 && form.services.length === 0) {
        alert('Harap pilih minimal 1 Sparepart atau 1 Jasa Servis!');
        return;
    }

    form.action = actionType;

    const routeName = form.transaction_id 
        ? route('mechanic.transactions.update', form.transaction_id) 
        : route('mechanic.transactions.store');
        
    const method = form.transaction_id ? 'put' : 'post';

    form[method](routeName, {
        onSuccess: (page) => {
            if (page.props.flash?.action === 'complete' && page.props.flash?.transaction_id) {
                // Redirect directly to print route to avoid popup blocker
                router.get(route('mechanic.transactions.print', page.props.flash.transaction_id));
            } else {
                resetForm();
            }
        },
    });
};
</script>

<template>
    <Head title="Kasir & Service" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kasir & Input Service</h2>
        </template>

        <div class="p-4 md:p-6 flex flex-col md:flex-row gap-6">
            
            <!-- Sidebar Draft -->
            <div class="w-full md:w-1/4">
                <div class="bg-white p-4 rounded-2xl shadow-sm border border-gray-100 flex flex-col h-auto max-h-[300px] md:max-h-none md:h-[calc(100vh-140px)]">
                    <div class="flex justify-between items-center border-b pb-2 mb-4 shrink-0">
                        <h3 class="text-lg font-bold text-gray-800">Riwayat Draft</h3>
                        <button @click="resetForm" class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded hover:bg-blue-200">Baru</button>
                    </div>
                    
                    <div class="flex-grow overflow-y-auto pr-1">
                        <div v-if="pending_transactions.length === 0" class="text-center text-sm text-gray-400 mt-10">
                            Tidak ada draft tersimpan.
                        </div>
                        <div v-else class="space-y-3">
                            <div v-for="draft in pending_transactions" :key="draft.id" 
                                @click="loadDraft(draft)"
                                class="border p-3 rounded-lg cursor-pointer transition-colors"
                                :class="form.transaction_id === draft.id ? 'border-primary bg-red-50' : 'border-gray-200 hover:border-blue-300 hover:bg-gray-50'">
                                <div class="font-bold text-sm">#TRX-{{ String(draft.id).padStart(6, '0') }}</div>
                                <div class="text-xs text-gray-600 mt-1 flex justify-between">
                                    <span>{{ draft.customer?.name || 'Umum' }}</span>
                                    <span class="font-medium text-black">{{ draft.motorcycle?.plate_number || '-' }}</span>
                                </div>
                                <div class="text-xs text-gray-400 mt-2">
                                    {{ new Date(draft.updated_at).toLocaleString('id-ID') }}
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Form Area -->
            <div class="w-full md:w-3/4">
                <!-- Validation Errors (Custom Display) -->
                <div v-if="Object.keys(form.errors).length > 0" class="bg-red-50 border border-red-200 text-red-600 p-4 rounded-xl mb-6">
                    <p class="font-bold mb-2">Terjadi Kesalahan:</p>
                    <ul class="list-disc pl-5">
                        <li v-for="(error, key) in form.errors" :key="key">{{ error }}</li>
                    </ul>
                </div>

                <form class="grid grid-cols-1 md:grid-cols-2 gap-6 h-auto md:h-[calc(100vh-140px)]">
                    
                    <!-- Form Customer & Motor -->
                    <div class="md:col-span-1 space-y-6 overflow-y-visible md:overflow-y-auto pr-1 pb-4">
                        <div class="bg-blue-50 border border-blue-200 p-3 rounded-lg flex items-center justify-between" v-if="form.transaction_id">
                            <span class="text-sm font-semibold text-blue-800">Mengedit Draft: #TRX-{{ String(form.transaction_id).padStart(6, '0') }}</span>
                            <button type="button" @click="resetForm" class="text-xs text-red-600 hover:underline">Batalkan Edit</button>
                        </div>
                        
                        <!-- Customer -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Data Pelanggan</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Pilih Pelanggan</label>
                                    <select v-model="customerSelection" @change="selectCustomer" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm">
                                        <option value="NEW">+ Pelanggan Baru (Quick Add)</option>
                                        <option v-for="c in customers" :key="c.id" :value="c.id">{{ c.name }} - {{ c.phone }}</option>
                                    </select>
                                </div>
                                <template v-if="isNewCustomer">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Nama Pelanggan (Baru)</label>
                                        <input v-model="form.customer_name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">No HP Pelanggan</label>
                                        <input v-model="form.customer_phone" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm">
                                    </div>
                                </template>
                            </div>
                        </div>

                        <!-- Motorcycle -->
                        <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2">Data Motor</h3>
                            <div class="space-y-4">
                                <div v-if="!isNewCustomer">
                                    <label class="block text-sm font-medium text-gray-700">Pilih Motor (Milik Pelanggan Ini)</label>
                                    <select v-model="motorcycleSelection" @change="selectMotorcycle" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm">
                                        <option value="NEW">+ Motor Baru (Quick Add)</option>
                                        <option v-for="m in availableMotorcycles" :key="m.id" :value="m.id">{{ m.plate_number }} ({{ m.type }})</option>
                                    </select>
                                </div>
                                <template v-if="isNewMotorcycle">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Plat Nomor Motor (Baru)</label>
                                        <input v-model="form.motorcycle_plate" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required>
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Tipe Motor (Merek/Model)</label>
                                        <input v-model="form.motorcycle_type" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required>
                                    </div>
                                </template>
                            </div>
                        </div>
                    </div>

                    <!-- Transaksi Sparepart & Jasa -->
                    <div class="md:col-span-1 space-y-6">
                        <div class="bg-white p-4 md:p-6 rounded-2xl shadow-sm border border-gray-100 h-full flex flex-col">
                            <h3 class="text-lg font-bold text-gray-800 mb-4 border-b pb-2 shrink-0">Detail Servis & Sparepart</h3>
                            
                            <div class="flex-grow overflow-y-visible md:overflow-y-auto pr-1">
                                
                                <!-- Jasa Servis -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Tambah Jasa Servis</label>
                                    <select @change="addService" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm bg-blue-50">
                                        <option value="">-- Pilih Jasa Servis --</option>
                                        <option value="NEW">+ Jasa Servis Baru (Quick Add)</option>
                                        <option v-for="svc in services" :key="svc.id" :value="svc.id">
                                            {{ svc.name }} - {{ formatRp(svc.price) }}
                                        </option>
                                    </select>
                                </div>

                                <div class="overflow-x-auto mb-6 border border-gray-200 rounded-lg" v-if="form.services.length > 0">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-blue-50 border-b">
                                            <tr>
                                                <th class="p-2">Jasa Servis</th>
                                                <th class="p-2 text-right">Biaya</th>
                                                <th class="p-2 text-center w-8"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(svc, index) in form.services" :key="index" class="border-b">
                                                <td class="p-2 font-medium">{{ svc.name }}</td>
                                                <td class="p-2 text-right font-medium">{{ formatRp(svc.price) }}</td>
                                                <td class="p-2 text-center">
                                                    <button type="button" @click="removeService(index)" class="text-red-500 hover:text-red-700">✖</button>
                                                </td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>

                                <!-- Spareparts -->
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Tambah Sparepart</label>
                                    <select @change="addSparepart" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm bg-gray-50">
                                        <option value="">-- Pilih Sparepart --</option>
                                        <option value="NEW">+ Sparepart Baru (Quick Add)</option>
                                        <option v-for="sp in spareparts" :key="sp.id" :value="sp.id">
                                            {{ sp.name }} - {{ formatRp(sp.price) }} (Stok: {{ sp.stock }})
                                        </option>
                                    </select>
                                </div>

                                <div class="overflow-x-auto mb-4 border border-gray-200 rounded-lg">
                                    <table class="w-full text-left text-sm">
                                        <thead class="bg-gray-50 border-b">
                                            <tr>
                                                <th class="p-2">Item Sparepart</th>
                                                <th class="p-2 text-center w-20">Qty</th>
                                                <th class="p-2 text-right">Harga</th>
                                                <th class="p-2 text-right">Subtotal</th>
                                                <th class="p-2 text-center w-8"></th>
                                            </tr>
                                        </thead>
                                        <tbody>
                                            <tr v-for="(item, index) in form.items" :key="index" class="border-b">
                                                <td class="p-2 font-medium">{{ item.name }}</td>
                                                <td class="p-2">
                                                    <input type="number" v-model="item.qty" min="1" :max="item.max_stock" class="w-full text-center border-gray-300 rounded p-1 text-sm">
                                                </td>
                                                <td class="p-2 text-right text-gray-500">{{ formatRp(item.price) }}</td>
                                                <td class="p-2 text-right font-medium">{{ formatRp(item.price * item.qty) }}</td>
                                                <td class="p-2 text-center">
                                                    <button type="button" @click="removeItem(index)" class="text-red-500 hover:text-red-700">✖</button>
                                                </td>
                                            </tr>
                                            <tr v-if="form.items.length === 0">
                                                <td colspan="5" class="p-4 text-center text-gray-400">Tidak ada sparepart ditambahkan.</td>
                                            </tr>
                                        </tbody>
                                    </table>
                                </div>
                                
                                <div class="mb-4">
                                    <label class="block text-sm font-medium text-gray-700">Catatan Teknisi (Opsional)</label>
                                    <textarea v-model="form.notes" rows="2" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm"></textarea>
                                </div>
                            </div>
                            
                            <!-- Totals & Actions -->
                            <div class="border-t pt-4 mt-auto">
                                <div class="flex justify-between text-gray-600 mb-1 text-sm">
                                    <span>Subtotal Jasa Servis:</span>
                                    <span>{{ formatRp(totalService) }}</span>
                                </div>
                                <div class="flex justify-between text-gray-600 mb-1 text-sm">
                                    <span>Subtotal Sparepart:</span>
                                    <span>{{ formatRp(totalSparepart) }}</span>
                                </div>
                                <div class="flex items-center justify-between text-red-600 mb-2 font-medium text-sm">
                                    <span>Diskon / Potongan Harga (Rp):</span>
                                    <input type="number" v-model="form.discount" min="0" class="w-1/3 text-right rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm h-8">
                                </div>
                                <div class="flex justify-between text-lg font-bold text-gray-900 mt-2 bg-gray-50 p-3 rounded-lg">
                                    <span>GRAND TOTAL:</span>
                                    <span>{{ formatRp(grandTotal) }}</span>
                                </div>
                                
                                <div class="mt-4 flex flex-col gap-2">
                                    <button type="button" @click="submit('draft')" :disabled="form.processing" class="w-full flex justify-center py-2 px-4 border border-blue-600 rounded-lg shadow-sm text-sm font-medium text-blue-600 bg-white hover:bg-blue-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors disabled:opacity-50">
                                        Simpan Saja (Draft)
                                    </button>
                                    <button type="button" @click="submit('complete')" :disabled="form.processing" class="w-full flex justify-center py-3 px-4 border border-transparent rounded-lg shadow-sm text-base font-medium text-white bg-primary hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors disabled:opacity-50">
                                        Simpan & Cetak Struk
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                </form>
            </div>
        </div>

        <!-- MODAL QUICK ADD SPAREPART -->
        <div v-if="showSparepartModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showSparepartModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitNewSparepart">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Add: Sparepart Baru</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Sparepart</label>
                                    <input v-model="newSparepart.name" type="text" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required>
                                </div>
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Stok Akhir</label>
                                        <input v-model="newSparepart.stock" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required min="1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Harga Jual (Rp)</label>
                                        <input v-model="newSparepart.price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required min="0">
                                    </div>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Harga Beli / Kulakan (Opsional)</label>
                                    <input v-model="newSparepart.buy_price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" min="0">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-primary text-base font-medium text-white hover:bg-red-600 sm:ml-3 sm:w-auto sm:text-sm">
                                Tambahkan & Masukkan Keranjang
                            </button>
                            <button type="button" @click="showSparepartModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL QUICK RESTOCK -->
        <div v-if="showRestockModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showRestockModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitRestock">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Restock: {{ restockData.name }}</h3>
                            <p class="text-sm text-gray-500 mb-4">Stok sparepart ini sedang habis (0). Silakan masukkan jumlah stok baru yang dibeli (Kulakan).</p>
                            <div class="space-y-4">
                                <div class="grid grid-cols-2 gap-4">
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Jumlah Stok Dibeli</label>
                                        <input v-model="restockData.qty" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required min="1">
                                    </div>
                                    <div>
                                        <label class="block text-sm font-medium text-gray-700">Total Harga Kulakan (Rp)</label>
                                        <input v-model="restockData.buy_price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required min="0">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-yellow-500 text-base font-medium text-white hover:bg-yellow-600 sm:ml-3 sm:w-auto sm:text-sm">
                                Simpan Kulakan & Masukkan Keranjang
                            </button>
                            <button type="button" @click="showRestockModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- MODAL QUICK ADD SERVICE -->
        <div v-if="showServiceModal" class="fixed inset-0 z-50 overflow-y-auto" aria-labelledby="modal-title" role="dialog" aria-modal="true">
            <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
                <div class="fixed inset-0 bg-gray-500 bg-opacity-75 transition-opacity" @click="showServiceModal = false"></div>
                <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
                <div class="inline-block align-bottom bg-white rounded-2xl text-left overflow-hidden shadow-xl transform transition-all sm:my-8 sm:align-middle sm:max-w-lg sm:w-full">
                    <form @submit.prevent="submitNewService">
                        <div class="bg-white px-4 pt-5 pb-4 sm:p-6 sm:pb-4">
                            <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Quick Add: Jasa Servis Baru</h3>
                            <div class="space-y-4">
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Nama Jasa Servis</label>
                                    <input v-model="newService.name" type="text" placeholder="Contoh: Turun Mesin Vario" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required>
                                </div>
                                <div>
                                    <label class="block text-sm font-medium text-gray-700">Biaya Jasa (Rp)</label>
                                    <input v-model="newService.price" type="number" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary sm:text-sm" required min="0">
                                </div>
                            </div>
                        </div>
                        <div class="bg-gray-50 px-4 py-3 sm:px-6 sm:flex sm:flex-row-reverse">
                            <button type="submit" class="w-full inline-flex justify-center rounded-md border border-transparent shadow-sm px-4 py-2 bg-blue-600 text-base font-medium text-white hover:bg-blue-700 sm:ml-3 sm:w-auto sm:text-sm">
                                Tambahkan & Masukkan Keranjang
                            </button>
                            <button type="button" @click="showServiceModal = false" class="mt-3 w-full inline-flex justify-center rounded-md border border-gray-300 shadow-sm px-4 py-2 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm">
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </AuthenticatedLayout>
</template>
