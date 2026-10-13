<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';

const props = defineProps({
    journals: Array,
    todayDate: String
});

const form = useForm({
    activity: '',
    date: props.todayDate
});

const submit = () => {
    form.post(route('mechanic.journals.store'), {
        onSuccess: () => form.reset('activity'),
    });
};
</script>

<template>
    <Head title="Jurnal Kegiatan" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Jurnal Kegiatan Harian (Absensi)</h2>
        </template>

        <div class="p-6 md:p-8">
            <div class="max-w-4xl mx-auto  space-y-8">
                
                <!-- Input Jurnal -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-pink-50">
                        <h3 class="text-lg font-bold text-gray-800">Tulis Kegiatan Hari Ini</h3>
                        <p class="text-sm text-gray-500 mt-1">Sebagai pengganti presensi, tuliskan kegiatan / pekerjaan Anda hari ini.</p>
                    </div>
                    <form @submit.prevent="submit" class="p-6 space-y-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Tanggal</label>
                            <input v-model="form.date" type="date" class="mt-1 block w-1/3 rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Aktivitas / Kegiatan</label>
                            <textarea v-model="form.activity" rows="4" placeholder="Contoh: Datang jam 08:00. Membersihkan area bengkel. Servis motor vario..." class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-primary focus:ring-primary sm:text-sm" required></textarea>
                            <div v-if="form.errors.activity" class="text-red-500 text-xs mt-1">{{ form.errors.activity }}</div>
                        </div>
                        <div class="flex justify-end mt-4">
                            <button type="submit" :disabled="form.processing" class="inline-flex justify-center rounded-md border border-transparent shadow-sm px-6 py-2 bg-primary text-base font-medium text-white hover:bg-red-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary sm:text-sm disabled:opacity-50">
                                Simpan Jurnal
                            </button>
                        </div>
                    </form>
                </div>

                <!-- Riwayat Jurnal Bulan Ini -->
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-2xl border border-gray-100">
                    <div class="p-6 border-b border-gray-100 bg-gray-50">
                        <h3 class="text-lg font-bold text-gray-800">Riwayat Jurnal Bulan Ini</h3>
                    </div>
                    <div class="p-6">
                        <div v-if="journals.length === 0" class="text-center text-gray-500 py-8">
                            Belum ada jurnal yang diisi bulan ini.
                        </div>
                        <div v-else class="space-y-4">
                            <div v-for="journal in journals" :key="journal.id" class="border-l-4 border-primary pl-4 py-3 bg-gray-50 rounded-r-lg">
                                <div class="font-bold text-gray-800 text-sm mb-1">{{ new Date(journal.date).toLocaleDateString('id-ID', { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' }) }}</div>
                                <div class="text-gray-700 text-sm whitespace-pre-line">{{ journal.activity }}</div>
                            </div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </AuthenticatedLayout>
</template>
