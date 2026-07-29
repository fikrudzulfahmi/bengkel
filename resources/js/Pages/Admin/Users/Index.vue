<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import Card from '@/Components/Card.vue';
import Badge from '@/Components/Badge.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';

const props = defineProps({
    users: Array,
});

const form = useForm({
    id: '',
    name: '',
    email: '',
    password: '',
    role: 'mekanik',
});

const isEditing = ref(false);
const showModal = ref(false);

const openCreateModal = () => {
    form.reset();
    form.clearErrors();
    isEditing.value = false;
    showModal.value = true;
};

const openEditModal = (user) => {
    form.reset();
    form.clearErrors();
    form.id = user.id;
    form.name = user.name;
    form.email = user.email;
    form.role = user.role;
    form.password = ''; // Kosongkan password saat edit, opsional diisi
    isEditing.value = true;
    showModal.value = true;
};

const submit = () => {
    if (isEditing.value) {
        form.put(route('admin.users.update', form.id), {
            onSuccess: () => showModal.value = false,
        });
    } else {
        form.post(route('admin.users.store'), {
            onSuccess: () => showModal.value = false,
        });
    }
};

const deleteUser = (id) => {
    if (confirm('Apakah Anda yakin ingin menghapus user ini?')) {
        useForm({}).delete(route('admin.users.destroy', id));
    }
};
</script>

<template>
    <Head title="Kelola Mekanik / User" />

    <AuthenticatedLayout>
        <template #header>
            <div class="flex justify-between items-center">
                <h2 class="font-semibold text-xl text-gray-800 leading-tight">Kelola Mekanik / User</h2>
                <PrimaryButton @click="openCreateModal">
                    + Tambah User
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
                                    <th class="p-5 font-medium">Nama</th>
                                    <th class="p-5 font-medium">Email</th>
                                    <th class="p-5 font-medium">Role</th>
                                    <th class="p-5 font-medium text-right">Aksi</th>
                                </tr>
                            </thead>
                            <tbody class="divide-y divide-gray-50">
                                <tr v-for="user in users" :key="user.id" class="hover:bg-gray-50/50 transition-colors">
                                    <td class="p-5 text-gray-800 font-medium">{{ user.name }}</td>
                                    <td class="p-5 text-gray-500 text-sm">{{ user.email }}</td>
                                    <td class="p-5">
                                        <Badge :type="user.role === 'admin' ? 'primary' : user.role === 'bendahara' ? 'warning' : 'info'">
                                            {{ user.role }}
                                        </Badge>
                                    </td>
                                    <td class="p-5 text-right space-x-2">
                                        <button @click="openEditModal(user)" class="text-blue-500 hover:text-blue-700 font-medium text-sm transition-colors p-2 hover:bg-blue-50 rounded-lg">Edit</button>
                                        <button @click="deleteUser(user.id)" class="text-red-500 hover:text-red-700 font-medium text-sm transition-colors p-2 hover:bg-red-50 rounded-lg">Hapus</button>
                                    </td>
                                </tr>
                                <tr v-if="users.length === 0">
                                    <td colspan="4" class="p-10 text-center text-gray-500">
                                        <div class="flex flex-col items-center justify-center">
                                            <svg class="w-12 h-12 text-gray-300 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197M13 7a4 4 0 11-8 0 4 4 0 018 0z"></path></svg>
                                            <span class="text-sm">Belum ada data user/mekanik.</span>
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
                                        {{ isEditing ? 'Edit User' : 'Tambah User Baru' }}
                                    </h3>
                                    
                                    <div class="space-y-5">
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Nama</label>
                                            <input v-model="form.name" type="text" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required>
                                            <div v-if="form.errors.name" class="text-red-500 text-xs mt-1.5">{{ form.errors.name }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Email</label>
                                            <input v-model="form.email" type="email" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" required>
                                            <div v-if="form.errors.email" class="text-red-500 text-xs mt-1.5">{{ form.errors.email }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">Role</label>
                                            <select v-model="form.role" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors">
                                                <option value="admin">Admin</option>
                                                <option value="mekanik">Mekanik</option>
                                                <option value="bendahara">Bendahara</option>
                                            </select>
                                            <div v-if="form.errors.role" class="text-red-500 text-xs mt-1.5">{{ form.errors.role }}</div>
                                        </div>
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700">
                                                Password <span v-if="isEditing" class="text-gray-400 font-normal ml-1">(Kosongkan jika tidak diubah)</span>
                                            </label>
                                            <input v-model="form.password" type="password" class="mt-1.5 block w-full rounded-lg border-gray-200 shadow-sm focus:border-primary focus:ring-primary sm:text-sm transition-colors" :required="!isEditing">
                                            <div v-if="form.errors.password" class="text-red-500 text-xs mt-1.5">{{ form.errors.password }}</div>
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

            </div>
        </div>
    </AuthenticatedLayout>
</template>
