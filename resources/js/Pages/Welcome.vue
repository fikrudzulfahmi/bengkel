<script setup>
import { Head, Link } from '@inertiajs/vue3';

defineProps({
    canLogin: {
        type: Boolean,
    },
    canRegister: {
        type: Boolean,
    },
});
</script>

<template>
    <Head title="Smekisa Motor Care - E-Bengkel" />

    <div class="min-h-screen bg-surface font-sans text-gray-800">
        <!-- Navbar -->
        <nav class="bg-white shadow-md">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="flex justify-between h-16">
                    <div class="flex items-center">
                        <svg class="w-8 h-8 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path></svg>
                        <span class="font-bold text-xl text-primary tracking-wider">E-Bengkel</span>
                    </div>
                    <div class="flex items-center space-x-4" v-if="canLogin">
                        <Link v-if="$page.props.auth.user" :href="route('dashboard')" class="text-gray-600 hover:text-primary font-medium transition">Dashboard</Link>
                        <template v-else>
                            <Link :href="route('login')" class="text-gray-600 hover:text-primary font-medium transition">Masuk</Link>
                            <Link v-if="canRegister" :href="route('register')" class="bg-primary hover:bg-red-600 text-white px-4 py-2 rounded-full font-medium transition shadow-lg shadow-red-500/30">Daftar</Link>
                        </template>
                    </div>
                </div>
            </div>
        </nav>

        <!-- Hero Section -->
        <main class="relative bg-gradient-to-br from-primary to-secondary text-white overflow-hidden">
            <!-- Decorative circle -->
            <div class="absolute top-0 right-0 -mr-20 -mt-20 w-96 h-96 rounded-full bg-white opacity-10 blur-3xl"></div>
            <div class="absolute bottom-0 left-0 -ml-20 -mb-20 w-80 h-80 rounded-full bg-black opacity-10 blur-2xl"></div>

            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-24 lg:py-32 relative z-10 flex flex-col md:flex-row items-center">
                <div class="md:w-1/2 text-center md:text-left mb-12 md:mb-0">
                    <h1 class="text-4xl sm:text-5xl lg:text-6xl font-extrabold tracking-tight mb-6 leading-tight">
                        Smekisa <br class="hidden md:block" /> Motor Care
                    </h1>
                    <p class="text-lg sm:text-xl text-red-50 mb-8 max-w-lg mx-auto md:mx-0 font-light">
                        Bengkel unit produksi jasa departemen Teknik Sepeda Motor di SMKS Islam 1 Kota Blitar. Layanan servis motor berkualitas, cepat, dan transparan.
                    </p>
                    <div class="flex justify-center md:justify-start space-x-4">
                        <Link v-if="canLogin" :href="route('login')" class="bg-white text-primary hover:bg-gray-50 px-8 py-3 rounded-full font-semibold text-lg shadow-lg transition transform hover:-translate-y-1">
                            Mulai Sistem
                        </Link>
                    </div>
                </div>
                <div class="md:w-1/2 flex justify-center">
                    <div class="relative w-72 h-72 sm:w-96 sm:h-96">
                        <div class="absolute inset-0 bg-white opacity-20 rounded-full animate-pulse"></div>
                        <div class="absolute inset-4 bg-white opacity-40 rounded-full"></div>
                        <div class="absolute inset-8 bg-white rounded-full flex items-center justify-center shadow-2xl">
                            <svg class="w-32 h-32 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path></svg>
                        </div>
                    </div>
                </div>
            </div>
        </main>

        <!-- Features Section -->
        <section class="py-20 bg-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <div class="text-center mb-16">
                    <h2 class="text-3xl font-bold text-gray-900 mb-4">Layanan Unggulan Kami</h2>
                    <p class="text-gray-500 max-w-2xl mx-auto">Kami memberikan pelayanan terbaik untuk merawat dan memperbaiki sepeda motor kesayangan Anda.</p>
                </div>
                
                <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                    <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 13.255A23.931 23.931 0 0112 15c-3.183 0-6.22-.62-9-1.745M16 6V4a2 2 0 00-2-2h-4a2 2 0 00-2 2v2m4 6h.01M5 20h14a2 2 0 002-2V8a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Servis Berkala</h3>
                        <p class="text-gray-600">Perawatan rutin untuk menjaga performa mesin agar selalu dalam kondisi prima saat digunakan.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-14 h-14 bg-pink-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-secondary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Pengerjaan Cepat</h3>
                        <p class="text-gray-600">Ditangani oleh mekanik terampil dan peralatan memadai untuk waktu servis yang efisien.</p>
                    </div>
                    
                    <div class="bg-gray-50 p-8 rounded-2xl border border-gray-100 hover:shadow-xl transition-shadow duration-300">
                        <div class="w-14 h-14 bg-red-100 rounded-xl flex items-center justify-center mb-6">
                            <svg class="w-7 h-7 text-primary" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path></svg>
                        </div>
                        <h3 class="text-xl font-semibold mb-3">Suku Cadang Asli</h3>
                        <p class="text-gray-600">Kami menyediakan dan menggunakan sparepart original yang terjamin kualitas dan keamanannya.</p>
                    </div>
                </div>
            </div>
        </section>

        <!-- Footer -->
        <footer class="bg-gray-900 text-white py-12">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 flex flex-col md:flex-row justify-between items-center">
                <div class="mb-6 md:mb-0 text-center md:text-left">
                    <span class="text-2xl font-bold text-white tracking-wider flex items-center justify-center md:justify-start">
                        <svg class="w-6 h-6 text-primary mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path></svg>
                        E-Bengkel
                    </span>
                    <p class="text-gray-400 mt-2 text-sm">Smekisa Motor Care - SMKS Islam 1 Kota Blitar</p>
                </div>
                <div class="text-gray-400 text-sm text-center md:text-right">
                    &copy; {{ new Date().getFullYear() }} E-Bengkel. All rights reserved.
                </div>
            </div>
        </footer>
    </div>
</template>
