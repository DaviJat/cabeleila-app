<script setup>
import { Menubar } from 'primevue';
import { Link } from '@inertiajs/vue3';

const items = [
    {
        label: 'Painel',
        to: 'admin.dashboard',
    },
    {
        label: 'Agenda',
        to: 'admin.appointments',
    },
    {
        label: 'Clientes',
        to: 'admin.clients',
    },
    {
        label: 'Horários',
        to: 'admin.availabilities',
    },
    {
        label: 'Serviços',
        to: 'admin.services',
    },
];
</script>

<template>
    <div class="min-h-screen bg-[#faf8f5]">
        <div class="max-w-7xl mx-auto flex flex-col sm:px-6 lg:px-8 gap-6 py-6">
            <header>
                <Menubar :model="items">
                    <template #start> Start </template>
                    <template #item="{ item }">
                        <Link :href="route(item.to)">{{ item.label }}</Link>
                    </template>
                    <template #end> End </template>
                </Menubar>
            </header>
            <main class="flex-1">
                <slot />
            </main>
        </div>
        <footer class="w-full border-t border-gray-200/50 bg-white/40 backdrop-blur-md mt-auto relative z-10">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-5 flex justify-center items-center">
                <p class="flex items-center justify-center text-sm text-gray-500 font-medium text-center">
                    &copy; {{ new Date().getFullYear() }} Cabeleila. Todos os direitos reservados.
                </p>
            </div>
        </footer>
    </div>
</template>
