<script setup>
import { ref } from 'vue';
import ApplicationLogo from '@/Components/ApplicationLogo.vue';
import Dropdown from '@/Components/Dropdown.vue';
import DropdownLink from '@/Components/DropdownLink.vue';
import ResponsiveNavLink from '@/Components/ResponsiveNavLink.vue';
import { Link, usePage } from '@inertiajs/vue3';

const showingNavigationDropdown = ref(false);
const page = usePage();

const navLinks = [
    { label: 'Dashboard', route: 'dashboard', active: 'dashboard', roles: ['admin', 'mekanik'] },
    
    // Admin Links
    { label: 'Mekanik / User', route: 'admin.users.index', active: 'admin.users.*', roles: ['admin'] },
    { label: 'Laporan Keuangan', route: 'admin.reports.index', active: 'admin.reports.*', roles: ['admin'] },
    { label: 'Jurnal & Log', route: 'admin.journals.index', active: 'admin.journals.*', roles: ['admin'] },
    
    // Mechanic Links
    { label: 'Kasir / Servis', route: 'mechanic.transactions.create', active: 'mechanic.transactions.create', roles: ['mekanik'] },
    { label: 'Riwayat Transaksi', route: 'mechanic.transactions.index', active: 'mechanic.transactions.index', roles: ['mekanik'] },
    { label: 'Kelola Sparepart', route: 'mechanic.spareparts.index', active: 'mechanic.spareparts.*', roles: ['mekanik'] },
    { label: 'Buku Kas', route: 'mechanic.cash-books.index', active: 'mechanic.cash-books.*', roles: ['mekanik'] },
    { label: 'Jurnal Harian', route: 'mechanic.journals.index', active: 'mechanic.journals.*', roles: ['mekanik'] },
];

const hasRole = (roles) => {
    return roles.includes(page.props.auth.user.role);
};
</script>

<template>
    <div class="min-h-screen bg-background flex flex-col md:flex-row font-sans text-gray-800">
        
        <!-- Mobile Header & Hamburger -->
        <div class="md:hidden flex items-center justify-between bg-white border-b border-gray-200 px-4 py-3">
            <Link :href="route('dashboard')" class="flex items-center gap-2">
                <div class="w-8 h-8 rounded-full bg-primary text-white flex items-center justify-center font-bold">E</div>
                <span class="font-bold text-lg text-gray-900 tracking-tight">E-Bengkel</span>
            </Link>
            <button @click="showingNavigationDropdown = !showingNavigationDropdown" class="text-gray-500 hover:text-primary focus:outline-none">
                <svg class="w-6 h-6" stroke="currentColor" fill="none" viewBox="0 0 24 24">
                    <path :class="{ hidden: showingNavigationDropdown, 'inline-flex': !showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16" />
                    <path :class="{ hidden: !showingNavigationDropdown, 'inline-flex': showingNavigationDropdown }" stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12" />
                </svg>
            </button>
        </div>

        <!-- Mobile Navigation Menu -->
        <div :class="{ block: showingNavigationDropdown, hidden: !showingNavigationDropdown }" class="md:hidden bg-white border-b border-gray-200">
            <div class="pt-2 pb-3 space-y-1">
                <template v-for="link in navLinks" :key="link.route">
                    <ResponsiveNavLink v-if="hasRole(link.roles)" :href="route(link.route)" :active="route().current(link.active)">
                        {{ link.label }}
                    </ResponsiveNavLink>
                </template>
            </div>
            <div class="pt-4 pb-1 border-t border-gray-200">
                <div class="px-4">
                    <div class="font-medium text-base text-gray-800">{{ $page.props.auth.user.name }}</div>
                    <div class="font-medium text-sm text-gray-500 capitalize">{{ $page.props.auth.user.role }}</div>
                </div>
                <div class="mt-3 space-y-1">
                    <ResponsiveNavLink :href="route('profile.edit')">Profile</ResponsiveNavLink>
                    <ResponsiveNavLink :href="route('logout')" method="post" as="button">Log Out</ResponsiveNavLink>
                </div>
            </div>
        </div>

        <!-- Desktop Sidebar -->
        <aside class="hidden md:flex flex-col w-64 bg-white border-r border-gray-100 flex-shrink-0 fixed h-full z-10 shadow-sm">
            <div class="h-16 flex items-center justify-center border-b border-gray-50 px-6 gap-3">
                <div class="w-8 h-8 rounded-full bg-gradient-to-br from-primary to-secondary text-white flex items-center justify-center font-bold shadow-soft">
                    E
                </div>
                <span class="font-bold text-xl text-gray-900 tracking-tight">E-Bengkel</span>
            </div>
            
            <nav class="flex-1 px-4 py-6 space-y-1 overflow-y-auto">
                <template v-for="link in navLinks" :key="link.route">
                    <Link 
                        v-if="hasRole(link.roles)"
                        :href="route(link.route)" 
                        :class="[
                            route().current(link.active) 
                                ? 'bg-primary/10 text-primary-dark font-semibold border-r-4 border-primary' 
                                : 'text-gray-500 hover:bg-primary/5 hover:text-primary font-medium',
                            'block px-4 py-3 rounded-l-lg transition-all duration-200 ease-in-out'
                        ]"
                    >
                        {{ link.label }}
                    </Link>
                </template>
            </nav>
            
        </aside>

        <!-- Main Content -->
        <main class="flex-1 md:ml-64 flex flex-col min-h-screen">
            <!-- Page Header -->
            <header class="bg-white/80 backdrop-blur-md border-b border-gray-100 sticky top-0 z-20 shadow-sm transition-all duration-300">
                <div class="max-w-7xl mx-auto py-3 px-4 sm:px-6 lg:px-8 flex justify-between items-center min-h-[64px]">
                    
                    <div class="flex-1 mr-4" v-if="$slots.header">
                        <slot name="header" />
                    </div>
                    <div class="flex-1" v-else></div>

                    <!-- User Dropdown (Top Right) -->
                    <div class="hidden md:flex items-center">
                        <Dropdown align="right" width="48">
                            <template #trigger>
                                <button class="flex items-center text-sm font-medium text-gray-600 hover:text-primary-dark transition-colors focus:outline-none bg-white border border-gray-200 px-3 py-1.5 rounded-full shadow-sm hover:shadow-soft">
                                    <div class="w-8 h-8 bg-primary-light/20 text-primary-dark rounded-full flex items-center justify-center font-bold mr-2">
                                        {{ $page.props.auth.user.name.charAt(0).toUpperCase() }}
                                    </div>
                                    <div class="text-left hidden lg:block mr-2">
                                        <div class="text-gray-800 font-bold leading-tight">{{ $page.props.auth.user.name }}</div>
                                        <div class="text-[10px] text-gray-500 capitalize leading-tight">{{ $page.props.auth.user.role }}</div>
                                    </div>
                                    <svg class="fill-current h-4 w-4 text-gray-400" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M5.293 7.293a1 1 0 011.414 0L10 10.586l3.293-3.293a1 1 0 111.414 1.414l-4 4a1 1 0 01-1.414 0l-4-4a1 1 0 010-1.414z" clip-rule="evenodd" />
                                    </svg>
                                </button>
                            </template>
                            <template #content>
                                <div class="px-4 py-3 border-b border-gray-100">
                                    <p class="text-sm font-semibold text-gray-900">{{ $page.props.auth.user.name }}</p>
                                    <p class="text-xs text-gray-500 capitalize">{{ $page.props.auth.user.role }}</p>
                                </div>
                                <DropdownLink :href="route('profile.edit')">Profile</DropdownLink>
                                <DropdownLink :href="route('logout')" method="post" as="button" class="text-red-600 hover:text-red-700">Log Out</DropdownLink>
                            </template>
                        </Dropdown>
                    </div>
                </div>
            </header>

            <!-- Page Content -->
            <div class="flex-1">
                <slot />
            </div>
        </main>
        
    </div>
</template>
