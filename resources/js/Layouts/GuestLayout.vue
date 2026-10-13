<script setup>
import { Link } from '@inertiajs/vue3';
</script>

<template>
    <div class="min-h-screen flex bg-gray-50">
        <!-- Left Side: Branding/Image -->
        <div class="hidden lg:flex lg:w-1/2 bg-gradient-to-br from-teal-600 to-emerald-700 p-12 flex-col justify-between relative overflow-hidden">
            <!-- Decorative circles -->
            <div class="absolute -top-24 -left-24 w-96 h-96 bg-white opacity-10 rounded-full blur-3xl"></div>
            <div class="absolute bottom-12 -right-12 w-64 h-64 bg-teal-300 opacity-20 rounded-full blur-2xl"></div>

            <div class="relative z-10">
                <Link href="/" class="flex items-center gap-3">
                    <div class="w-12 h-12 bg-white rounded-xl flex items-center justify-center shadow-lg">
                        <svg class="w-8 h-8 text-teal-600" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path></svg>
                    </div>
                    <span class="text-2xl font-bold text-white tracking-tight">Smekisa Motor Care</span>
                </Link>
            </div>

            <div class="relative z-10 mb-12">
                <h1 class="text-4xl font-extrabold text-white leading-tight mb-4">
                    Sistem Informasi <br> Manajemen Bengkel
                </h1>
                <p class="text-teal-100 text-lg max-w-md leading-relaxed">
                    Kelola antrean servis, stok sparepart, pembukuan kas, dan operasional harian dengan lebih rapi dan efisien.
                </p>
            </div>

            <div class="relative z-10 text-teal-200 text-sm">
                &copy; {{ new Date().getFullYear() }} Smekisa Motor Care. Hak Cipta Dilindungi.
            </div>
        </div>

        <!-- Right Side: Form -->
        <div class="w-full lg:w-1/2 flex items-center justify-center p-8 sm:p-12">
            <div class="w-full max-w-md">
                <div class="lg:hidden mb-10 text-center flex flex-col items-center">
                    <div class="w-16 h-16 bg-gradient-to-br from-teal-500 to-emerald-600 rounded-2xl flex items-center justify-center shadow-lg mb-4 text-white">
                        <svg class="w-10 h-10" fill="none" stroke="currentColor" viewBox="0 0 24 24"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M11.42 15.17L17.25 21A2.652 2.652 0 0021 17.25l-5.877-5.877M11.42 15.17l2.496-3.03c.317-.384.74-.626 1.208-.766M11.42 15.17l-4.655 5.653a2.548 2.548 0 11-3.586-3.586l6.837-5.63m5.108-.233c.55-.164 1.163-.188 1.743-.14a4.5 4.5 0 004.486-6.336l-3.276 3.277a3.004 3.004 0 01-2.25-2.25l3.276-3.276a4.5 4.5 0 00-6.336 4.486c.091 1.076-.071 2.264-.904 2.95l-.102.085m-1.745 1.437L5.909 7.5H4.5L2.25 3.75l1.5-1.5L7.5 4.5v1.409l4.26 4.26m-1.745 1.437l1.745-1.437m6.615 8.206L15.75 15.75M4.867 19.125h.008v.008h-.008v-.008z"></path></svg>
                    </div>
                    <h2 class="text-2xl font-bold text-gray-900">Smekisa Motor</h2>
                </div>

                <div class="bg-white px-8 py-10 shadow-xl shadow-gray-200/50 sm:rounded-3xl border border-gray-100">
                    <slot />
                </div>
            </div>
        </div>
    </div>
</template>
