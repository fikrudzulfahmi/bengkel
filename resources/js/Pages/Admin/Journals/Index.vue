<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, router } from '@inertiajs/vue3';
import { ref } from 'vue';

const props = defineProps({
    journals: Array,
    activityLogs: Array,
    filters: Object
});

const date = ref(props.filters.date);

const filterData = () => {
    router.get(route('admin.journals.index'), { date: date.value }, { preserveState: true });
};
</script>

<template>
    <Head title="Jurnal & Log Aktivitas" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jurnal Kegiatan & Log Aktivitas Mekanik</h2>
        </template>

        <div class="p-6 md:p-8">
            <div class="max-w-7xl mx-auto  space-y-8">
                
                <!-- Filter -->
                <div class="bg-white p-6 rounded-2xl shadow-sm border border-gray-100 flex items-center space-x-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700 mb-1">Tanggal</label>
                        <input v-model="date" type="date" @change="filterData" class="rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm">
                    </div>
                </div>

                <div class="grid grid-cols-1 md:grid-cols-2 gap-8">
                    <!-- Jurnal Harian -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                        <div class="p-6 border-b border-gray-100 bg-pink-50">
                            <h3 class="text-lg font-bold text-gray-800">Jurnal Kegiatan Harian (Pengganti Absen)</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="journals.length === 0" class="text-center text-gray-500 py-8">
                                Belum ada jurnal yang diisi pada tanggal ini.
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="journal in journals" :key="journal.id" class="border-l-4 border-primary pl-4 py-2 bg-gray-50 rounded-r-lg">
                                    <div class="font-semibold text-gray-800">{{ journal.user?.name }}</div>
                                    <div class="text-gray-600 text-sm mt-1 whitespace-pre-line">{{ journal.activity }}</div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Log Aktivitas -->
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                        <div class="p-6 border-b border-gray-100 bg-gray-50">
                            <h3 class="text-lg font-bold text-gray-800">Log Aktivitas Sistem</h3>
                        </div>
                        <div class="p-6">
                            <div v-if="activityLogs.length === 0" class="text-center text-gray-500 py-8">
                                Belum ada aktivitas tercatat pada tanggal ini.
                            </div>
                            <div v-else class="space-y-4">
                                <div v-for="log in activityLogs" :key="log.id" class="border-b pb-3 last:border-0">
                                    <div class="flex justify-between items-start">
                                        <div>
                                            <span class="font-semibold text-gray-800 text-sm">{{ log.user?.name }}</span>
                                            <span class="text-xs text-gray-400 ml-2">{{ new Date(log.created_at).toLocaleTimeString('id-ID') }}</span>
                                        </div>
                                        <span class="px-2 py-1 bg-gray-100 text-gray-600 rounded text-xs">{{ log.action }}</span>
                                    </div>
                                    <div class="text-gray-600 text-sm mt-1">{{ log.description }}</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
