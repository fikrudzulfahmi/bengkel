<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import Card from '@/Components/Card.vue';
import { Head } from '@inertiajs/vue3';
</script>

<template>
    <Head title="Dashboard" />

    <AuthenticatedLayout>
        <template #header>
            <h2
                class="text-xl font-semibold leading-tight text-gray-800 dark:text-gray-200"
            >
                Dashboard
            </h2>
        </template>

        <div class="p-6 md:p-8 space-y-6">
            <div class="mx-auto max-w-7xl space-y-6">
                <!-- Welcome Banner -->
                <div class="bg-gradient-to-r from-primary to-secondary rounded-2xl p-6 sm:p-10 text-white shadow-soft relative overflow-hidden">
                    <div class="relative z-10">
                        <h3 class="text-2xl sm:text-3xl font-bold mb-2">Selamat Datang, {{ $page.props.auth.user.name }}! 👋</h3>
                        <p class="text-primary-light text-sm sm:text-base max-w-xl">Semoga harimu menyenangkan. Pantau terus aktivitas bengkel hari ini.</p>
                    </div>
                    <!-- Decorative circles -->
                    <div class="absolute top-0 right-0 -mr-8 -mt-8 w-48 h-48 rounded-full bg-white opacity-10 blur-2xl"></div>
                    <div class="absolute bottom-0 right-1/4 w-32 h-32 rounded-full bg-white opacity-10 blur-xl"></div>
                </div>

                <!-- Stats Grid -->
                <div class="grid grid-cols-1 md:grid-cols-3 gap-6">
                    <Card>
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-primary/10 text-primary-dark">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 7h8m0 0v8m0-8l-8 8-4-4-6 6"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Total Transaksi Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-800">12</p>
                            </div>
                        </div>
                    </Card>
                    
                    <Card>
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-blue-50 text-blue-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Pendapatan Hari Ini</p>
                                <p class="text-2xl font-bold text-gray-800">Rp 1.250.000</p>
                            </div>
                        </div>
                    </Card>

                    <Card>
                        <div class="flex items-center">
                            <div class="p-3 rounded-xl bg-amber-50 text-amber-600">
                                <svg class="w-8 h-8" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                            </div>
                            <div class="ml-4">
                                <p class="text-sm font-medium text-gray-500">Suku Cadang Menipis</p>
                                <p class="text-2xl font-bold text-gray-800">5 Item</p>
                            </div>
                        </div>
                    </Card>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
